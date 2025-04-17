<?php

namespace k1fl1k\joyart\Providers;

use Illuminate\Support\ServiceProvider;
use k1fl1k\joyart\App\Livewire\Profile\UpdateAvatarForm;
use k1fl1k\joyart\App\Livewire\Profile\UpdateBirthdayForm;
use Livewire\Livewire;

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
        Livewire::component('profile.update-avatar-form', UpdateAvatarForm::class);
        Livewire::component('profile.update-birthday-form', UpdateBirthdayForm::class);
    }
}
