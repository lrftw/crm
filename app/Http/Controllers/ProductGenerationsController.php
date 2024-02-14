<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductGenerations;

class ProductGenerationsController extends Controller
{
    const notFoundMessage = "Generacja/Wersja produktu o tym ID nie został znaleziona";

    protected function successMessage($action) { return "Generacja/Wersja produktu została pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." generacji/wersji produktu "; }
    public function getAll() 
    {
        return response(ProductGenerations::all()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function get($id) 
    {
            if (ProductGenerations::where('ID',$id)->exists()) {
                $ProductGenerations = ProductGenerations::where('ID',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($ProductGenerations, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            ProductGenerations::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzony")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(ProductGenerations::where('ID',$id)->exists()) {
                ProductGenerations::where('ID',$id)->first()->fill($request->all())->save();
                return response()->json(["message" =>  $this->successMessage("edytowany")], 200);    
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
            if(ProductGenerations::where('ID', $id)->exists()) {
                ProductGenerations::find($id)->delete();
                return response()->json([ "message" =>  $this->successMessage("usunięty")], 200);
            } else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("usunąć"), "error" => $ex->getMessage()], 500);    
        }
    }
}