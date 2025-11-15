<?php

use haimaz\BusinessSteper\Supports\Helper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->nullableMorphs('tenant');
            $table->nullableMorphs('requester');
            $table->json('data');
            if(Helper::getDefaultStep() == null){
                $table->string('current_step')->nullable()->default(Helper::getDefaultStep());
            }else{
                $table->string('current_step')->default(Helper::getDefaultStep());
            }
            $table->enum('type', Helper::getBusinessTypes())->default(Helper::getDefaultBusinessType());
            $table->json('extra')->nullable();
            $table->timestamps();
        });

        Schema::create('saleables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')
                ->nullable()
                ->constrained('requests', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->morphs('saleable');
            $table->integer('qty')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saleables');
        Schema::dropIfExists('requests');
    }
};

