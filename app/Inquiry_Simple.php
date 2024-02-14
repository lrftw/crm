<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quotation;
use App\Order;

class Inquiry_Simple extends Model
{
    protected $table = "Inquiries";
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $with = array('quotation_data');

    public function quotation_data() 
    {
        return $this->hasMany('App\Quotation','inquiry_id','id');
    }
    public function getCodeAttribute($value)
    {
        return 'INQ/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
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
                return 1;
            }//PROJECT::EXIST
            else return 1;

        }
    }
}