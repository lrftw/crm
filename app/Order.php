<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Inquiry;
class Order extends Model
{
    protected $table = "Orders";
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $appends = array('code','month_year');
    protected $with = array('quotation_data');
    protected $dates = ['delivered_at'];
    public static function boot()
    {
        parent::boot();
        self::created(function($model){
             //   Quotation::where('inquiry_id',$model->inquiry_id)->where('id','!=',$model->id)->update(['state' => 3]);
        });
        self::updated(function($model){
            if($model->state == 4)
            {
                Inquiry::where('id',$model->quotation_data->inquiry_id)->update(['state' => 8]);
                Order::where('id',$model->id)->update(['delivered_at' => date('Y-m-d H:i:s')]);
            }
            if($model->state != 4)
            {
                Inquiry::where('id',$model->quotation_data->inquiry_id)->update(['state' => null]);
                Order::where('id',$model->id)->update(['delivered_at' => null]);
            }
         //   Quotation::where('inquiry_id',$model->inquiry_id)->where('id','!=',$model->id)->update(['state' => 3]);
    });

    }
    public function quotation_data() 
    {
        return $this->belongsTo('App\Quotation','quotation_id','id');
    }

    public function getCodeAttribute($value) 
    {
        return 'ORD/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }
    public function getMonthYearAttribute($value) {
        if($this->delivered_at != null)
            return $this->delivered_at->format('m-Y');
        else return null;
    }
}