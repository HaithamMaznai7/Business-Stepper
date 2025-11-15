<?php

namespace haimaz\BusinessSteper\Supports;

use haimaz\BusinessSteper\Interfaces\StepperInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

abstract class SuperStepper implements StepperInterface
{    

    /**
     * @var Collection<int, Step>
     */
    public function all()
    {
        return $this->getBuilder()->getSteps();
    }

    /**
     * @var Collection<int, Step>
     */
    public function required()
    {
        return $this->getBuilder()->getSteps()->where(
            fn ($step) => $step->isRequired()
        );
    }

    /**
     * @var Collection<int, Step>
     */
    public function passed()
    {
        return $this->getBuilder()->getSteps()->where(
            fn ($step) => ! $step->isRequired()
        );
    }

    /**
     * @var Step
     */
    public function find($key)
    {
        return $this->getBuilder()->getSteps()->where(
            fn ($step) => $step->getKey() == $key
        )->first();
    }

    public function current()
    {
        return $this->required()->first()?->getKey() ?? Helper::getDefaultStep();
    }

    public function next()
    {
        $steps = $this->required()->where( function(Step $step){
            return $step->getKey() != $this->current();
        });

        return $steps->first()?->getKey();
    }
  
    public function previous()
    {
        return $this->passed()->last()?->getKey();
    }

    public function steps()
    {
        return $this->all()->map(fn ($step) => $step->getKey())->toArray();
    }

    public function requiredSteps()
    {
        return $this->required()->map(fn ($step) => $step->getKey())->toArray();
    }

    public function passedSteps()
    {
        return $this->passed()->map(fn ($step) => $step->getKey())->toArray();
    }

    public function getBuilder(): Structure
    {
        return Cache::rememberForever($this->primaryCacheKey(), function () {      
            $structure = collect([]);

            foreach($this->beforeSteps() as $step){
                $structure->add($step);
            }

            foreach($this->getSaleables() as $saleable){
                foreach($saleable->getRequirements()->getSteps() as $step){
                    if($structure->where(function($i) use($step) {return $i->key == $step->key;})->first()){
                        $structure = $structure->map(function($item) use($step){
                            if(isset($step->key) && isset($item->key) && $step->key === $item->key){                            
                                $item->title = $step->title;
                                $item->subtitle = $step->subtitle != $item->subtitle ? $item->subtitle . '&' . $step->subtitle : $step->subtitle;
                                $item->repeatable = $item->repeatable || $step->repeatable;
                                $item->repeatName = $step->repeatName ?? $item->repeatName ?? null;
                                foreach($step->content as $field){
                                    if($item->content->where(function($i) use($field) {return $i->key == $field->key;})->first()){                 
                                            $item->content = $item->content->map(function ($requirment) use($field){
                                                if($requirment->key == $field->key){
                                                    $requirment->value = $field->value ?? $requirment->value;
                                                    foreach($field->rules as $rule){
                                                        if(! in_array($rule, $requirment->rules)){
                                                            $requirment->rules[] = $rule;
                                                        }
                                                    }
                                                }
                                                
                                                return $requirment;
                                            });
                                    }else{
                                        $item->content->add($field);
                                    }
                                }
                                
                                
                            }

                            return $item;
                        });
                    }else{
                        $structure->add($step);
                    }
                }
            }

            foreach($this->afterSteps() as $step){
                $structure->add($step);
            }

            $structure = new Structure(
                $structure->map(function($item){
                return $item->toArray();
                })->toArray()
            );

            $structure->fillData($this->getValues());

            return $structure;
        });
    }

    public function isConfirmed(): bool
    {
        return count($this->requiredSteps()) == 0;
    }
}