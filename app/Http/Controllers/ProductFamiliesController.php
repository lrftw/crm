<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductFamilies;

class ProductFamiliesController extends Controller
{
    const notFoundMessage = "Rodzina produktów o tym id nie został znaleziona";

    protected function successMessage($action) { return "Rodzina produktów została pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." rodziny produktów"; }
    public function getAll() 
    {
        return response(ProductFamilies::all()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function get($id) 
    {
            if (ProductFamilies::where('id',$id)->exists()) {
                $ProductFamilies = ProductFamilies::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($ProductFamilies, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            ProductFamilies::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzona")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(ProductFamilies::where('id',$id)->exists()) {
                ProductFamilies::where('id',$id)->first()->fill($request->all())->save();
                return response()->json(["message" =>  $this->successMessage("edytowana")], 200);    
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
            if(ProductFamilies::where('id', $id)->exists()) {
                ProductFamilies::find($id)->delete();
                return response()->json([ "message" =>  $this->successMessage("usunięta")], 200);
            } else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("usunąć"), "error" => $ex->getMessage()], 500);    
        }
    }
}