<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait ApiResponser {

  private function successResponse($data, $code) {
    return response()->json($data, $code);
  }

  protected function errorResponse($message, $code) {
    return response()->json(['error' => $message, 'code' => $code], $code);
  }

  protected function showAll(Collection $collection, $code = 200){
    if ($collection->isEmpty()) {
      return $this->successResponse(['data' => $collection], $code);
    }

    //$collection = $this->cacheResponse($collection);

    return $this->successResponse(['data' => $collection], $code);
  }

  protected function showOne(Model $model, $code = 200){
    return $this->successResponse(['data' => $model], $code);
  }

  protected function showMessage($message, $code = 200){
    return $this->successResponse(['data' => $message], $code);
  }

  protected function cacheResponse($data) {
    $url = request()->url();
    $queryParams = request()->query();

    ksort($queryParams);

    $queryString = \http_build_query($queryParams);

    $fullUrl = '{$url}?{$queryString}';

    return Cache::remember($url, 30, function() use($data) {
      return $data;
    });
  }


}
