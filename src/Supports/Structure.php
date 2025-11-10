<?php

namespace haimaz\BusinessSteper\Supports;

use Exception;
use haimaz\BusinessSteper\PressFileParser;
use Illuminate\Support\Collection;

class Structure
{    
    private $structure;
    
    public function __construct(
        $structure,
    ) {
        $this->build($structure);
    }

    public function fillData(array $data = [])
    {
        $this->structure = $this->structure->map(function($step) use($data) {
            $step->content = $step->content->map(function($requirement) use($data) {
                $requirement->value = isset($data[$requirement->key]) ? $data[$requirement->key] : $requirement->value;
                return $requirement;
            });

            return $step;
        });
    }

    public static function fromJsonFile($path)
    {
        if(! str_ends_with($path, '.json')){
            throw new Exception("the file path required to be json");
        }

        $pressFileParser = (new PressFileParser($path));
    
        return new Structure($pressFileParser->getData());
    }

    public static function fromJson($json)
    {    
        return new Structure(collect(
            json_decode(
                $json,
                true
            )
        ));
    }

    private function build($data)
    {
        $this->structure = collect([]);

        foreach($data as $step){

            $step = Step::fromArray(
                $step
            );

            $this->structure->add($step);
        }
    }

    public function toArray()
    {
        return $this->structure->map(function($step){
            return $step->toArray();
        })->toArray();
    }

    /**
     * @var Collection<int, Step>
     */
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