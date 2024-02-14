<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

function pnull($val) { if($val == null || $val == "")return "/"; else return $val;}        

class Product extends Model
{
    protected $table = "Products";
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $appends = array("family_model");
    protected $with = array('product_family_data');

    public function product_family_data() 
    {
        return $this->belongsTo('App\ProductFamilies','product_family_id','id');
    }
    public function getFullNameAttribute($value)
    {
        return pnull($this->product_family_data['name']).' '.
               pnull($this->model).'-'.
               pnull($this->temperature).'-'.
               pnull($this->montage).'-'.
               'IP'.pnull($this->ip).'-'.
               pnull($this->management_type).'-'.
               pnull($this->distribution_angle).'-'.
               pnull($this->code);
    }
    public function getCodeAttribute($value) 
    {
        return 'PRO/'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }   
    public function getFamilyModelAttribute($value)
    {
        return $this->product_family_data->name.' '.$this->model;
    }
}