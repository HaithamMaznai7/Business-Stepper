<?php

namespace haimaz\BusinessSteper\Supports;

use Exception;
use haimaz\BusinessSteper\Interfaces\Orderable;
use haimaz\BusinessSteper\Interfaces\POSInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class POSPanel implements POSInterface
{

  public function getStepper() : SuperStepper
  {
    return new Stepper(
      $this->currentRequest()
    );
  }

  public function addRequest(){
      $request = $this->createRequest();

      $this->setCurrentRequest($request->uuid);
  }

  public function removeRequest(string $key){

      $targetRequest = $this->scopedRequests()->whereKey($key)->first();

      if($targetRequest->getKey() == $key){
          $targetRequest->delete();
          if($this->scopedRequests()->count() > 0){
              $request = $this->scopedRequests()->first();
          }else{
              $request = $this->createRequest();
          }
          $this->setCurrentRequest($request);
      }else if($targetRequest){
          $targetRequest->delete();
      }else{
          throw new Exception('The Uuid is not exists');
      }

  }

  public function getSaleables()
  {
      return $this->currentRequest()->allSaleables();
  }

  public function addSaleable(Orderable $orderable): void {

      if(! $orderable->available())
      {
          throw new NotFoundHttpException("{$orderable->getNameAttribute()} is not available currently");
      }

      if($orderable->uniqueOnSingleRequest() && $this->getSaleables()->where(function ($item) use($orderable) { return $item->is($orderable); })->first())
      {
          throw new NotFoundHttpException("{$orderable->getNameAttribute()} is added before and you can not add it more than one time in single request");
      }

      Cache::forget($this->getStepper()->primaryCacheKey());
      if($this->getSaleables()->where(function ($item) use($orderable) { return $item->is($orderable); })->first()){
          $this->getSaleables()->where(function ($item) use($orderable) { return $item->is($orderable); })->first()->pivot->increment('qty');
      }else{
          $orderable->requests()->attach($this->currentRequest()->getKey(), [
              'qty' => 1,
          ]);
      }

      $this->currentRequest()->update([
          'current_step' => $this->getStepper()->current(),
      ]);

      $this->currentRequest()->refresh();

      $this->onUpdated();
  }

  public function deleteSaleable(Orderable $orderable): void {

      if(! $this->getSaleables()->where(fn ($item) => $item->is($orderable))->first())
      {
          throw new NotFoundHttpException("{$orderable->getNameAttribute()} is actually not exists in this request");
      }

      Cache::forget($this->getStepper()->primaryCacheKey());

      if($this->getSaleables()->where(function ($item) use($orderable) { return $item->is($orderable); })->first()->pivot->qty > 1){
          $this->getSaleables()->where(function ($item) use($orderable) { return $item->is($orderable); })->first()->pivot->decrement('qty');
      }else{
          $orderable->requests()->detach($this->currentRequest()->getKey());
      }

      $this->currentRequest()->update([
          'current_step' => $this->getStepper()->current(),
      ]);

      $this->currentRequest()->refresh();

      $this->onUpdated();
  }
  public function updateValues($step, $values){

      if(! $step instanceof Step){
        $step = $this->getStepper()->find($step);
      }

      try{

          $validator = $step->getValidator($values);

          if ($validator->fails()) {
              // Throw validation exception with the validator instance
              throw new ValidationException($validator);
          }
          $data = array_merge($this->getValues() ,$validator->validated());

          $this->currentRequest()->update([
              'data' => json_encode($data),
          ]);

          Cache::forget($this->getStepper()->primaryCacheKey());

          $this->currentRequest()->refresh();

          return $validator->validated();

      }catch(Exception $e){
          throw $e;
      }finally{
          $this->onUpdated();
      }
  }
  public function getValues()
  {
    return json_decode($this->currentRequest()->data, true);
  }
}
