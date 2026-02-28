<?php

namespace App\Services\Business\Profile;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

class ProfileService
{
    public function updateBasicInfo(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            // avatar upload (optional)
            if (!empty($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                if (!empty($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $data['avatar'] = $data['avatar']->store("users/{$user->id}/profile", 'public');
            } else {
                unset($data['avatar']); // don't overwrite existing avatar with null
            }

            // we do NOT update email here (read-only)
            unset($data['email']);

            $user->update([
                'name'    => $data['name'],
                'phone'   => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                ...(!empty($data['avatar']) ? ['avatar' => $data['avatar']] : []),
            ]);

            return $user->fresh();
        });
    }
     public function sendResetLink(User $user): string
    {
        // returns status string: Password::RESET_LINK_SENT or error message key
        return Password::sendResetLink(['email' => $user->email]);
    }
}