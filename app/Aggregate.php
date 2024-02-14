<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quotation;
use App\Order;

class Aggregate extends Model
{
    protected $table = "Inquiries";
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $appends = ['quotation_state','order_state','project_state','order_deadline'];
    protected $with = array('inquiring_contractor_data');

    //protected $with = array('contact_data','general_contractor_data','electrical_contractor_data','investor_data','inquiring_contractor_data');
 /*   protected $casts = [
        'created_at' => 'datetime:d.m.Y',
        'realization_date' => 'datetime:d.m.Y',
    ];*/
    public function getCodeAttribute($value)
    {
        return 'INQ/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
    public function getProjectStateAttribute($value)
    {
        return -1;
    }
    public function inquiring_contractor_data() 
    {
        return $this->hasOne('App\Contractor','id','inquiring_contractor_id');
    }

    public function getQuotationStateAttribute($value)
    {
        $Quotations_Won = Quotation::where('inquiry_id',$this->id)->where('state',1)->count();
        $Quotations_Canceled = Quotation::where('inquiry_id',$this->id)->where('state',3)->count();
        $Quotations_Lost = Quotation::where('inquiry_id',$this->id)->where('state',2)->count();
        $Quota_N = Quotation::where('inquiry_id',$this->id)->count();
        $Quota_NotCanceled = $Quota_N-$Quotations_Canceled;
        if($Quota_NotCanceled > 0)
        {
            if($Quotations_Won > 0)//Do rozszerzenia
            {
                return 1;
            }
            if($Quota_NotCanceled == $Quotations_Lost)
                return 2;
            return 0;
        }
        else return -1;
    }
    public function getOrderStateAttribute($value)
    {
        $Quotation_Won = Quotation::where('inquiry_id',$this->id)->where('state',1)->first();
        if($Quotation_Won)
        {
            $Order = Order::where('quotation_id',$Quotation_Won->id)->first();
            if($Order)
            
                return $Order->state;
            else return -1;
        }
        else return -1;
    }
    public function getOrderDeadlineAttribute($value)
    {
        $Quotation_Won = Quotation::where('inquiry_id',$this->id)->where('state',1)->first();
        if($Quotation_Won)
        {
            $Order = Order::where('quotation_id',$Quotation_Won->id)->first();
            if($Order)
                return $Order->deadline;
            else return null;
        }
        else return null;
    }

}
