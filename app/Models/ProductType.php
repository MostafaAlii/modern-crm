<?php
namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class ProductType extends BaseModel implements TranslatableContract {
    use Translatable;
    protected $table = 'product_types';
    protected $fillable = ['uuid', 'slug', 'is_active'];
    public $translatedAttributes = ['name', 'description'];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
