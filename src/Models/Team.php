<?php

namespace Jiannius\Atom\Models;

use Illuminate\Database\Eloquent\Model;
use Jiannius\Atom\Models\User;
use Jiannius\Atom\Traits\Model as AtomModel;

class Team extends Model
{
    use AtomModel;
    
    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = [
        'counter',
    ];

    /**
     * Get users for team
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'teams_users');
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
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")->orWhereHas('users', function($q) use ($search) {
                $q->search($search);
            });
        });
    }

    /**
     * Scope for user id
     * 
     * @param Builder $query
     * @param integer $id
     * @return Builder
     */
    public function scopeUserId($query, $id)
    {
        return $query->whereHas('users', function($q) use ($id) {
            $q->whereIn('users.id', (array)$id);
        });
    }

    /**
     * Get counter attribute
     * 
     * @return array
     */
    public function getCounterAttribute()
    {
        return [
            'users' => $this->users()->count(),
        ];
    }
}
