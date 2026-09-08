<?php
namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Admins, Operators, and Company Users can view members.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Member $member): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        // Company members can only view members of their own company
        return $user->company_id === $member->company_id;
    }

    /**
     * Admins and Company Users can create members.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCompanyMember();
    }

    /**
     * Admins and Company Users can edit members (upload new PDFs).
     */
    public function update(User $user, Member $member): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCompanyMember()) {
            return $user->company_id === $member->company_id;
        }

        return false;
    }

    /**
     * Only admins can delete members.
     */
    public function delete(User $user, Member $member): bool
    {
        return $user->isAdmin();
    }
}