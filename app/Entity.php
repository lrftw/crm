<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $table = "Entities";
    protected $primaryKey = "id";
    protected $guarded = [];
    //protected $with = array('contractor_data');
    protected $with = array('entity_rows_data');
    public static function boot()
    {
        parent::boot();
        self::created(function($model){
        });
    }
    public function entity_rows_data() 
    {
        return $this->hasMany('App\EntityRow','entity_id','id');
    }
    public function getCodeAttribute($value) 
    {
        return 'ENT:'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
}