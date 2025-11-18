<?php

namespace haimaz\BusinessSteper\Interfaces;

use haimaz\BusinessSteper\Supports\Requirment;
use haimaz\BusinessSteper\Supports\Step;
use Illuminate\Support\Collection;

interface StepperInterface
{
    public function afterSteps(): Collection;
    public function beforeSteps(): Collection;
    public function buildSteps(Step $step): Step;
    public function buildRequirments(Requirment $requirment): Requirment;
    public function getSaleables(): Collection;
    public function getValues(): array;
    public function primaryCacheKey(): string;
}
