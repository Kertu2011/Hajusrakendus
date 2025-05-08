<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'description',
    ];

    // Kui soovid, et created_at ja updated_at oleksid teise nimega JSON vastustes
    // (pole rangelt vajalik, kui lepid created_at/updated_at nimedega)
    // const CREATED_AT = 'added';
    // const UPDATED_AT = 'edited';

    // Andmetüüpide teisendamine (hea praktika)
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        // Kui kasutaksid 'added' ja 'edited' nimedega veerge:
        // 'added' => 'datetime',
        // 'edited' => 'datetime',
    ];
}