<?php

namespace haimaz\BusinessSteper\Supports;

use haimaz\BusinessSteper\Interfaces\StepperInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

abstract class SuperStepper implements StepperInterface, Arrayable
{
  /**
   * @var Collection<int, Step>
   */
  public function all()
  {
    return $this->getStructure()->map(callback: fn ($step) => $this->buildSteps($step));
  }

  /**
   * @var Collection<int, Step>
   */
  public function required()
  {
    return $this->all()->where(
      fn ($step) => $step->isRequired()
    )->map(fn ($step) => $this->buildSteps($step));
  }

  /**
   * @var Collection<int, Step>
   */
  public function passed()
  {
    return $this->all()->where(
        fn ($step) => ! $step->isRequired()
    )->map(fn ($step) => $this->buildSteps($step));
  }

  /**
   * @var Step
   */
  public function find($key)
  {
    return $this->getStructure()->where(
        fn ($step) => $step->getKey() == $key
    )->first();
  }

  public function current(bool $full = false)
  {
    if($full){
      return $this->required()->first();
    }

    return $this->required()->first()?->getKey() ?? Helper::getDefaultStep();
  }

  public function next(bool $full = false)
  {
    $steps = $this->required()->where( function(Step $step){
        return $step->getKey() != $this->current();
    });

    if($full){
      return $steps->first();
    }

    return $steps->first()?->getKey();
  }

  public function previous(bool $full = false)
  {
    if($full){
      return $this->passed()->last();
    }

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

  private function getStructure(): Collection
  {
    return Cache::rememberForever($this->primaryCacheKey(), function () {

      $steps = collect([]);


      foreach($this->getSaleables() as $saleable){
        foreach($saleable->getRequirements()->getSteps() as $step){
          if(! $steps->where(fn($i) => $i->key == $step->key)->first()){
            $steps->add($step);
          }else{
            $steps->filter(fn($i) => $i->key == $step->key)->map(function ($item) use ($step) {
              $item->title = $step->title;
              $item->subtitle = $step->subtitle != $item->subtitle ? $item->subtitle . '&' . $step->subtitle : $step->subtitle;
              $item->repeatable = $item->repeatable || $step->repeatable;
              $item->repeatName = $step->repeatName ?? $item->repeatName ?? null;
              foreach($step->content as $field){
                if(! $item->content->where(fn($i) => $i->key == $field->key)->first()){
                  $item->content->add($field);
                }else{
                  $item->content->filter(fn($i) => $i->key == $field->key)->map( function ($requirment) use($field) {
                    foreach($field->rules as $rule){
                      if(! in_array($rule, $requirment->rules)){
                        $requirment->rules[] = $rule;
                      }
                    }
                  });
                }
              }

              return $item;
            });
          }

        }
      }

      $structure = collect([]);

      $structure = $structure->merge($this->beforeSteps());

      $structure = $structure->merge($steps);

      $structure = $structure->merge($this->afterSteps());

      return $structure;
    });
  }

  public function buildSteps(Step $step): Step
  {
    $step->content->map(fn (Requirment $requirment) => $this->buildRequirments($requirment));

    return $step;
  }

  public function buildRequirments(Requirment $requirment): Requirment
  {
    return $requirment;
  }

  public function getValues(): array
  {
    return [];
  }

  public function getSaleables(): Collection
  {
    return collect([]);
  }

  public function isConfirmed(): bool
  {
      return count($this->requiredSteps()) == 0;
  }
}
