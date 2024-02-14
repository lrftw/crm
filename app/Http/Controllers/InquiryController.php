<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;
use App\Inquiry_Simple;
use App\Quotation;

class InquiryController extends Controller
{
    const notFoundMessage = "Zapytanie ofertowe o tym ID nie zostało znalezione";

    protected function successMessage($action) { return "Zapytanie ofertowe zostało pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." zapytania ofertowego"; }
    public function getAll(Request $request) 
    {
            if($request->mode == 'simple')
                return response(Inquiry_Simple::orderBy('id','desc')->get()->toJson(JSON_PRETTY_PRINT),200);
            else
                return response(Inquiry::with('quotation_data')->orderBy('id','desc')->get()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function getAllWhere($where) 
    {
        if($where == "inprogress")
        {
            $collection = Inquiry::with('quotation_data')->orderBy('id','desc')->get();
            $filtered = $collection->filter(function ($value, $key) {
                return ($value->state >= 1) && ($value->state <= 4);
            })->values();
            
            $json = $filtered->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);
        }
        else if($where == "orderable")
        {
            $collection = Inquiry::with('quotation_data')->orderBy('id','desc')->get();
            $filtered = $collection->filter(function ($value, $key) {
                return ($value->state == 5);
            })->values();
            
            $json = $filtered->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);
        }
    }

    public function get($id) 
    {
            if (Inquiry::where('id',$id)->exists()) {
                $product = Inquiry::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            Inquiry::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzone")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(Inquiry::where('id',$id)->exists()) {
                Inquiry::where('id',$id)->first()->fill($request->all())->save();
               // $this->afterStateUpdated($id,$request->input('state'));
                return response()->json(["message" =>  $this->successMessage("edytowane")], 200);    
            }
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);    
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("edytować"), "error" => $ex->getMessage()], 500);    
        }
    }

    public function delete($id) 
    {
        try {
            if(Inquiry::where('id', $id)->exists()) {
                Inquiry::find($id)->delete();
                return response()->json([ "message" =>  $this->successMessage("usunięte")], 200);
            } else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("usunąć"), "error" => $ex->getMessage()], 500);    
        }
    }
    private function afterStateUpdated($inquiry_id,$state)
    {
        if($state == 9)
        {
            Quotation::where('inquiry_id',$inquiry_id)->update(['state' => 2]);
        }
    }
}