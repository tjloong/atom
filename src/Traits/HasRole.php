<?php

namespace Jiannius\Atom\Traits;

use Jiannius\Atom\Models\Role;
use Jiannius\Atom\Models\Ability;
use Illuminate\Support\Str;

/**
 * This trait is designed to be used for User model
 */
trait HasRole
{
    /**
     * Get role for user
     */
    public function role()
    {
        return $this->belongsTo(Role::class)->withoutGlobalScopes();
    }

    /**
     * Get abilities for user
     */
    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'abilities_users')->withPivot('access');
    }

    /**
     * Scope for is role
     * 
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeWhereIsRole($query, $name)
    {
        return $query->whereHas('role', fn($q) => $q->where('slug', $name));
    }

    /**
     * Check user is role
     * 
     * @param mixed $names
     * @return boolean
     */
    public function isRole($names)
    {
        if (!$this->role) return false;

        return collect((array)$names)->filter(function($name) {
            $substr = Str::slug(str_replace('*', '', $name));
            $role = Str::slug($this->role->name);

            if ($name === 'root') return $this->role->is_root;
            else if ($name === 'admin') return $this->role->is_admin;
            else if (Str::startsWith($name, '*')) return Str::endsWith($role, $substr);
            else if (Str::endsWith($name, '*')) return Str::startsWith($role, $substr);
            else return $role === $name;
        })->count() > 0;
    }
}