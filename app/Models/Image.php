<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'image';

    // Primary Key Name
    protected $primaryKey = 'imageid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'image',
        'productid'
    ];


    // Methods (Relationships)

    /**
     * Get the product associated with image
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productid', 'productid');
    }
    

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
