<?php

namespace haimaz\BusinessSteper\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'order_no' => $this->currentRequest()->uuid,
            'request_no' => $this->currentRequest()->uuid,
            'previous_step' => $this->getStepper()->previous(),
            'step' => $this->getStepper()->current(),
            'next_step' => $this->getStepper()->next(),
            'services' => $this->getSaleables()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->getNameAttribute(),
                    'type' => $service->getDescriptionAttribute(),
                    'price' => $service->id,
                    'vat' => $service->id,
                    'discount' => $service->id,
                    'cost' => $service->id,
                ];
            })->toArray(),
            'steps' => $this->getStepper()->all()->map(function ($step){
                return [
                    'pageType' => $step->getKey(),
                    'inputs' => $step->content->map(function ($requirement) {
                        return [
                            'name' => $requirement->key,
                            'type' => $requirement->key,
                            'rules' => $requirement->key,
                            'value' => $requirement->value,
                            'validate' => $requirement->key,
                        ];
                    })->toArray(),
                    'route' => $step->key,
                ];
            })->toArray(),
            'requirements' => $this->getStepper()->required()->map(function ($step){
                return [
                    'pageType' => $step->getKey(),
                    'inputs' => $step->content->map(function ($requirement) {
                        return [
                            'name' => $requirement->key,
                            'type' => $requirement->key,
                            'rules' => $requirement->key,
                            'value' => $requirement->value,
                            'validate' => $requirement->key,
                        ];
                    })->toArray(),
                    'route' => route('package.update'),
                ];
            })->toArray(),
            'amount' => [
                'subtotal' => 0.00,
                'vat' => 0.00,
                'discount' => 0.00,
                'coupon' => 0.00,
                'total' => 0.00,
            ]
        ];
    }
}
