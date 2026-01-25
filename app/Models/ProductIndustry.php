<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class ProductIndustry extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'product_industries';
    protected $fillable = ['uuid', 'slug', 'is_active'];
    public $translatedAttributes = ['name', 'description'];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
