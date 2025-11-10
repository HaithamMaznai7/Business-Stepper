<?php

namespace haimaz\BusinessSteper\Tests\Feature;

use Exception;
use haimaz\BusinessSteper\Models\Service;
use haimaz\BusinessSteper\PressFileParser;
use haimaz\BusinessSteper\Supports\GuestApiPOS;
use haimaz\BusinessSteper\Supports\POSPanel;
use haimaz\BusinessSteper\Supports\Structure;
use haimaz\BusinessSteper\Tests\TestCase;
use Illuminate\Support\Arr;

class MarkdownTest extends TestCase
{

    /** @test */
    public function test_requirements_from_json_file()
    {

        // $this->add_services();

        // $services = Service::all();

        $response = $this->getJson('/api/package/orders', );
        
        $data = json_decode($response->getContent(), true);

        $firstOrder = $data['order_no'];

        // foreach($services as $service){
        //     $this->assertDatabaseHas('services',['name' => $service->name]);
        //     $response = $this->postJson('/api/package/orders/' . $service->id , ['current_request' => $firstOrder]);
        //     $data = json_decode($response->getContent(), true);
        // }
        
        $steps = collect($data['steps']);
        $currentStep = $data['step'];
        $nextStep = $data['next_step'];

        if($currentStep != null){
            do{
                try{
                    $data = ['current_request' => $firstOrder];
                    $inputs = collect($steps->where('pageType', $currentStep)->first()['inputs']);
                    
                    foreach($inputs as $input){
                        $data[$input['name']] = 'default';
                    }

                    $response = $this->postJson('/api/package/orders', $data);
                    $data = json_decode($response->getContent(), true);
                    dd($data);
                    $response = $this->getJson('/api/package/orders');
        
                    $data = json_decode($response->getContent(), true);

                    $steps = collect($data['steps']);
                    $currentStep = $data['step'];
                    $nextStep = $data['next_step'];
                    
                }catch(Exception $e){
                    dd($e);
                }
            }while($nextStep != null);
        }

        $response = $this->getJson('/api/package/orders', );
        
        $data = json_decode($response->getContent(), true);
        
        dd($data);
        // $secondOrder = $data['order_no'];

        // dd($firstOrder, $data);

        // $pos = app()->make(POSPanel::class, ['type' => 'b2c']);
        
        // $this->assertInstanceOf(GuestApiPOS::class, $pos);

        

        // $expected = [];
        // $actual = $pos->getValues();

        // foreach($pos->getStepper()->all() as $step){
        //     foreach($step->getInputs() as $input){
        //         $this->assertArrayHasKey($input, $actual);
        //         $expected[$input] = 'default';
        //     }
        // }

        // $this->assertEquals($expected, $actual);   
    }

    public function getStructure($serviceSteps){
        
        $pressFileParser = (new PressFileParser(__DIR__. '/../data/'. $serviceSteps));
        
        return $pressFileParser->getData();       

    }

    public function getSteps($serviceSteps)
    {
        
        $requirements = $this->getStructure($serviceSteps);
        
        foreach($requirements as $step){
            $this->assertArrayHasKey('key', $step);
            $this->assertArrayHasKey('title', $step);
            $this->assertArrayHasKey('subtitle', $step);
            $this->assertArrayHasKey('requirements', $step);
            $this->assertArrayHasKey('repeatable', $step);
            
            if($step['repeatable']){
                $this->assertArrayHasKey('repeat-name', $step);
            }
        }
                
        return $requirements->map(function ($step) {
            return [
               'value' => $step['key'],
               'label' => $step['title'],
               'description' => $step['subtitle'],
            ];
        });
    }
    
    /** @test */
    // public function add_services()
    // {
        
    //     $data = config('test.services');
    //     $this->assertIsArray($data);
    //     $this->assertEquals(3, count($data));
        
    //     foreach($data as $item){
            
    //         $steps = Structure::fromJsonFile(__DIR__. '/../../Tests/data/'. $item['steps_path'] )->getSteps();
            
    //         $item = array_merge($item, [
    //             'structure' => json_encode($steps->map(function($step){
    //                 return $step->toArray();
    //             })->toArray())
    //         ]);
            
    //         $service = Service::create(
    //         Arr::only($item, [
    //             'name', 'description', 'structure'
    //             ])
    //         );
                
    //         $actualValue = $service->getRequirements()
    //             ->getSteps()->map(function($step){
    //                 return $step->toArray();
    //             });

    //         $structure = $this->getStructure($item['steps']);
            
    //         $this->assertEquals($structure->count(),$actualValue->count());

    //         foreach($actualValue as $key => $step){
    //             $this->assertArrayHasKey('key', $step);
    //             $this->assertArrayHasKey('title', $step);
    //             $this->assertArrayHasKey('subtitle', $step);
    //             $this->assertArrayHasKey('repeatable', $step);
    //             $this->assertArrayHasKey('repeat-name', $step);
    //             $this->assertArrayHasKey('requirements', $step);
    //             $this->assertEquals(count($structure[$key]['requirements']),count($step['requirements']));
                
    //             foreach($step['requirements'] as $id => $requirement){             
    //                 $this->assertArrayHasKey('key', $requirement);
    //                 $this->assertArrayHasKey('value', $requirement);
    //                 $this->assertArrayHasKey('rules', $requirement);
    //                 $this->assertEquals(count($structure[$key]['requirements'][$id]['rules']),count($requirement['rules']));
    //             }
    //         }

    //         $this->assertDatabaseHas('services', ['name' => $item['name'] ]);
    //     }
    // }

    /** @test */
    public function create_request()
    {
        $pos = app()->make(POSPanel::class, ['type' => 'b2c']);
        
        if($pos->currentRequest()->pos_type && $pos->currentRequest()->pos_id){
            $uuid = $pos->currentRequest()->uuid;
            
            $this->assertEquals($uuid, $pos->currentRequest()->uuid);
            
            $this->assertEquals(1, $pos->scopedRequests()->count());
            
            $this->assertNotEquals($uuid, $pos->currentRequest()->uuid);
            
            $pos->setCurrentRequest($uuid);
            
            $this->assertEquals($uuid, $pos->currentRequest()->uuid);
            
            $pos->removeRequest($uuid);
            
            $this->assertNotEquals($uuid, $pos->currentRequest()->uuid);
            
            $this->assertEquals(1, $pos->scopedRequests()->count());
        }else{
            $this->assertEquals(1, $pos->scopedRequests()->count());
        }
    }
   
}