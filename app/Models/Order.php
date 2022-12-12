<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Get Details
     **/
    public function details(){
        return $this->hasMany(OrderDetail::class,'order_id');
    }

    /**
     * Get User
     **/
    public function user(){
        return $this->belongsTo(User::class);
    }
}
