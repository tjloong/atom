<?php

namespace Jiannius\Atom\Traits;

use App\Models\User;
use Jiannius\Atom\Traits\HasTeam;

/**
 * This trait is used by any model that require ownership
 */
trait HasOwner
{
	/**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootHasOwner()
    {
        static::creating(function($model) {
            if (request()->user()) {
                $model->created_by = request()->user()->id;
                $model->owned_by = request()->user()->id;
            }
        });

        static::updating(function($model) {
            if (request()->user()) {
                $model->owned_by = $model->owned_by ?? request()->user()->id;
            }
        });

        static::addGlobalScope('owner', function ($query) {
            if (request()->user()) $query->accessibleByUser();
        });
    }

    /**
     * Initialize the trait
     * 
     * @return void
     */
    protected function initializeHasOwner()
    {
        $this->fillable[] = 'owned_by';
        $this->fillable[] = 'created_by';
        
        $this->casts['owned_by'] = 'integer';
        $this->casts['created_by'] = 'integer';
    }

    /**
     * Get owner for model
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owned_by');
    }

    /**
     * Get creator for model
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for accessibility by current user
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeAccessibleByUser($query)
    {
        $user = request()->user();

        if (!$user->hasColumn('role_id')) return $query;
        if ($user->isRole('root')) return $query;

        $access = $user->role->access ?? null;
        if (!$access) abort(401);

        if ($access === 'restrict') {
            return $query->where('owned_by', $user->id);
        }
        else if ($access === 'global') {
            return $query;
        }
        else if ($access === 'team') {
            $query->where('owned_by', $user->id);

            if (in_array(HasTeam::class, class_uses_recursive($user))) {
                return $query->orWhere(function($q) use ($user) {
                    $q->ownerTeamId($user->teams->pluck('id')->toArray());
                });
            }
        }
    }

    /**
     * Scope for owner with team id
     * 
     * @param Builder $query
     * @param integer $id
     * @return Builder
     */
    public function scopeOwnerTeamId($query, $id)
    {
        return $query->whereHas('owner', function($q) use ($id) {
            $q->teamId($id);
        });
    }
}
