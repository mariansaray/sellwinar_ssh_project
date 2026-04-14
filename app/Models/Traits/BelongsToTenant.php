<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (!$model->tenant_id && app()->bound('current_tenant_id') && $tenantId = app('current_tenant_id')) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}
