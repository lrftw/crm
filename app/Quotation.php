<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;

class Quotation extends Model
{
    protected $table = "Quotations";
    protected $primaryKey = "id";
    protected $guarded = [];
    //protected $fillable = array('quotation_rows_data');
    protected $with = array('inquiry_data','entity_data');
    protected $appends = array('order_state');

    public static function boot()
    {
        parent::boot();
        self::created(function($model){

        });
    }   

    public function entity_data() 
    {
        return $this->hasOne('App\Entity','id','entity_id');
    }
    public function inquiry_data() 
    {
        return $this->belongsTo('App\Inquiry','inquiry_id','id');
    }
    public function getCodeAttribute($value) 
    {
        return 'QUO/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
    public function getTotalValueAttribute($value) { return intval($value); }   
    public function getTotalCostAttribute($value) { return intval($value); }   
    public function getOrderStateAttribute()
    {
        $order = Order::where('quotation_id', $this->id)->first();
        if(!$order) {
            return 'NOT_ORDERED';
        } 
        else {
            if($order->quotation_data->inquiry_data->id)
            return $order->state;
        }
    }
    /*public function setQuotationRowsDataAttribute($value)
    {
        $this->quotation_rows_data()->createMany($value);
    }*/

}