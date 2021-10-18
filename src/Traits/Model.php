<?php

namespace Jiannius\Atom\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait Model
{
    /**
     * Apply scope from filters
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $filter) {
            $column = $filter['column'];
            $value = $filter['value'];
            $scope = $filter['scope'] ?? null;
            $operator = $filter['operator'] ?? null;

            if ($scope) $query->$scope($value);
            else if ($operator) $query->where($column, $operator, $value);
            else if (is_array($value)) $query->whereIn($column, $value);
            else $query->where($column, $value);
        }

        return $query;
    }

    /**
     * Scope for filter from query string
     * 
     * @param Buildr $query
     * @return Builder
     */
    public function scopeFilterFromQuery($query)
    {
        $query->filter($this->getFiltersFromQuery());

        if (request()->order_by) {
            foreach (explode(',', request()->order_by) as $order) {
                $split = explode('__', $order);
                $query->orderBy($split[0], $split[1]);
            }
        }
        else if (isset($this->orderby)) {
            foreach ($this->orderby as $key => $val) {
                $query->orderBy($key, $val);
            }
        }

        return $query;
    }

    /**
     * Scope for fetching the api resource from query builder
     *
     * @param Builder $query
     * @return Collection
     */
    public function scopeFetch($query)
    {
        $query->filterFromQuery();

        $limit = request()->limit ?? $this->paginate ?? 30;
        $filters = [];

        foreach ($this->getFiltersFromQuery() as $filter) {
            $filters[$filter['column']] = $filter['value'];
        }

        if ($this->paginate === false) $collection = (new ResourceCollection($query->get()));
        else $collection = (new ResourceCollection($query->paginate($limit)));

        return tap(
            $collection->additional(['meta' => ['filters' => $filters]]),
            function ($paginator) {
                return $paginator->getCollection()->transform(function ($model) {
                    if (method_exists($model, 'getCounterAttribute')) $model->append('counter');
                    
                    return $model;
                });
            }
        );
    }

    /**
     * Scope for date range
     *
     * @param Builder $query
     * @param string $from
     * @param string $to
     * @param string $column
     * @return Builder
     */
    public function scopeDateRange($query, $from = null, $to = null, $column = 'date')
    {
        if ($from && !$to) return $query->whereRaw("$column >= '$from'");
        if (!$from && $to) return $query->whereRaw("$column <= '$to'");
        if ($from && $to) return $query->whereRaw("$column between '$from' and '$to'");
    }

    /**
     * Check model has a specific column
     * 
     * @return boolean
     */
    public function hasColumn($column)
    {
        $table = $this->getTable();

        return ($this->connection && Schema::connection($this->connection)->hasColumn($table, $column))
                    || (!$this->connection && Schema::hasColumn($table, $column));
    }

    /**
     * Check model column is a date type
     * 
     * @return boolean
     */
    public function isDateColumn($column)
    {
        return $this->hasColumn($column)
            && Schema::getColumnType($this->getTable(), $column) === 'date';
    }

    /**
     * Check model column is a datetime type
     * 
     * @return boolean
     */
    public function isDatetimeColumn($column)
    {
        return $this->hasColumn($column)
            && in_array(Schema::getColumnType($this->getTable(), $column), ['datetime', 'timestamp']);
    }

    /**
     * Get filters from query string
     */
    public function getFiltersFromQuery()
    {
        if (request()->isMethod('post')) $qs = request()->all();
        else $qs = request()->query();

        if ($qs == '' || is_null($qs)) return [];
        if (is_string($qs)) return ['search' => $qs];

        $filters = [];

        foreach ($qs as $key => $value) {
            if (is_null($value)) continue;

            $column = preg_replace('/(_from|_to)$/', '', $key);
            $fn = Str::camel($column);

            if (method_exists($this, 'scope' . ucfirst($fn))) {
                array_push($filters, ['column' => $column, 'value' => $value, 'scope' => $fn]);
            }
            else {
                if ($this->isDateColumn($column) || $this->isDatetimeColumn($column)) {
                    if (Str::endsWith($key, '_from')) array_push($filters, ['column' => $column, 'value' => $value, 'operator' => '>=']);
                    if (Str::endsWith($key, '_to')) array_push($filters, ['column' => $column, 'value' => $value, 'operator' => '<=']);
                }
                else if ($this->hasColumn($column)) {
                    array_push($filters, ['column' => $column, 'value' => $value]);
                }
            }
        }

        return $filters;
    }
}
