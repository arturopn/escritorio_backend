<?php
namespace App\Http\Controllers;
use App\Models\Establishment;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;
use App\Http\Controllers\ApiController;
use App\Image;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
//require_once "../vendor/autoload.php";
include('../SWSDK.php');
use SWServices\JSonIssuer\JsonEmisionTimbrado as jsonEmisionTimbrado;
use App\Mail\TestAmazonSes;
use Mail;

class EstablishmentController extends ApiController
{

     use UploadTrait;

    public function __construct() {
      parent::__construct();
    }

     public function index()
    {
        $establishment = Establishment::all();

        return $this->showAll($establishment);
    }


    public function store(Request $request)
    {
        $rules = [
          'name' => 'required',
        ];

        //$this->validate($request, $rules);
        $data = $request->all();
        if ($request->hasFile('logo')) {
        $storage = (new Factory())->createStorage();
        $storageClient = $storage->getStorageClient();
        $defaultBucket = $storage->getBucket();
        $image = $request->file('logo');
        $folder = 'uploads/images/';
        $name =  str_random(25);
        $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        $image->move($folder, $name. '.' . $image->getClientOriginalExtension());
        //$this->uploadOne($image, $folder, 'public', $name);
        $defaultBucket->upload(
          fopen($image, 'r+')
        );
        $data['logo'] = $filePath;
        }

        $establishment = Establishment::create($data);
        //return $request;
        return $this->showOne($establishment, 201);


    }

    public function show(Establishment $establishment)
    {
        // $establishment = Establishment::findOrFail($id);

        return $this->showOne($establishment);

    }

    public function update(Request $request, Establishment $establishment)
    {
    	// $establishment = User::findOrFail($id);

    	if ($request->has('name')) {
        	$establishment->name = $request->name;
      	}

      	if ($request->has('location')) {
        	$establishment->location = $request->location;
      	}

      	if ($request->has('address')) {
        	$establishment->address = $request->address;
      	}

        if ($request->has('ownerId')) {
          $establishment->ownerId = $request->ownerId;
        }

        //$image = Establishment::find(85);
        if ($request->hasFile('logo')) {
        $image = $request->file('logo');
        $folder = 'uploads/images/';
        $name =  str_random(25);
        $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        $image->move($folder, $name. '.' . $image->getClientOriginalExtension());
        //$this->uploadOne($image, $folder, 'public', $name);
        $establishment['logo'] = $filePath;
        }

      	// if ($request->has('logo')) {
       //  	$establishment->logo = $request->logo;
      	// }

      	if ($request->has('capacity')) {
        	$establishment->capacity = $request->capacity;
      	}

        if ($request->has('rfc')) {
          $establishment->rfc = $request->rfc;
        }

        if ($request->has('clabe')) {
          $establishment->clabe = $request->clabe;
        }

      	if ($request->has('discount')) {
        	$establishment->discount = $request->discount;
      	}

      	if (!$establishment->isDirty()) {

        return response()->json([
          'error' => 'You need to specify a different value to update',
          'code' => 422
        ], 422);
      }

     $establishment->save();
      //return $image;
      return response()->json(['data' => $request->all()], 200);

    }

    public function destroy(Establishment $establishment)
    {
        // $establishment = Establishment::findOrFail($id);
        //Storage::delete($establishment->logo);
        $establishment->delete();

        return response()->json(['data' => $establishment], 200);

    }

    public function get_near_places(Request $request){
      $lat = $request->lat;
      $lng = $request->lng;
      $distance = $request->distance;

      $locations = DB::select(DB::raw("
            SELECT \"establishmentId\", name, address, lat, lon, distance FROM (
              SELECT \"establishmentId\", name, address, lat, lon,
                (6371 * acos(cos( radians('$lat')) *
                cos(radians(CAST ( lat AS DOUBLE PRECISION))) *
                cos(radians(CAST ( lon AS DOUBLE PRECISION ))- radians('$lng'))+sin(radians('$lat')) *
            		sin(radians(CAST ( lat AS DOUBLE PRECISION ))))) as distance
                FROM
                  (SELECT
                    \"establishmentId\",
                    name,
                    address,
                    split_part(location,',',1) AS lat,
                    split_part(location,',',2) AS lon
                    FROM establishments
                  ) as X
            ) establishments WHERE distance < '$distance' ORDER BY distance"));

            return $locations;
    }

    public function calculate_payment(Request $request){
      $total = 10000;
      $discount = 0;
      return response()->json(['total' => ($total - $discount )]);
    }

    public function get_user_establishments($userId){
      $establishments = Establishment::where('ownerId', $userId)->get();

      return response()->json(['data' => $establishments], 200);

    }

    public function getEstablishmentsInformation(Request $request) {
      // $ids =  $request->ids;
      $ids = explode(',', $request->ids);
      $establishments = Establishment::whereIn('establishmentId', $ids)->get();
      return response()->json(['data' => $establishments], 200);
    }


    public function createBill(){
      header('Content-Type: text/plain');
      date_default_timezone_set('America/Mexico_City');
      $params = array(
          "url"=>"http://services.test.sw.com.mx",
          "user"=>"demo",
          "password"=> "123456789",
          "token"=> "T2lYQ0t4L0RHVkR4dHZ5Nkk1VHNEakZ3Y0J4Nk9GODZuRyt4cE1wVm5tbXB3YVZxTHdOdHAwVXY2NTdJb1hkREtXTzE3dk9pMmdMdkFDR2xFWFVPUXpTUm9mTG1ySXdZbFNja3FRa0RlYURqbzdzdlI2UUx1WGJiKzViUWY2dnZGbFloUDJ6RjhFTGF4M1BySnJ4cHF0YjUvbmRyWWpjTkVLN3ppd3RxL0dJPQ.T2lYQ0t4L0RHVkR4dHZ5Nkk1VHNEakZ3Y0J4Nk9GODZuRyt4cE1wVm5tbFlVcU92YUJTZWlHU3pER1kySnlXRTF4alNUS0ZWcUlVS0NhelhqaXdnWTRncklVSWVvZlFZMWNyUjVxYUFxMWFxcStUL1IzdGpHRTJqdS9Zakw2UGRiMTFPRlV3a2kyOWI5WUZHWk85ODJtU0M2UlJEUkFTVXhYTDNKZVdhOXIySE1tUVlFdm1jN3kvRStBQlpLRi9NeWJrd0R3clhpYWJrVUMwV0Mwd3FhUXdpUFF5NW5PN3J5cklMb0FETHlxVFRtRW16UW5ZVjAwUjdCa2g0Yk1iTExCeXJkVDRhMGMxOUZ1YWlIUWRRVC8yalFTNUczZXdvWlF0cSt2UW0waFZKY2gyaW5jeElydXN3clNPUDNvU1J2dm9weHBTSlZYNU9aaGsvalpQMUxqOVhnUURvVWdZR1E1MmpZUWd6ckdoa3MwMW53UUNlSWg4UEdGUEtyYUl3ZTlYMFhxdTN1Mld3K2xzeGVFTlQrY1AzdnlDTkF6MEF3ZXV5WU1ZU0pvSEw3dm80TU9zV0c4S2dPcFZZakYrZGVDWGNrR2ZOaGt0RTIwZ1dMMmVrYVpySVpKZzlDR3ZEWW5zWmZxVDBTREE9.UF35kGReZAQzsmg8-2KwA24uBugeMnFUzTb4LsuJFcU"
      );
      $emisor["Rfc"]="LAN8507268IA";
      $emisor["Nombre"]="NombreRazonSocial";
      $emisor["RegimenFiscal"]="601";
      $receptor["Rfc"] = "AAA010101AAA";
      $receptor["Nombre"] = "NombreRazonSocial";
      $receptor["ResidenciaFiscalSpecified"] = false;
      $receptor["NumRegIdTrib"] = null;
      $receptor["UsoCFDI"] = "G03";
      $conceptos = null;
      $ImpuestosTotales = null;
      $complemento = null;
      $totalImpuestosTrasladados = 0;
      $Subtotal = 0;

      $comprobante["Version"] = "3.3";
      $comprobante["Serie"] = "A";
      $comprobante["Folio"] = "123456";
      $comprobante["Fecha"] = date('Y-m-d\TH:i:s');
      $comprobante["Moneda"] = "MXN";
      $comprobante["TipoDeComprobante"] = "I";
      $comprobante["LugarExpedicion"] = "45400";
      $comprobante["Emisor"] = $emisor;
      $comprobante["Receptor"] = $receptor;
      $comprobante["Complemento"] = $complemento;
      $comprobante["MetodoPagoSpecified"] = true;
      $comprobante["FormaPago"] = "01";
      $comprobante["MetodoPago"] = "PUE";

      for($i=0; $i<5; $i++){
          $traslado[0]["Base"] = "200.00";
          $Subtotal += (float) $traslado[0]["Base"];
          $traslado[0]["Impuesto"] = "002";
          $traslado[0]["TipoFactor"] = "Tasa";
          $traslado[0]["TasaOCuota"] = "0.160000";
          $traslado[0]["TasaOCuotaSpecified"] = true;
          $traslado[0]["Importe"] = "32.00";
          $totalImpuestosTrasladados +=(float) $traslado[0]["Importe"];
          $traslado[0]["ImporteSpecified"] = true;
          $impuesto["Traslados"] = $traslado;
          $concepto["ClaveProdServ"] = "50211503";
          $concepto["NoIdentificacion"] = "UT421511";
          $concepto["Cantidad"] = 1;
          $concepto["ClaveUnidad"] = "H87";
          $concepto["Unidad"] = "Pieza";
          $concepto["Descripcion"] = "Cigarros";
          $concepto["ValorUnitario"] = "200.00";
          $concepto["Importe"] = "200.00";

          $conceptos[$i]=$concepto;
          $conceptos[$i]["Impuestos"] = $impuesto;
      }
      $comprobante["Conceptos"] = $conceptos;
      $ImpuestosTotales["Retenciones"] = null;
      $ImpuestosTotales["Traslados"][0]["Impuesto"] = "002";
      $ImpuestosTotales["Traslados"][0]["TipoFactor"] = "Tasa";
      $ImpuestosTotales["Traslados"][0]["TasaOCuota"] = "0.160000";
      $ImpuestosTotales["Traslados"][0]["Importe"] = (string)$totalImpuestosTrasladados;
      $ImpuestosTotales["TotalImpuestosRetenidosSpecified"] = false;
      $ImpuestosTotales["TotalImpuestosTrasladados"] = (string)$totalImpuestosTrasladados;
      $ImpuestosTotales["TotalImpuestosTrasladadosSpecified"] = true;
      $comprobante["Impuestos"] = $ImpuestosTotales;


      $comprobante["SubTotal"] = (string)$Subtotal;
      $comprobante["Total"] = (string)$Subtotal + $totalImpuestosTrasladados;

      $json = json_encode($comprobante);
      //echo $json;  

      try{
    $basePath = "C:/xampp/htdocs/toktok/backend/public/uploads/facturas";
    $jsonIssuerStamp = jsonEmisionTimbrado::Set($params);
    $resultadoJson = $jsonIssuerStamp::jsonEmisionTimbradoV4($json);
    
    if($resultadoJson->status=="success"){
        //save CFDI
        $ruta=$basePath.$resultadoJson->data->uuid.".xml";
        file_put_contents($ruta, $resultadoJson->data->cfdi);
        echo $resultadoJson->data->cfdi;
        //save QRCode
        $nombreyRuta = $resultadoJson->data->uuid.".png";
        imagepng(imagecreatefromstring(base64_decode($resultadoJson->data->qrCode)), $basePath.$nombreyRuta);
    }
    else{
        //save data error
        // $ruta = $basePath."Error-".$comprobante["Serie"]."-".$comprobante["Folio"].".txt";
        // $mensaje= $resultadoJson->message."\n".$resultadoJson->messageDetail;
        // file_put_contents($ruta, $mensaje);
      return response()->json(['data' => $resultadoJson], 200);
    }
    //var_dump($resultadoJson);
}
catch(Exception $e){
    echo $e->getMessage();
}
   
    }

    public function getEstablishmentsUsers($nu){
      echo($nu);
      $emails = explode(",", $nu); 


      //$a = "1,2,3,4,5";
      //$b= preg_split("/[,]/",$nu);
      //echo($b);
      //$emails->implode($nu, ',');

      //echo($emails);

      //Mail::to('toktokdevelopment@gmail.com')->send(new TestAmazonSes('It works!'));

      /**/$data = array('name'=>"jesus","body"=>"Testsmail");
 
      Mail::send('tpl', $data, function($message)
       {
      $message->from('fetchup@fetchup.io', "Admin - ****");
      $message->subject("Alta de administrador");
      $message->to('toktokdevelopment@gmail.com');
       });

      /*foreach ($emails as $value) {
        //echo($value);
        //echo(' + ');

        $data = array('name'=>"jesus","body"=>"Testsmail");
 
      Mail::send('tpl', $data, function($message)
       {
      $message->from('fetchup@fetchup.io', "Admin - ****");
      $message->subject("Alta de administrador");
      $message->to('toktokdevelopment@gmail.com');
       });

      }*/
      //echo "El usuario no existe por lo tanto no se puede agregar";
      //print_r($emails[0]);

      /**/

      
     /* foreach ($emails as $value) {
       ///Mail::to('jzavala270591@gmail.com')->send(new TestAmazonSes('It works!'));

      $data = array('name'=>"jesus","body"=>"Testsmail");
 
      Mail::send('tpl', $data, function($message)
       {
      $message->from('fetchup@fetchup.io', "Admin - ****");
      $message->subject("Alta de administrador");
      $message->to('toktokdevelopment@gmail.com');
       });
      
       }*/
      //primero checar si ya existe en la tabla

      //echo('algo mal 32232');
      //echo($nu);
      //$correos = explode(',', $nu);
      //echo($correos);

      //$str_arr = explode (",", $nu);


      /*DB::table('user_establishments')->insert([
        'establishmentId' =>2,
        'userId' =>5
      ]);*/
      //echo($str_arr);
      //return($str_arr);
      //$users = DB::table('users')->pluck('email');
      // $exist =  DB::table('users')
      // ->whereIn('email')->get();
       //echo($users);
      //return $correos;

      /*if(!isset($exist)){

        $user = DB::table('users')
        ->join('user_roles', 'user_roles.userId', '=', 'users.userId')
        ->where('users.userId', $userId)->first();
  
  
       
        echo "id de usuario x ",$user->name;
        echo "roll del usuario x ",$user->establishmentId;

        DB::table('user_establishments')->insert([
          'establishmentId' =>$userId,
          'userId' => $user->userId,
          'rollid' => $user->establishmentId
        ]);

        }else{
          echo "El usuario no existe por lo tanto no se puede agregar";
        }*/
   }
}
