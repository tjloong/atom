<?php

namespace Jiannius\Atom\Traits;

use App\Models\Tenant;

trait MultiTenant
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootMultiTenant()
    {
        static::saving(function ($model) {
            if ($id = request()->user()->tenant_id ?? null) {
                $model->tenant_id = $model->tenant_id ?? $id;
            }
        });
       
        static::addGlobalScope('multitenant', function ($query) {
            if (auth()->hasUser()) {
                $query->accessibleByTenant();
            }
        });
    }

    /**
     * Initialize the trait
     * 
     * @return void
     */
    protected function initializeMultiTenant()
    {
        $this->fillable[] = 'tenant_id';
        $this->casts['tenant_id'] = 'integer';
    }

    /**
     * Get tenant for model
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope for multi tenant
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeAccessibleByTenant($query)
    {
        if (request()->user()->isRole('root')) return $query->with(['tenant' => fn($q) => $q->withoutGlobalScopes()]);
        else if ($tenantid = request()->user()->tenant_id ?? null) {
            return $query->where('tenant_id', $tenantid);
        }
        else return $query->whereNull($this->getTable() . '.id');
    }
}
