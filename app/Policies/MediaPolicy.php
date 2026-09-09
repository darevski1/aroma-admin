<?php

namespace App\Policies;

use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Media $media): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Media $media): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Media $media): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Media $media): bool
    {
        return false;
    }

    public function replicate(User $user, Media $media): bool
    {
        return $user->isAdmin();
    }
}
