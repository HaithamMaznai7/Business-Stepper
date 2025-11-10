<?php

namespace haimaz\BusinessSteper\Interfaces;

abstract class Saleable extends Orderable
{
    public function qauntity(): int
    {
        return 10;
    }
} 