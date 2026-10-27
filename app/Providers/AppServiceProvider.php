<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // 1. Mendaftarkan Policies (Acara 24 Poin 2c)
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);

        // 2. Mendefinisikan Gates (Acara 24 Poin 1a)
        // Gate sesuai modul BKPM Hal 22: 'edit-post'
        Gate::define('edit-post', function (User $user, $post) {
            $postUserId = is_object($post) ? ($post->user_id ?? null) : ($post['user_id'] ?? null);
            return $user->id === $postUserId;
        });

        // Gate spesifik domain POS Toko
        Gate::define('manage-products', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('view-profile', function (User $user, ?User $target = null) {
            return true; // Setiap user yang login bisa melihat profilnya
        });
    }
}
