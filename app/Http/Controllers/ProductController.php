<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
    const notFoundMessage = "Produkt o tym ID nie zostal znaleziony";

    protected function successMessage($action) { return "Produkt zostal pomyslnie ".$action; }
    protected function errorMessage($action) { return "Nie udalo sie ".$action." produktu"; }
    public function getAllProducts() 
    {
        return response(Product::all()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function getProduct($id) 
    {
            if (Product::where('ProductID',$id)->exists()) {
                $product = Product::where('ProductID',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function createProduct(Request $request) 
    {
        Product::create($request->all());
        return response()->json(["message" =>  $this->successMessage("utworzony")], 200);
    }


    public function updateProduct(Request $request, $id) 
    {
        try {
            if(Product::where('ProductID',$id)->exists()) {
                Product::where('ProductID',$id)->first()->fill($request->all())->save();
                return response()->json(["message" =>  $this->successMessage("edytowany")], 200);    
            }
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);    
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("edytowac"), "error" => $ex->getMessage()], 500);    
        }
    }

    public function deleteProduct($id) 
    {
        if(Product::where('ProductID', $id)->exists()) {
            Product::find($id)->delete();
            return response()->json([ "message" =>  $this->successMessage("usunięty")], 200);
        } else {
            return response()->json(["message" => $this::notFoundMessage], 404);
        }
    }
}