<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SubCategory extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'subcategory';

    // Primary Key Name
    protected $primaryKey = 'subcatid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'subcategoryname',
        'catid'
    ];


    // Methods (Relationships)
    
    /**
     * Get the Products associated with the subcategory
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'subcatid', 'subcatid');
    }

    /**
     * Get category associated with subcategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'catid', 'catid');
    }

    public static function findByName($name){
        return SubCategory::where('subcategoryname', $name)->first();
    }


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
