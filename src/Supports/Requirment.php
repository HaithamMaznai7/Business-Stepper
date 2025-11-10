<?php

namespace haimaz\BusinessSteper\Supports;

class Requirment
{
    public function __construct(
        public string $key,
        public $value,
        public array $rules = []
    ) {}

    public static function fromStructure($key, $rules)
    {
        return new Requirment(
            $key,
            null, 
            $rules
        );
    }

    public function toArray()
    {
        return [
            'key' => $this->key,
            'value' => $this->value, 
            'rules' => $this->rules
        ];
    }

    public function fromData($key, $value)
    {
        $requirment = new Requirment(
            $key,
            $value, 
            []
        );

        // $requirment->setRules();

        return $requirment;
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
}