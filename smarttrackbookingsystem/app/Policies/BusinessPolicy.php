<?php
// app/Policies/BusinessPolicy.php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    /**
     * Determine if the user can update the business.
     */
    public function update(User $user, Business $business): bool
    {
        // Check if user is super admin (org_admin)
        if ($user->user_type === 'org_admin') {
            return true;
        }
        
        // Check if user is an admin of this business
        // Fix: Specify the table for the status column to avoid ambiguity
        return $business->admins()
            ->where('user_id', $user->id)
            ->where('business_admins.status', 'active') // Specify the table name
            ->exists();
    }

    /**
     * Determine if the user can view business settings.
     */
    public function viewSettings(User $user, Business $business): bool
    {
        return $this->update($user, $business);
    }
}