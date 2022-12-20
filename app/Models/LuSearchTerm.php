<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuSearchTerm extends Model
{
    use SoftDeletes;

    /**
     * Get Table name
     **/
    public static function getTableName(){
        return ((new self)->getTable());
    }
}
