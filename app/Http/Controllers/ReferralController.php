<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

class ReferralController extends Controller
{
    public const COOKIE_NAME = 'chomin_referral';

    public const COOKIE_TTL_MINUTES = 60 * 24 * 30;

    public const REFERRER_BONUS_POINTS = 200;

    public const REFEREE_BONUS_POINTS = 100;

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
                self::COOKIE_NAME,
                strtoupper($code),
                now()->addMinutes(self::COOKIE_TTL_MINUTES)->getTimestamp(),
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
        $code = strtoupper((string) $request->cookie(self::COOKIE_NAME, ''));
        if (! $code) {
            return;
        }

        $referrer = User::where('referral_code', $code)->first();
        if (! $referrer || $referrer->id === $newUser->id) {
            return;
        }

        $newUser->update(['referred_by_user_id' => $referrer->id]);
    }

    public static function creditFirstOrder(User $buyer): void
    {
        if (! $buyer->referred_by_user_id || $buyer->referral_credited_at) {
            return;
        }

        $hasPriorOrders = $buyer->orders()
            ->whereIn('status', ['paid', 'shipping', 'completed'])
            ->count() > 1;

        if ($hasPriorOrders) {
            return;
        }

        $referrer = User::find($buyer->referred_by_user_id);
        if (! $referrer) {
            return;
        }

        $referrer->increment('points', self::REFERRER_BONUS_POINTS);
        $referrer->pointTransactions()->create([
            'points' => self::REFERRER_BONUS_POINTS,
            'type' => 'referral',
            'note' => "Referral bonus from {$buyer->email}",
        ]);

        $buyer->increment('points', self::REFEREE_BONUS_POINTS);
        $buyer->pointTransactions()->create([
            'points' => self::REFEREE_BONUS_POINTS,
            'type' => 'referral',
            'note' => "Referral bonus from {$referrer->email}",
        ]);

        $buyer->update(['referral_credited_at' => now()]);
    }
}
