<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'category';

    // Primary Key Name
    protected $primaryKey = 'catid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'categoryname'
    ];



    // Methods (Relationships)

    /**
     * Get all subcategories of a category
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'catid', 'catid');
    }
    


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
