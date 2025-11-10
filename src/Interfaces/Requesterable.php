<?php

namespace haimaz\BusinessSteper\Interfaces;

use haimaz\BusinessSteper\Enums\RequestType;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Requesterable
{
  public function requests() : MorphMany;

  public function getDefaultRequestType() : RequestType;

  public function getAvailableRequestTypes() : array;

  public function getObjectType() : string;
  
  public function getObjectKey();
}
