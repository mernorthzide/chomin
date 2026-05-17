<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Cookie;

class ReferralController extends Controller
{
    public function index(string $locale)
    {
        $user = Auth::user();
        $user->ensureReferralCode();

        $referrals = $user->referrals()
            ->select('id', 'name', 'created_at', 'referral_credited_at')
            ->latest()
            ->paginate(10);

        $creditedCount = $user->referrals()->whereNotNull('referral_credited_at')->count();
        $pendingCount = $user->referrals()->whereNull('referral_credited_at')->count();

        return view('pages.profile.referrals', compact('referrals', 'creditedCount', 'pendingCount'));
    }

    public function capture(string $locale, string $code): RedirectResponse
    {
        $exists = User::where('referral_code', strtoupper($code))->exists();

        $response = redirect()->route('home', ['locale' => $locale]);

        if ($exists) {
            $response->withCookie(new Cookie(
                config('chomin.referral.cookie_name'),
                strtoupper($code),
                now()->addMinutes((int) config('chomin.referral.cookie_ttl_minutes'))->getTimestamp(),
                '/',
                null,
                request()->isSecure(),
                true,
            ));
        }

        return $response;
    }

    public static function applyToNewUser(User $newUser, Request $request): void
    {
        $code = strtoupper((string) $request->cookie(config('chomin.referral.cookie_name'), ''));
        if (! $code) {
            return;
        }

        $referrer = User::where('referral_code', $code)->first();
        if (! $referrer || $referrer->id === $newUser->id) {
            return;
        }

        $newUser->forceFill(['referred_by_user_id' => $referrer->id])->save();
    }

    public static function creditFirstOrder(User $buyer): void
    {
        if (! $buyer->referred_by_user_id || $buyer->referral_credited_at) {
            return;
        }

        $paidOrderCount = $buyer->orders()
            ->whereIn('status', OrderStatus::paidStatuses())
            ->count();

        if ($paidOrderCount !== 1) {
            return;
        }

        $referrer = User::find($buyer->referred_by_user_id);
        if (! $referrer) {
            return;
        }

        $referrerBonus = (int) config('chomin.referral.referrer_bonus_points');
        $refereeBonus = (int) config('chomin.referral.referee_bonus_points');

        DB::transaction(function () use ($buyer, $referrer, $referrerBonus, $refereeBonus) {
            $referrer->increment('points', $referrerBonus);
            $referrer->pointTransactions()->create([
                'points' => $referrerBonus,
                'type' => 'referral',
                'description' => "Referral bonus from {$buyer->email}",
            ]);

            $buyer->increment('points', $refereeBonus);
            $buyer->pointTransactions()->create([
                'points' => $refereeBonus,
                'type' => 'referral',
                'description' => "Referral bonus from {$referrer->email}",
            ]);

            $buyer->forceFill(['referral_credited_at' => now()])->save();
        });
    }
}
