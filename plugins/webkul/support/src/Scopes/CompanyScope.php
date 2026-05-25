<?php

namespace Webkul\Support\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Throwable;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        try {
            if (! app()->runningInConsole() && filament()->auth()->check()) {
                $user = filament()->auth()->user();

                if ($user && ! $user->is_superadmin && $user->default_company_id) {
                    $builder->where($model->getTable().'.company_id', $user->default_company_id);
                }
            }
        } catch (Throwable) {
        }
    }
}
