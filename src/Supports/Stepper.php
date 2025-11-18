<?php

namespace haimaz\BusinessSteper\Supports;

use haimaz\BusinessSteper\Models\Request;
use Illuminate\Support\Collection;

class Stepper extends SuperStepper
{
  public function __construct(protected Request $request) {}
  public function primaryCacheKey(): string
  {
    return "request_{$this->request->uuid}_structure";
  }

  public function beforeSteps(): Collection
  {
    return collect([]);
  }

  public function afterSteps(): Collection
  {
    return collect([]);
  }

  public function getSaleables(): Collection
  {
    return $this->request->allSaleables();
  }

  public function getValues(): array
  {
    return json_decode($this->request->data, true);
  }

  public function toArray()
  {
    return [];
    // return $this->all()->map(function ($step) {
    //   return $step->toArray();
    // })->toArray();
  }

  public function checkout(): void
  {

  }
}
