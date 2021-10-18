<?php

namespace Jiannius\Atom\Traits;

use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Jiannius\Atom\Traits\MultiTenant;

trait HasSlug
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootHasSlug()
    {
        // listen to saving event
        static::saving(function ($model) {
            if ($model->slug && $model->isDirty('slug')) $str = $model->slug;
            else if (!$model->slug) $str = $model->name ?? $model->title;

            if (isset($str)) {
                // non english slug
                if (strlen($str) != strlen(utf8_decode($str))) {
                    $slug = preg_replace('#(\p{P}|\p{C}|\p{S}|\p{Z})+#u', '-', strtolower($str));
                }
                // normal english slug
                else {
                    $slug = Str::slug($str);
                }
    
                // if the generated slug is empty
                if (!$slug) $slug = strtolower(Str::random(10));
                // if the generated slug is a number, append something
                else if (is_numeric($slug)) $slug = $slug . '-' . strtolower(Str::random(5));
                // remove head and tail dash
                else $slug = trim($slug, '-');
    
                // check for uniqueness
                $query = DB::table($model->getTable())->where('slug', $slug);

                if (in_array(MultiTenant::class, class_uses_recursive($model))) {
                    $query->where('tenant_id', $model->tenant_id);
                }
    
                if ($query->count()) $slug = $slug . '-' . strtolower(Str::random(5));
                
                $model->slug = $slug;
            }
        });
    }

    /**
     * Scope for find by slug
     *
     * @param Builder $query
     * @param string $slug
     * @return Builder
     */
    public function scopeFindBySlug($query, $slug)
    {
        if (is_numeric($slug)) $query->where($this->getTable() . '.id', $slug);
        else $query->where($this->getTable() . '.slug', $slug);

        return $query->first();
    }

    /**
     * Scope for find by slug or failed
     *
     * @param Builder $query
     * @param string $slug
     * @return Builder
     */
    public function scopeFindBySlugOrFail($query, $slug)
    {
        if (is_numeric($slug)) $query->where($this->getTable() . '.id', $slug);
        else $query->where($this->getTable() . '.slug', $slug);

        return $query->firstOrFail();
    }
}