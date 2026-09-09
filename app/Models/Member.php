<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'member_ID',
        'national_ID',
        'family_ID',
        'phone_num',
        'reference_to_pdf',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function medicalDocuments(): HasMany
    {
        return $this->hasMany(MedicalDocument::class);
    }

    public function medicationDosages(): HasMany
    {
        return $this->belongsTo(MedicationAndDosage::class, 'member_id');
    }
    public function medicationAndDosages(): HasMany
    {
        return $this->hasMany(MedicationAndDosage::class, 'member_id');
    }
    public function distributionDates(): HasMany
    {
        return $this->hasMany(DateWhenMemberTakeHisMedical::class);
    }
}
