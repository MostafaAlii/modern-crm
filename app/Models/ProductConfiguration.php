<?php
namespace App\Models;
class ProductConfiguration extends BaseModel {
    protected $fillable = [
        'uuid',
        'plan_id',
        'config_key',
        'config_value',
        'category'
    ];

    public function plan()
    {
        return $this->belongsTo(ProductPlan::class, 'plan_id');
    }
}
