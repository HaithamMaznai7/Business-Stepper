<?php

namespace haimaz\BusinessSteper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class PressFileParser 
{
    protected $filename;
    protected $file;
    protected Collection $data;
    
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $this->splitFile();

        $this->decodeFile();
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    private function splitFile()
    {
        $this->file = File::exists($this->filename) ? File::get($this->filename) : $this->filename;
    }

    private function decodeFile()
    {
        $this->data = collect(
            json_decode(
                $this->file,
                true
            )
        );
    }
}