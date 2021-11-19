<?php

namespace App\Traits;

trait HasSearch
{
    public function scopeSearch($query, $columnName, $value)
    {
        return $query->where($columnName, '=', $value);
    }

    public function scopeOrSearch($query, $columnName, $value)
    {
        return $query->orWhere($columnName, '=', $value);
    }

    public function scopeSearchLike($query, $columnName, $value)
    {
        return $query->where($columnName, 'LIKE', '%'.$value.'%');
    }

    public function scopeOrSearchLike($query, $columnName, $value)
    {
        return $query->orWhere($columnName, 'LIKE', '%'.$value.'%');
    }

    public function scopeSearchFromDate($query, $columnName, $value)
    {
        return $query->whereDate($columnName, '>=', $value);
    }

    public function scopeSearchToDate($query, $columnName, $value)
    {
        return $query->whereDate($columnName, '<=', $value);
    }
}
