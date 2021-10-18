<?php

namespace Jiannius\Atom\Traits;

use Jiannius\Atom\Models\Team;

trait HasTeam
{
    /**
     * Get teams for user
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'teams_users');
    }

    /**
     * Scope for team id
     * 
     * @param Builder $query
     * @param integer $id
     * @return Builder
     */
    public function scopeTeamId($query, $id)
    {
        return $query->whereHas('teams', function($q) use ($id) {
            $q->whereIn('teams.id', (array)$id);
        });
    }
}