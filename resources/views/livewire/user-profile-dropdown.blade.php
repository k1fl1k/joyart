<?php

use k1fl1k\joyart\Livewire\Actions\Logout;
use k1fl1k\joyart\Models\User;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public ?User $user = null;

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

<div class="hidden sm:flex sm:items-center sm:ms-6">
    @if ($user)
        <div class="profile-circle">
            <img src="{{ $user->avatar ?? asset('storage/images/avatar-male.png') }}"
                 alt="User Avatar">
        </div>
        <div class="username">
            <span>{{ $user->username }}</span>
            <span class="role">role: {{ $user->role }}</span>
        </div>
    @endif

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button
                class="">
                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            @if ($user)
                <x-dropdown-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link>
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </button>
            @else
                <x-dropdown-link :href="route('login')" wire:navigate>
                    {{ __('Log In') }}
                </x-dropdown-link>
            @endif
        </x-slot>
    </x-dropdown>
</div>
