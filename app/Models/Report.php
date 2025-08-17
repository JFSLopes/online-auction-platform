<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'report';

    // Primary Key Name
    protected $primaryKey = 'reportid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'userwhoreported',
        'userreported',
        'content',
        'date',
        'auctionid'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
