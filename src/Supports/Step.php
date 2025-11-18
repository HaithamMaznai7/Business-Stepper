<?php

namespace haimaz\BusinessSteper\Supports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator;

class Step implements Arrayable
{
    public $content;

    public function __construct(
        public string $key,
        public string $title,
        public string $subtitle,
        public bool $repeatable = false,
        public null|string $repeatName = null,
        array $requirements = [],
    ) {

        if($this->repeatable && is_null($this->repeatName)){
            $this->repeatName = 'items';
        }

        $this->build($requirements);
    }

    public function toArray()
    {
        return [
            "key" => $this->key,
            "title" => $this->title,
            "subtitle" => $this->subtitle,
            "repeatable" => $this->repeatable,
            "repeat-name"=> $this->repeatName,
            "requirements" => $this->content->map(function($item){
                return $item->toArray();
            })->toArray()
        ];
    }

    public function requirementsToArray()
    {
        $data = [];

        // foreach($this->requirements as $requirement)
        // {
        //     $data[$requirement->getName()] = $requirement->getRules();
        // }

        return $data;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public static function fromJson($json)
    {
        return self::fromArray(json_decode($json, true));
    }

    public static function fromArray(array $array)
    {
      return new Step(
          $array["key"],
          $array["title"],
          $array["subtitle"],
          $array["repeatable"],
          $array["repeat-name"] ?? null,
          $array["requirements"],
      );
    }

    public function getValues()
    {

        $values = [];
        foreach($this->content as $requirment){
            $values[$requirment->key] = $requirment->value;
        }

        return $values;
    }

    public function isRequired(): bool
    {
        return ! $this->isValidated();
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getInputs()
    {
        return $this->content->map(function ($item) {
            return $item->key;
        })->toArray();
    }

    public function getRules()
    {
        $rules = [];
        $inputs = $this->getInputs();

        foreach($inputs as $input){
            $inputRules = $this->content->where(function($item) use($input) {
                return $item->key == $input;
            })->first()->rules;

            foreach($inputRules as $index => $rule){
              if(str_contains($rule, 'App\\Rules')){
                  $inputRules[$index] = new $rule();
              }else{
                  $inputRules[$index] = $rule;
              }
            }
            $rules[$input] = $inputRules;
        }

        return $rules;
    }

    public function getRequiredInputs()
    {
      $rules = [];
      $inputs = $this->getInputs();

      foreach($inputs as $input){
        $inputRules = $this->content->where(function($item) use($input) {
          return $item->key == $input;
        })->first()->rules;

        $requiredInput = [];

        foreach($inputRules as $index => $rule){
          if(str_contains($rule, 'required')){
            $requiredInput[] = $rule;
          }
        }

        $rules[$input] = $requiredInput;
      }

      return $rules;
    }

    public function getData()
    {
        $values = [];
        $inputs = $this->getInputs();

        foreach($inputs as $input){
            $values[$input] = $this->content->where(function($item) use($input) {
                return $item->key == $input;
            })->first()->value;
        }

        return $values;
    }

    public function getValidator(array|null $data = null)
    {
        $data ??= $this->getValues();
        return Validator::make($data, $this->getRules());
    }

    public function isValidated(array|null $inputs = null)
    {
        $data = [];

        $inputs ??= $this->getValues();

        foreach($inputs as $input => $value){

            if(in_array($input, $this->getInputs())){
                $data[$input] = $value;
            }
        }


        $validator = $this->getValidator(
            $data
        );

        return !$validator->fails();
    }

    public function build($requirements)
    {
      $this->content = collect([]);

      foreach($requirements as $data){
        if(! is_array($data)){
          dd($data);
        }
        
        $requirment = Requirment::fromArray($data);

        $this->content->add($requirment);

      }
    }
}
