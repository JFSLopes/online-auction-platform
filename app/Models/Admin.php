<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'admin';

    // Primary Key Name
    protected $primaryKey = 'adminid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'adminlevel',
        'uid'
    ];


    /**
     * Get the id of the user corresponding to the admin ID
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'uid', 'userid');
    }

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
