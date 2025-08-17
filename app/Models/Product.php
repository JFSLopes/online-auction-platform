<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'product';

    // Primary Key Name
    protected $primaryKey = 'productid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'title',
        'description',
        'state',
        'authid',
        'subcatid'
    ];

    // Atributtes that must be hidden for serialization
    protected $hidden = [
        'tsvectors'
    ];


    // Methods (Relationships)

    /**
     * Get auction associated with product
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'productid', 'productid');
    }

    /**
     * Get user associated with product
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authid', 'authid');
    }

    /**
     * Get the product images
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'productid', 'productid');
    }

    /**
     * Get subcategory associated with a product
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcatid', 'subcatid');
    }



    // Methods (Not relationships)

    /**
     * Get all the possible states of a product
     */
    public static function getProductConditions()
    {
        $conditions = DB::select("SELECT unnest(enum_range(NULL::ProductState)) AS condition");

        // Extract only the strings from the results
        $conditionStrings = array_map(function ($row) {
            return $row->condition;
        }, $conditions);

        return array_values($conditionStrings);
    }



    // Methods (Not Relationships)

    /**
     * Get all auction after a full text search
     */
    public static function fullTextSearch($query){
        return Product::query()
            ->select('product.*', 'auction.*')
            ->join('auction', 'product.productid', '=', 'auction.productid')
            ->whereRaw("product.tsvectors @@ to_tsquery('english', ?)", [$query]) // Perform full-text search
            ->orderByRaw("ts_rank(product.tsvectors, to_tsquery('english', ?)) DESC", [$query]) // Order by relevance
            ->get();
    }

    /**
     * Get all the auctions after an exact match
     */
    public static function exactMatch($query){
        return Product::query()
        ->select('product.*', 'auction.*')
        ->join('auction', 'product.productid', '=', 'auction.productid')
        ->where('product.title', 'like', '%' . $query . '%')
        ->orWhere('product.description', 'like', '%' . $query . '%')
        ->get();
    }

    /**
     * Returns the auction and the products info
     */
    public static function getAllAuctionsAndProducts(){
        return Product::query()
            ->select('product.*', 'auction.*')
            ->join('auction', 'product.productid', '=', 'auction.productid')
            ->get();
    }

    public static function getAllUnfinishedAuctionsAndProducts(){
        return Product::query()
            ->select('product.*', 'auction.*')
            ->join('auction', 'product.productid', '=', 'auction.productid')
            ->where('auction.closedate', '>', now())
            ->get();
    }

    public static function getMainImage($productid){

        $image = DB::select('SELECT * FROM image WHERE productId = ?', [$productid]);

        if($image != null){
            return $image[0]->image;
        }
    }

    public static function getImages($productid){
        // Define the directory path
        $directory = public_path("images/items/");
        
        $pattern = $directory . "{$productid}-*.*"; // Matches "<productid>-<anynumber>.<anyextension>"
        $files = glob($pattern);

        // Trim the path to start from /public
        $publicPath = public_path();
        $relativePaths = array_map(function($file) use ($publicPath) {
            return str_replace($publicPath, '', $file);
        }, $files);

        // Return the paths starting from /public
        return $relativePaths;
    }
        
        // Don't add create and update timestamps in database.
        public $timestamps  = false;
}