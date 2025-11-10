<?php

namespace haimaz\BusinessSteper\Interfaces;

use haimaz\BusinessSteper\Supports\Structure;
use Illuminate\Support\Collection;

interface StepperInterface
{
    public function beforeSteps(): Collection;

    public function afterSteps(): Collection;
    
    public function getSaleables(): Collection;

    public function getValues(): array;

    public function getBuilder(): Structure;

    public function isConfirmed(): bool;

    public function primaryCacheKey(): string;

    public function checkout(): void;
} 