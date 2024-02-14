<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Quotation;

class OrderController extends Controller
{
    const notFoundMessage = "Zamówienie o tym ID nie zostało znalezione";

    protected function successMessage($action) { return "Zamówienie zostało pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." zamówienia"; }
    public function getAll() 
    {
            return response(Order::orderBy('id','desc')->get()->toJson(JSON_PRETTY_PRINT),200);
    }

  /*  public function getAllWhere($where) 
    {
        if($where == "inprogress")
        {
            $collection = Order::with('quotation_data')->orderBy('id','desc')->get();
            $filtered = $collection->filter(function ($value, $key) {
                return ($value->state >= 1) && ($value->state <= 4);
            })->values();
            
            $json = $filtered->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);
        }
    }*/

    public function get($id) 
    {
            if (Order::where('id',$id)->exists()) {
                $product = Order::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            Order::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzone")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(Order::where('id',$id)->exists()) {
                Order::where('id',$id)->first()->fill($request->all())->save();
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
            if(Order::where('id', $id)->exists()) {
                Order::find($id)->delete();
                return response()->json([ "message" =>  $this->successMessage("usunięte")], 200);
            } else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("usunąć"), "error" => $ex->getMessage()], 500);    
        }
    }
}