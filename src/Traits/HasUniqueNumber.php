<?php

namespace Jiannius\Atom\Traits;

use DB;

trait HasUniqueNumber
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootHasUniqueNumber()
    {
        // listen to creating event
        static::saving(function ($model) {
            if (!$model->number) {
                $table = $model->getTable();
                $duplicated = true;

                while ($duplicated) {
                    $prefix = date('ymd');
                    $random = rand(100000, 999999);
                    $number = $prefix . '-' . $random;
                    $duplicated = DB::table($table)->where('number', $number)->count() > 0;
                }

                $model->number = $number;
            }
        });
    }

}