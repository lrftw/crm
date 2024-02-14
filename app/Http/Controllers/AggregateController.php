<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Aggregate;

class AggregateController extends Controller
{
    const notFoundMessage = "Zapytanie ofertowe o tym ID nie zostało znalezione";

    protected function successMessage($action) { return "Zapytanie ofertowe zostało pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." zapytania ofertowego"; }

 /*   public function getAll(Request $request) 
    {
            if($request->mode == 'simple')
                return response(Inquiry_Simple::orderBy('id','desc')->get()->toJson(JSON_PRETTY_PRINT),200);
            else
                return response(Inquiry::with('quotation_data')->orderBy('id','desc')->get()->toJson(JSON_PRETTY_PRINT),200);
    }*/

    public function getAllWhere($where) 
    {
        if($where == "inprogress")
        {
            $collection = Aggregate::where('state',null)->orderBy('id','desc')->get();            
            $json = $collection->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);
        }
        else
        {
            $collection = Aggregate::orderBy('id','desc')->get();            
            $json = $collection->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);

        }
    }

}