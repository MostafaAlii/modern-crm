<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class ProductModule extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = "product_modules";
    protected $fillable = [
        'uuid',
        'product_id',
        'slug',
        'is_enabled',
        'display_order'
    ];

    public $translatedAttributes = ['name', 'description'];
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function features() {
        return $this->hasMany(ProductModuleFeature::class, 'module_id');
    }
}