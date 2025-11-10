<?php
// src/Traits/RequesterableTrait.php
namespace haimaz\BusinessSteper\Traits;

use haimaz\BusinessSteper\Enums\RequestType;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait RequesterableTrait
{
  public function requests() : MorphMany
  {
    return $this->morphMany('');
  }

  public function getDefaultRequestType() : RequestType
  {
    return RequestType::Customer;
  }

  public function getAvailableRequestTypes() : array
  {
    return [
        RequestType::Customer
    ];
  }

  public function getObjectType() : string
  {
    return static::class;
  }
  
  public function getObjectKey()
  {
    return $this->getKey();
  }
}
