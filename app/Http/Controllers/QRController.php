<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\QRCodes;
use App\Http\Controllers\ApiController;

class QRController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $qr = QRCodes::all();

        return $this->showAll($qr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $rules = [
        //   'name' => 'required',
        // ];

        //$this->validate($request, $rules);
        $data = $request->all();
        if ($request->hasFile('image')) {
        $image = $request->file('image');
        $folder = 'uploads/images/';
        $name =  str_random(25);
        $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        $image->move($folder, $name. '.' . $image->getClientOriginalExtension());
        //$this->uploadOne($image, $folder, 'public', $name);
        $data['image'] = $filePath;
        }
        $data['qrToken'] = str_random(25);
        $data['inUse'] = "1";
        $qr = QRcodes::create($data);
        //return $request;
        return $this->showOne($qr, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(QRCodes $qr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QRCodes $qr)
    {
        if ($request->has('door')) {
            $qr->door = $request->door;
        }
        if ($request->has('establishmentId')) {
            $qr->establishmentId = $request->establishmentId;
        }
        if ($request->has('location')) {
            $qr->location = $request->location;
        }
        if ($request->has('image')) {
            $qr->image = $request->image;
        }
        // if ($request->hasFile('image')) {
        // $image = $request->file('image');
        // $folder = 'uploads/images/';
        // $name =  str_random(25);
        // $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        // $image->move($folder, $name. '.' . $image->getClientOriginalExtension());
        // //$this->uploadOne($image, $folder, 'public', $name);
        // $qr['image'] = $filePath;
        // }

        if (!$qr->isDirty()) {

        return response()->json([
          'error' => 'You need to specify a different value to update',
          'code' => 422
        ], 422);
        }

        $qr->save();
        //return $image;
        return response()->json(['data' => $request->all()], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(QRCodes $qr)
    {
        // $establishment = Establishment::findOrFail($id);
        //Storage::delete($establishment->logo);
        $qr->delete();

        return response()->json(['data' => $qr], 200);
    }

    public function created_by_user_qr($userId) {
      $codes = QRCodes::where('userId', $userId)->get();

      return response()->json(['data' => $codes], 200);
    }
}
