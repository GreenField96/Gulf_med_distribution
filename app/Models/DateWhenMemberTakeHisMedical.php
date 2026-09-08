<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DateWhenMemberTakeHisMedical extends Model
{
    protected $table = 'dates_when_member_take_his_medicals';

    protected $fillable = [
        'member_id',
        'date_month_year',
        'is_taken',
        'confirmed_by_user_id',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }
}
