<?php

namespace haimaz\BusinessSteper\Interfaces;

use haimaz\BusinessSteper\Models\Request;
use haimaz\BusinessSteper\Supports\Structure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

abstract class Orderable extends Model
{

    public function getNameAttribute() : string
    {
      return $this->name ?? 'name';
    }

    public function getDescriptionAttribute() : string
    {
      return $this->description ?? 'description';
    }

    public function available() : bool
    {
      return $this->active ?? true;
    }

    public function uniqueOnSingleRequest() : bool
    {
      return false;
    }

    public function getRequirements(): Structure
    {
      return $this->steps ?? Structure::fromArray([]);
    }

    public function requests(): MorphToMany
    {
      return $this->morphToMany(Request::class, 'saleable')->withPivot(['qty']);
    }
    public function getSaleableCost(Request $request): float
    {
      return $this->price ?? .0;
    }
    public function getSaleableDiscount(Request $request): float
    {
      return $this->discount ?? .0;
    }
    public function getSaleableQTY(Request $request): int
    {
      return $this->requests()->where('requests.id', $request->id)->first()->pivot->qty ?? 1;
    }
} 