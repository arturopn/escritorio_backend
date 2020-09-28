<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ApiController;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends ApiController
{
    public function __construct() {
      parent::__construct();
    }

     public function index()
    {
        $rate = Rate::all();

        return $this->showAll($rate);
    }


    public function store(Request $request)
    {
        $rules = [
          'establishmentId' => 'required',
          'charge_1' => 'required'
        ];

        //$this->validate($request, $rules);
        $data = $request->all();


        $rate = Rate::create($data);
        //return $image;
        return response()->json(['data' => $rate], 201);


    }

    public function show(Rate $rate)
    {
        // $establishment = Establishment::findOrFail($id);

        return $this->showOne($rate);

    }

    public function update(Request $request, Rate $rate)
    {
    	// $establishment = User::findOrFail($id);

    	if ($request->has('establishmentId')) {
        	$rate->establishmentId = $request->establishmentId;
      	}

      	if ($request->has('tolerance')) {
        	$rate->tolerance = $request->tolerance;
      	}

      	if ($request->has('charge_1')) {
        	$rate->charge_1 = $request->charge_1;
      	}

      	if ($request->has('is_double')) {
        	$rate->is_double = $request->is_double;
      	}

      	if ($request->has('charge_2')) {
        	$rate->charge_2 = $request->charge_2;
      	}

      	if ($request->has('subsequent')) {
        	$rate->subsequent = $request->subsequent;
      	}
      	if ($request->has('from')) {
        	$rate->from = $request->from;
      	}
      	if ($request->has('photo')) {
        	$rate->to = $request->to;
      	}
      	if ($request->has('one_time_payment')) {
        	$rate->one_time_payment = $request->to;
      	}





      	if (!$rate->isDirty()) {

        return response()->json([
          'error' => 'You need to specify a different value to update',
          'code' => 422
        ], 422);
      }

     $rate->save();
      //return $image;
      return response()->json(['data' => $request->all()], 200);

    }

    public function destroy(Rate $rate)
    {
        // $establishment = Establishment::findOrFail($id);
        //Storage::delete($establishment->logo);
        $rate->delete();

        return response()->json(['data' => $rate], 200);

    }

    public function created_by_user_rate($userId) {
      $rates = Rate::where('userId', $userId)->get();

      return $this->showAll($rates);
    }
}
