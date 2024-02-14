<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quotation;
use App\Order;

class Inquiry extends Model
{
    protected $table = "Inquiries";
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $with = array('contact_data','general_contractor_data','electrical_contractor_data','investor_data','inquiring_contractor_data');
 /*   protected $casts = [
        'created_at' => 'datetime:d.m.Y',
        'realization_date' => 'datetime:d.m.Y',
    ];*/
    public static function boot()
    {
        parent::boot();
        self::updated(function($model){
            if($model->state == 9)
                Quotation::where('inquiry_id',$model->id)->update(['state' => 2]);
        });
    }

    public function contact_data() 
    {
        return $this->hasOne('App\Contact','id','contact_id')->with('contractor_data');
    }
    public function general_contractor_data() 
    {
        return $this->hasOne('App\Contractor','id','general_contractor_id');
    }
    public function electrical_contractor_data() 
    {
        return $this->hasOne('App\Contractor','id','electrical_contractor_id');
    }
    public function inquiring_contractor_data() 
    {
        return $this->hasOne('App\Contractor','id','inquiring_contractor_id');
    }
    public function investor_data() 
    {
        return $this->hasOne('App\Contractor','id','investor_id');
    }
    public function quotation_data() 
    {
        return $this->hasMany('App\Quotation','inquiry_id','id');
    }
    
    public function getCodeAttribute($value)
    {
        return 'INQ/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
    public function getSuccessChancesAttribute($value)
    {
        $state = $this->_getState($this->state);
        if($state >= 5 && $state <= 9)
            return null;
        else
            return $value;
    }
    public function getStateAttribute($value)
    {
        return $this->_getState($value);
    }
    public function _getState($value)
    {
        //$Quotations_Number = Quotation::where('inquiry_id',$this->id)->where('state',0)->count();
        //$Projects_Number = 0;
        //$Orders_Number = Order::where
        //return $value;
        if($value != null)
            return $value;
        else
        {   
            $Quotations_Won = Quotation::where('inquiry_id',$this->id)->where('state',1)->count();
            $Quota_N = Quotation::where('inquiry_id',$this->id)->count();
            if($Quota_N > 0)
            {
                if($Quotations_Won > 0)//Do rozszerzenia
                {
                    $Order = Order::where('quotation_id',Quotation::where('inquiry_id',$this->id)->where('state',1)->first()->id)->first();
                    if($Order)
                     {
                        return 6;
                     }
                     else 
                        return 5;
                }
                return 3;
            }//PROJECT::EXIST
            else return 1;

        }
    }
}