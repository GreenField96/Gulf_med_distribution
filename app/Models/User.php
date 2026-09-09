<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_num',
        'company_id',
        'role',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship with Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Role Helper Methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin_user';
    }

    public function isMedicalOperator(): bool
    {
        return $this->role === 'medical_distro_operator_user';
    }

    public function isCompanyMember(): bool
    {
        return $this->role === 'companies_members';
    }
}