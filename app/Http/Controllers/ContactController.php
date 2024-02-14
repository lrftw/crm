<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;

class ContactController extends Controller
{
    const notFoundMessage = "Kontakt o tym ID nie został znaleziony";

    protected function successMessage($action) { return "Kontakt został pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." kontaktu"; }
    public function getAll() 
    {
        return response(Contact::with('contractor_data')->get()->toJson(JSON_PRETTY_PRINT),200);
    }

    public function get($id) 
    {
            if (Contact::where('id',$id)->exists()) {
                $product = Contact::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request) 
    {
        try {
            Contact::create($request->all());
            return response()->json(["message" =>  $this->successMessage("utworzone")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(Contact::where('id',$id)->exists()) {
                Contact::where('id',$id)->first()->fill($request->all())->save();
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
            if(Contact::where('id', $id)->exists()) {
                Contact::find($id)->delete();
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