<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityRow extends Model
{
    protected $table = "EntityRows";
    protected $primaryKey = "id";
    protected $guarded = [];
    //protected $with = array('contractor_data');
  //  protected $with = array('product_data');
    public static function boot()
    {
        parent::boot();
        self::created(function($model){
            
            EntityRow::where('id',$model->id)->update([   'price' => $model->product_data->price,
                                                          'cost_human' => $model->product_data->workhours*25,
                                                          'cost_material' => $model->product_data->components_cost,
                                                          'row_name' => $model->product_data->full_name,
                                                          'needed_workhours' => $model->product_data->workhours]);
        });
    }
    public function product_data() 
    {
        return $this->belongsTo('App\Product','product_id','id');
    }
    public function quotation_data() 
    {
        return $this->belongsTo('App\Entity','entity_id','id');
    }

    public function getCodeAttribute($value) 
    {
        return 'ENr:'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
}