<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = "Contacts";
    protected $primaryKey = "id";
    protected $guarded = [];
    //protected $with = array('contractor_data');

    public function contractor_data() 
    {
        return $this->belongsTo('App\Contractor','contractor_id','id');
    }

    public function getCodeAttribute($value) 
    {
        return 'PER/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }
}