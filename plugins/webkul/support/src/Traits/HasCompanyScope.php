<?php

namespace Webkul\Support\Traits;

use Throwable;
use Webkul\Support\Scopes\CompanyScope;

trait HasCompanyScope
{
    public static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            try {
                if (! $model->company_id && ! app()->runningInConsole() && filament()->auth()->check()) {
                    $user = filament()->auth()->user();

                    if ($user && $user->default_company_id) {
                        $model->company_id = $user->default_company_id;
                    }
                }
            } catch (Throwable) {
            }
        });
    }
}
