<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnblockRequest extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'unblockrequest';

    // Primary Key Name
    protected $primaryKey = 'unblockid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'userid',
        'content',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function user() : BelongsTo{
        return $this->belongsTo(Users::class, 'userid', 'userid');
    }


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
