<?php

namespace haimaz\BusinessSteper\Models;

use haimaz\BusinessSteper\Enums\RequestType;
use haimaz\BusinessSteper\Interfaces\Requesterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Request extends Model
{

  protected $table = 'requests';

  protected $fillable = ['creator_id', 'tenant_type', 'tenant_id', 'requester_type', 'requester_id', 'data', 'current_step', 'type', 'extra'];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->uuid = Str::uuid();
      $model->data ??= json_encode([]);
    });
  }

  public function tenant() : MorphTo
  {
    return $this->morphTo('tenant', 'tenant_type', 'tenant_id');
  }

  public function requester() : MorphTo
  {
    return $this->morphTo('requester', 'requester_type', 'requester_id');
  }

  public function creator() : BelongsTo
  {

    $userModel = config('business_stepper.creator.model');
    $foreignKey = config('business_stepper.creator.foreignKey');
    $ownerKey = config('business_stepper.creator.ownerKey');

    return $this->belongsTo($userModel::class, $foreignKey, $ownerKey);
  }

  public function scopeGuest($query)
  {
    $foreignKey = config('business_stepper.creator.foreignKey');

    $query->whereNull($foreignKey)
    ->whereNull('tenant_id')
    ->whereNull('tenant_type')
    ->whereNull('requester_id')
    ->whereNull('requester_type');
  }

  public function scopeBy($query, $user = null)
  {
    $foreignKey = config('business_stepper.creator.foreignKey');
    $ownerKey = config('business_stepper.creator.ownerKey');

    $query->where($foreignKey, $user?->$ownerKey);
  }

  public function scopeOnTenant($query, Requesterable $tenant)
  {
    $query->where('tenant_type', $tenant->getObjectType())->where('tenant_id', $tenant->getObjectKey());
  }

  public function scopeFor($query, Requesterable $requester)
  {
    $query->where('requester_type', $requester->getObjectType())->where('requester_id', $requester->getObjectKey());
  }

  public function scopeIn($query, RequestType $type = RequestType::Customer)
  {
    $query->where('type', $type->businessType());
  }

  public function scopeUuid($query, $uuid = null)
  {
    $query->where('uuid', $uuid);
  }

  // public function products()
  // {
  //   return $this->morphedByMany(Product::class, 'saleable');
  // }

  public function saleables(): array
  {

    $saleables = [];

    foreach(config('business_steper.saleables') as $alias => $model){
      $saleables[$alias] = $this->morphedByMany($model, 'saleable')->withPivot(['qty']);
    }

    return $saleables;
  }

  public function allSaleables(): Collection
  {

    $saleables = $this->saleables();
    $all = collect([]);

    foreach($saleables as $alias => $model){
      $all = $all->merge($model->get());
    }

    return $all;
  }
}
