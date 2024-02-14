<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductFamilies extends Model
{
    protected $table = "ProductFamilies";
    protected $primaryKey = "id";
    protected $guarded = [];
    public function getCodeAttribute($value)
    {
        return 'PRF/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }  
}
