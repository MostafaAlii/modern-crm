<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class ProductModuleFeature extends BaseModel implements TranslatableContract
{
    use Translatable;
    protected $table = "product_module_features";
    protected $fillable = [
        'uuid',
        'module_id',
        'slug',
        'feature_type',
        'settings',
        'is_enabled'
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function module()
    {
        return $this->belongsTo(ProductModule::class, 'module_id');
    }

    public function planFeatures()
    {
        return $this->hasMany(ProductPlanFeature::class, 'feature_id');
    }

    public function plans()
    {
        return $this->belongsToMany(ProductPlan::class, 'product_plan_features', 'feature_id', 'plan_id')
            ->withPivot('is_included', 'limit_value', 'settings')
            ->withTimestamps();
    }
}
