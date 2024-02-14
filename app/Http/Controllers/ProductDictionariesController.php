<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductDictionaries;

class ProductDictionariesController extends Controller
{
    const notFoundMessage = "Słownik parametrow o tym ID nie został znaleziony";

    protected function successMessage($action) { return "Słownik parametrow został pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." słownika parametrów"; }
    public function getAll() 
    {
        return response(ProductDictionaries::all()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function get($id) 
    {
            if (ProductDictionaries::where('ID',$id)->exists()) {
                $ProductDictionaries = ProductDictionaries::where('ID',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($ProductDictionaries, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            ProductDictionaries::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzony")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(ProductDictionaries::where('ID',$id)->exists()) {
                ProductDictionaries::where('ID',$id)->first()->fill($request->all())->save();
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
            if(ProductDictionaries::where('ID', $id)->exists()) {
                ProductDictionaries::find($id)->delete();
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