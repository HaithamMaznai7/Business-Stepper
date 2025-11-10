<?php

namespace haimaz\BusinessSteper\Supports;

use haimaz\BusinessSteper\Models\Request;
use Illuminate\Support\Collection;

class Stepper extends SuperStepper
{    
    
    public function __construct(private Request $request, private Collection|null $beforeSteps = null, private Collection|null $afterSteps = null) {}
    
    public function beforeSteps(): Collection
    {
        return $this->beforeSteps ?? collect([]);
    }

    public function afterSteps(): Collection
    {
        return $this->afterSteps ?? collect([]);
    }

    public function getSaleables(): Collection
    {
        return $this->request->allSaleables();
    }

    public function primaryCacheKey(): string
    {
        return "request-{$this->request->id}-structure";
    }

    public function isConfirmed(): bool
    {
        return count($this->requiredSteps()) == 0;
    }

    public function checkout(): void
    {
        
    }

    public function getValues(): array
    {
        return json_decode($this->request->data, true);
    }

}