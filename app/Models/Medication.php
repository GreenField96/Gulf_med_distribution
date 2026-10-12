<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    protected $fillable = [
        'medical_name',
        'dosages',
        'price',
    ];

    public function memberDosages(): HasMany
    {
        return $this->hasMany(MedicationAndDosage::class);
    }
}
