<?php

namespace Jiannius\Atom\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Jiannius\Atom\Models\User;
use Jiannius\Atom\Traits\Model as AtomModel;
use Jiannius\Atom\Traits\HasSlug;

class Role extends Model
{
    use HasSlug;
    use AtomModel;
    
    protected $fillable = [
        'name',
        'slug',
        'access',
    ];

    protected $with = [
        'abilities',
    ];

    protected $appends = [
        'is_admin',
        'is_root',
    ];

    /**
     * Get abilities for role
     */
    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'abilities_roles');
    }

    /**
     * Get users for role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope for fussy search
     * 
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%$search%");
    }

    /**
     * Scope for role name in slug or titlecase
     * 
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeName($query, $name)
    {
        return $query->where('name', str_replace('-', ' ', $name));
    }

    /**
     * Scope for assignable role
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeAssignable($query)
    {
        return $query->when(
            request()->user()->isRole('root'),
            fn($q) => $q->where('is_system', true),
            fn($q) => $q->where('access', '<>', 'root')
        );
    }

    /**
     * Scope for clonable role
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeClonable($query)
    {
        return $query->where(function ($q) {
            $q->where('is_system', false)->orWhere(function ($q) {
                $q->where('is_system', true)->where('slug', 'restricted-user');
            });
        });
    }

    /**
     * Check role is an admin role
     * 
     * @return boolean
     */
    public function getIsAdminAttribute()
    {
        return in_array($this->name, ['Admin', 'Administrator', 'Account Admin']);
    }

    /**
     * Check role is root
     * 
     * @return boolean
     */
    public function getIsRootAttribute()
    {
        return $this->access === 'root';
    }
}
