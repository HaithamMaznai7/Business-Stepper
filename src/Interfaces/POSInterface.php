<?php

namespace haimaz\BusinessSteper\Interfaces;

use haimaz\BusinessSteper\Models\Request;
use haimaz\BusinessSteper\Supports\Step;
use Illuminate\Support\Collection;

interface POSInterface
{
    /**
     * @var Collection<int, Step>
     */
    
    public function beforeSteps(): Collection;

    /**
     * @var Collection<int, Step>
     */

    public function afterSteps(): Collection;

    public function currentRequest();

    public function setCurrentRequest(mixed $request): void;

    public function scopedRequests(): Collection;

    public function createRequest(): Request;
    
    public function onUpdated(): void;

    public function checkout(): void;
} 