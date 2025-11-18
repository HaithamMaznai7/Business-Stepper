<?php

namespace haimaz\BusinessSteper\Supports;

use Exception;
use haimaz\BusinessSteper\PressFileParser;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Structure implements Arrayable
{
  private $structure;
  public function __construct(array $structure)
  {
    $this->structure = $this->build($structure);
  }

  public static function fromJsonFile($path): Structure
  {
    if(! str_ends_with($path, '.json')){
        throw new Exception("the file path required to be json");
    }

    $pressFileParser = (new PressFileParser($path));

    return new Structure($pressFileParser->getData()->toArray());
  }

  public static function fromJson($json): Structure
  {
    return self::fromArray(
      json_decode(
          $json,
          true
      )
    );
  }

  public static function fromArray($array): Structure
  {
    return new Structure(
      $array
    );
  }

  public function toJson()
  {
    return json_encode(
      $this->structure->map( function (Step $step) {
        return $step->toArray();
      })->toArray()
    );
  }

  protected function build($data){
    $structure = collect([]);

    foreach($data as $step){
      $step = Step::fromArray(
          $step
      );

      $structure->add($step);
    }

    return $structure;
  }

  public function toArray()
  {
      return $this->structure->map(function($step){
          return $step->toArray();
      })->toArray();
  }

  public function getSteps(): Collection
  {
    return $this->structure;
  }

  public function stepByKey($key)
  {
    return $this->structure->where(function($step) use($key) {
        return $step->key == $key;
    })->first();
  }
}
