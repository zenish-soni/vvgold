<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\AppConstant;

class LuSubCategory extends Model
{
    use SoftDeletes;

    /**
     * Get Table name
     **/
    public static function getTableName(){
        return ((new self)->getTable());
    }

    /**
     * Get Image Attribuge
     **/
    public function getImageAttribute($image){
        return AppConstant::getImage($image);
    }

    /**
     * Get Category Name
     **/
    public function category(){
        return $this->belongsTo(LuCategory::class,'lu_category_id');
    }

    /**
     * Get Sub Category Name
     **/
    public function subSubCategory(){
        return $this->hasMany(SubSubCategory::class,'lu_sub_category_id')->where('status',1);
    }
}
