<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;

class MedicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Medication $medication): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMedicalOperator();
    }

    public function update(User $user, Medication $medication): bool
    {
        return $user->isAdmin() || $user->isMedicalOperator();
    }

    public function delete(User $user, Medication $medication): bool
    {
        return $user->isAdmin();
    }
}