<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\AppConstant;

class Product extends Model
{
    use SoftDeletes;

    /**
     * Get Table name
     **/
    public static function getTableName(){
        return ((new self)->getTable());
    }

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get Category
     **/
    public function category(){
        return $this->belongsTo(LuCategory::class,'lu_category_id');
    }

    /**
     * Get Sub Category
     **/
    public function subCategory(){
        return $this->belongsTo(LuSubCategory::class,'lu_sub_category_id');
    }

    /**
     * Get Sub Category
     **/
    public function subSubCategory(){
        return $this->belongsTo(SubSubCategory::class,'sub_sub_category_id');
    }

    /**
     * Get Size
     **/
    public function size(){
        return $this->belongsTo(LuSize::class,'lu_size_id');
    }

    /**
     * Get Product Photo
     **/
    public function photos(){
        return $this->hasMany(ProductPhoto::class,'product_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }
}
