<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class Product extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'products';
    protected $fillable = [
        'uuid',
        'product_type_id',
        'product_industry_id',
        'slug',
        'is_active'
    ];

    public $translatedAttributes = ['name', 'description'];
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function productIndustry()
    {
        return $this->belongsTo(ProductIndustry::class);
    }

    public function modules()
    {
        return $this->hasMany(ProductModule::class);
    }

    public function plans()
    {
        return $this->hasMany(ProductPlan::class);
    }
}
