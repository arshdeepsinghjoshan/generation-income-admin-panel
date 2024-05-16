<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AActiveRecord
{
    public function scopeFindActive($query, $column = 'state_id', $state_id = 1)
    { 
        return $query->where($column, $state_id);
    }
    public function scopeMy($query, $column = 'created_by_id', $id = null)
    {
        if ($id === null) {
            $id = Auth::check() ? Auth::user()->id : 0;
        }
        return $query->where($column, $id);
    }
}
