<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;
use App\Order;
use Illuminate\Support\Facades\DB;

class RaportController extends Controller
{
    public function InquiryCountByVoivodeship(Request $request) {
        if($request->where == 'won')
        {
            $data = DB::select("select voivodeship,count(*) as count from Inquiries WHERE state = '8' GROUP BY voivodeship");
            return response($data, 200);
        }
        else if($request->where == 'lost')
        {
            $data = DB::select("select voivodeship,count(*) as count from Inquiries WHERE state = '9' GROUP BY voivodeship");
            return response($data, 200);
        } 
        else 
        {
            $data = DB::select("select voivodeship,count(*) as count from Inquiries GROUP BY voivodeship");
            return response($data, 200);
        }
    }
    public function EntityCountByMonth(Request $request) {
        $month_q = "'1' = '1'";
        $month_y = "'1' = '1'";
        if($request->month != null)
            $month_q = "MONTH(created_at) = ".$request->month;
        if($request->year != null)
            $year_q = "YEAR(created_at) = ".$request->year;
        $data = DB::select("select count(*) as data from {$request->entity} WHERE {$month_q} AND {$month_y}");
        return response($data, 200);
    }
    public function OrderValuesWithDates(Request $request) {
        $data = Order::where('delivered_at','!=',null)->orderBy('delivered_at','asc')->get();
        return response($data, 200);
    }
    public function OrderValueByMonth(Request $request) {
        $collection = Order::with('quotation_data')->where('delivered_at','!=',null)->whereYear('delivered_at','=',date("Y"))->orderBy('id','desc')->get();
        $filtered = $collection->groupBy('month_year');
        $json = $filtered->toJson(JSON_NUMERIC_CHECK);
        return response($json, 200);
    }
    public function OrderValueByContractor(Request $request) {
        if($request->month != "all")
            $collection = Order::with('quotation_data')->where('delivered_at','!=',null)->whereYear('delivered_at','=',date("Y"))->whereMonth('delivered_at','=',$request->month)->orderBy('id','desc')->get();
        else 
            $collection = Order::with('quotation_data')->where('delivered_at','!=',null)->whereYear('delivered_at','=',date("Y"))->orderBy('id','desc')->get();
            $filtered = $collection->groupBy(function($data) {return $data->quotation_data->inquiry_data->inquiring_contractor_data->name;});
            $json = $filtered->toJson(JSON_NUMERIC_CHECK);
        return response($json, 200);
    }

    public function IncomeBy(Request $request) {
        //Income By Voivodeship With By Month and Year
        $where         = "";
        $whereMonth    = "'1' = '1'";
        $whereYear     = "'1' = '1'";
        
        if($request->month != null) 
        {
            $whereMonth = "MONTH(s.created_at) = {$request->month}";
            $whereYear  = "YEAR(s.created_at) = YEAR(CURDATE())";
        }
        if($request->year != null) 
        {
            $whereYear  = "YEAR(s.created_at) = {$request->year}";
        }
                                    
        $where = "{$whereMonth} AND {$whereYear}";

        $by = "";
        if($request->by == 'voivodeship')
            $by = "i.voivodeship";
        else if($request->by == "contractor")
            $by = "c.name";

        $data = DB::select("SELECT SUM(e.agg_total_value) as total_value,SUM(e.agg_total_cost) as total_cost,{$by} as _group FROM Sales as s LEFT JOIN Entities e ON e.id = s.entity_id
        LEFT JOIN Orders o ON o.id = s.order_id
        LEFT JOIN Quotations q ON q.id = o.quotation_id
        LEFT JOIN Inquiries i ON i.id = q.inquiry_id
        LEFT JOIN Contractors c ON c.id = i.inquiring_contractor_id
        WHERE {$where} 
        GROUP BY {$by} 
        ORDER BY SUM(e.agg_total_value) DESC");

        return response($data, 200);
    }

}