<?php

namespace haimaz\BusinessSteper\Interfaces;

use Carbon\Carbon;

abstract class Subscribeable extends Orderable
{
    public function range() : Carbon
    {
        return now()->addMonth();
    }
} 