<?php

namespace App\Services\Shared;

use Illuminate\Support\Facades\DB;

class ServiceTableSunat
{
    public function getAll($table)
    {
        return DB::table($table)->get();
    }

    public function findById($table, $index, $value)
    {
        return DB::table($table)->where($index, $value)->first();
    }
}

