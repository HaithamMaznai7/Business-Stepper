<?php

namespace haimaz\BusinessSteper\Supports;

use Illuminate\Contracts\Support\Arrayable;

class Requirment implements Arrayable
{
  public function __construct(
    public string $key,
    public mixed $value = null,
    public string $type = 'input',
    public array $extra = [],
    public array $rules = [],
  ) {}

  public static function fromStructure($key, $rules)
  {
    return new Requirment(
      key: $key,
      rules: $rules
    );
  }

  public function toArray()
  {
    return [
      'key' => $this->key,
      'type' => $this->type,
      'value' => $this->value,
      'extra' => $this->extra,
      'rules' => $this->rules
    ];
  }

  public static function fromArray(array $array)
  {
    return new Requirment(
      $array['key'] ,
      isset($array['value']) ? $array['value'] : null ,
      isset($array['type']) ? $array['type'] : 'input',
      isset($array['extra']) ? $array['extra'] : [] ,
      isset($array['rules']) ? $array['rules'] : [] ,
    );
  }

  public function fromData($key, $value)
  {
    return new Requirment(
      key: $key,
      value: $value,
    );
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function setRules($rules)
  {
    $this->rules = $rules;
  }

  public function build($value)
  {
    $this->value = $value;
  }

  public function render()
  {
    return $this->toArray();
  }
}
