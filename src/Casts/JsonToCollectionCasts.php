<?php

namespace haimaz\BusinessSteper\Casts;

use App\Support\VehicleInfo;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class JsonToCollectionCasts implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
      return collect(json_decode($value, true));
    }
    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
      if($value instanceof Collection){
        $array = $value->toArray();
      }else if(is_array($value)){
        $array = $value;
      }

      return [
        $key => json_encode($array)
      ];
    }
}
