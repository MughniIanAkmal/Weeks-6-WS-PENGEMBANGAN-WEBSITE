<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar produk (Acara 24).
     */
    public function viewAny(?User $user): bool
    {
        return true; // Siapapun (termasuk tamu) bisa melihat daftar produk
    }

    /**
     * Tentukan apakah user bisa melihat detail produk tertentu.
     */
    public function view(?User $user, Product|int|string $product): bool
    {
        return true;
    }

    /**
     * Tentukan apakah user bisa membuat produk baru (Hanya Admin).
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Tentukan apakah user bisa mengupdate produk (Hanya Admin).
     */
    public function update(User $user, Product|int|string $product): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Tentukan apakah user bisa menghapus produk (Hanya Admin).
     */
    public function delete(User $user, Product|int|string $product): bool
    {
        return $user->role === 'admin';
    }
}
