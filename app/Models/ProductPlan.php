<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class ProductPlan extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = "product_plans";
    protected $fillable = [
        'uuid',
        'product_id',
        'slug',
        'price',
        'billing_cycle',
        'max_users',
        'storage_gb',
        'is_active',
        'display_order'
    ];

    public $translatedAttributes = ['name', 'description'];

    public function getMaxUsersFormattedAttribute()
    {
        return $this->max_users ?? 'Unlimited';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function planFeatures()
    {
        return $this->hasMany(ProductPlanFeature::class, 'plan_id');
    }

    public function features()
    {
        return $this->belongsToMany(ProductModuleFeature::class, 'product_plan_features', 'plan_id', 'feature_id')
            ->withPivot('is_included', 'limit_value', 'settings')
            ->withTimestamps();
    }

    public function configurations()
    {
        return $this->hasMany(ProductConfiguration::class, 'plan_id');
    }
}
