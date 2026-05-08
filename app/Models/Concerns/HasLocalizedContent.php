<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasLocalizedContent
{
    public function localized(string $field, ?string $locale = null, mixed $default = null): mixed
    {
        $locale ??= app()->getLocale();
        $translation = $this->translationFor($locale);

        if ($translation && filled($translation->{$field})) {
            return $translation->{$field};
        }

        if ($locale !== config('chomin.locales.default', 'th')) {
            $fallback = $this->translationFor(config('chomin.locales.default', 'th'));
            if ($fallback && filled($fallback->{$field})) {
                return $fallback->{$field};
            }
        }

        return $this->{$field} ?? $default;
    }

    protected function translationFor(string $locale): ?Model
    {
        if (method_exists($this, 'relationLoaded') && $this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale);
        }

        if (method_exists($this, 'translations')) {
            return $this->translations()->where('locale', $locale)->first();
        }

        return null;
    }
}
