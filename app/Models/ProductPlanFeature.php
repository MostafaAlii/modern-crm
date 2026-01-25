<?php
namespace App\Models;
class ProductPlanFeature extends BaseModel {
    protected $table = "product_plan_features";
    protected $fillable = [
        'uuid',
        'plan_id',
        'feature_id',
        'is_included',
        'limit_value',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_included' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(ProductPlan::class, 'plan_id');
    }

    public function feature()
    {
        return $this->belongsTo(ProductModuleFeature::class, 'feature_id');
    }
}
