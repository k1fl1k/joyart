<?php

use k1fl1k\joyart\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="">
    <div class="header">
        <div class="logo"><a href="/">joyhub</a></div>
        <div class="search-bar">
            <input type="text" placeholder="Search" />
        </div>
        <div class="user-profile">
            @livewire('user-profile-dropdown')
        </div>
    </div>
</nav>
