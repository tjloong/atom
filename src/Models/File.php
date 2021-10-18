<?php

namespace Jiannius\Atom\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Jiannius\Atom\Traits\FileManager;
use Jiannius\Atom\Traits\Model as AtomModel;

class File extends Model
{
    use AtomModel;
    use FileManager;

    protected $fillable = [
        'name',
        'mime',
        'size',
        'url',
        'data',
    ];

    protected $casts = [
        'size' => 'float',
        'data' => 'object',
    ];

    protected $appends = [
        'type',
    ];

    protected $orderby = [
        'created_at' => 'desc',
    ];

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
            $q
                ->where('name', 'like', "%$search%")
                ->orWhere('url', 'like', "%$search%");
        });
    }
}
