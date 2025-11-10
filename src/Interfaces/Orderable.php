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
        return ' <Name> ';
    }
    public function getDescriptionAttribute() : string
    {
        return ' - Description';
    }

    public function available() : bool
    {
        return true;
    }

    public function uniqueOnSingleRequest() : bool
    {
        return false;
    }

    public function getRequirements(): Structure
    {
        return new Structure([]);
    }

    public function requests(): MorphToMany
    {
        return $this->morphToMany(Request::class, 'saleable')->withPivot(['qty']);
    }

    public function getSaleableCost(Request $request): float
    {
        return .0;
    }
} 