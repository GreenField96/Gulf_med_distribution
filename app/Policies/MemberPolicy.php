<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Admin and Medical Operators can view any member.
     * Company Members can only view members belonging to their company.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Member $member): bool
    {
        if ($user->isAdmin() || $user->isMedicalOperator()) {
            return true;
        }

        return $user->company_id === $member->company_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMedicalOperator() || $user->isCompanyMember();
    }

    public function update(User $user, Member $member): bool
    {
        if ($user->isAdmin() || $user->isMedicalOperator()) {
            return true;
        }

        return $user->company_id === $member->company_id;
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->isAdmin();
    }
}