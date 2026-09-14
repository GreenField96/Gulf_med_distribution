<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicationAndDosage extends Model
{
    protected $table = 'medications_and_dosages';
    use HasFactory;
    protected static function booted(): void
    {
        // Triggers when a new medication record is created
        static::created(function ($medicationAndDosage) {
            $medicationAndDosage->member?->update(['is_locked' => 0]);
        });

        // Triggers when an existing medication record is updated
        static::updated(function ($medicationAndDosage) {
            $medicationAndDosage->member?->update(['is_locked' => 0]);
        });

        // Triggers when a medication record is deleted
        static::deleted(function ($medicationAndDosage) {
            $medicationAndDosage->member?->update(['is_locked' => 0]);
        });
    }
    protected $fillable = [
        'medication_id',
        'member_id',
        'amount',
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

