<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quotation;
use App\Entity;


class QuotationController extends Controller
{
    const notFoundMessage = "Wycena o tym ID nie został znaleziony";

    protected function successMessage($action) { return "Wycena została pomyślnie ".$action; }
    protected function errorMessage($action) { return "Nie udało się ".$action." kontaktu"; }
    public function getAll() 
    {
        return response(Quotation::get()->toJson(JSON_PRETTY_PRINT),200);
    }
    public function getAllWhere($where) 
    {
        global $_conditions;
        global $_where;
        $_where = $where;
        $_conditions = array(
            'orderable' => function($arg) { return ($arg->state == 1 && $arg->inquiry_data->state == 5);}
        );
            $collection = Quotation::orderBy('id','desc')->get();
            $filtered = $collection->filter(function ($value, $key) {
                global $_conditions;
                global $_where;
                return $_conditions[$_where]($value);
            })->values();
            $json = $filtered->toJson(JSON_PRETTY_PRINT);
            return response($json, 200);
    }


    public function get($id) 
    {
            if (Quotation::where('id',$id)->exists()) {
                $product = Quotation::where('id',$id)->get()->toJson(JSON_PRETTY_PRINT);
                return response($product, 200);
            } 
            else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
    }

    public function create(Request $request)
    {
        try {
            $entity_container = new Entity;
            $entity_container->save();

            $quota = new Quotation;
            $quota->fill($request->except(['entity_rows_data']));
            $quota->entity_id = $entity_container->id;
            $quota->save();

            $entity_container->entity_rows_data()->createMany($request->input('entity_rows_data'));

            $total_value = 
            $total_cost = 
            $total_human_cost = 
            $total_material_cost = 
            $total_workhours = 0;

            foreach($entity_container->entity_rows_data as $row)
            {
                $total_value            += $row->quantity * ($row->product_data->price * (1-($row->discount/100)));
                $total_cost             += $row->quantity * ($row->product_data->components_cost + ($row->product_data->workhours*25));
                $total_human_cost       += $row->quantity * ($row->product_data->workhours * 25);
                $total_material_cost    += $row->quantity * ($row->product_data->components_cost);
                $total_workhours        += $row->quantity * ($row->product_data->workhours);
            }
            $entity_container->update([  'agg_total_value'         => $total_value,
                                         'agg_total_cost'          => $total_cost,
                                         'agg_total_human_cost'    => $total_human_cost,
                                         'agg_total_material_cost' => $total_material_cost,
                                         'agg_total_workhours'     => $total_workhours]);

            return response()->json(["message" =>  $this->successMessage("utworzone")], 200);
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("utworzyć"), "error" => $ex->getMessage()], 500);    
        }
    }


    public function update(Request $request, $id) 
    {
        try {
            if(Quotation::where('id',$id)->exists()) {
                $inquiry_id = Quotation::where('id',$id)->first()->inquiry_id;
                $check_result = $this->checkIfStateCanBeUpdated($inquiry_id,$request->input('state'),$id);
                if($check_result !== true) return response()->json(["message" => $check_result, "error" => $check_result], 405);    

                Quotation::where('id',$id)->first()->fill($request->all())->save();
                $this->afterStateUpdated($inquiry_id,$id,$request->input('state'));

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
            if(Quotation::where('id', $id)->exists()) {
                Quotation::find($id)->delete();
                return response()->json([ "message" =>  $this->successMessage("usunięte")], 200);
            } else {
                return response()->json(["message" => $this::notFoundMessage], 404);
            }
        }
        catch(\Illuminate\Database\QueryException $ex) {
            return response()->json(["message" => $this->errorMessage("usunąć"), "error" => $ex->getMessage()], 500);    
        }
    }

    private function checkIfStateCanBeUpdated($inquiry_id,$state,$id) 
    {
        if($state == 1)
        {
            if(Quotation::where('inquiry_id',$inquiry_id)->where('state',1)->count() == 0)
                return true;
            else 
                return "To zapytanie ofertowe posiada już wygraną oferte";
        }else if($state == 0)
        {
            if((Quotation::where('inquiry_id',$inquiry_id)->where('state',1)->count() == 0) || 
                Quotation::where('inquiry_id',$inquiry_id)->where('state',1)->first()->id == $id)
                return true;
            else 
                return "To zapytanie ofertowe posiada już wygraną oferte";
        }
        else return true;
    }
    private function afterStateUpdated($inquiry_id,$quotation_id,$state)
    {
        if($state == 1)
        {
            Quotation::where('inquiry_id',$inquiry_id)->where('id','!=',$quotation_id)->where('state','=',0)->update(['state' => 3]);
        }
    }

}