<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contractor;

class ContractorController extends Controller
{
    const notFoundMessage = "Kontrahent o tym ID nie został znaleziony";

    protected function successMessage($action) { return "Kontrahent został pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." kontrahenta"; }
    public function getAll() 
    {
        return response(Contractor::with('contact_data')->get()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function get($id) 
    {
            if (Contractor::where('id',$id)->exists()) {
                $product = Contractor::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            Contractor::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzony")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(Contractor::where('id',$id)->exists()) {
                Contractor::where('id',$id)->first()->fill($request->all())->save();
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
            if(Contractor::where('id', $id)->exists()) {
                Contractor::find($id)->delete();
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