<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $table = "Contractors";
    protected $primaryKey = "id";
    protected $guarded = [];
/*    protected $with = array('contact_data');
*/
    public function all_contact_data() 
    {
        return $this->hasOne('App\Contact','contractor_id','id');//zmienić na hasMany, jak grid obsluzy
    }
    public function contact_data() 
    {
        return $this->belongsTo('App\Contact','contact_id','id');//zmienić na hasMany, jak grid obsluzy
    }
    public function getCodeAttribute($value) 
    {
        return 'COM/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
}