<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Otorisasi update post berdasarkan kepemilikan user_id (Sesuai BKPM Acara 24 Hal. 24).
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
