<?php

use k1fl1k\joyart\Livewire\Actions\Logout;
use k1fl1k\joyart\Models\Tag;
use Livewire\Volt\Component;

new class extends Component {
    public $tags;

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function mount()
    {
        // Завантажуємо теги
        $this->tags = Tag::whereNull('parent_id')->with('subtags')->get();
    }
}; ?>

<nav x-data="{ open: false }" class="">
    <x-header :tags="$tags"/>
</nav>
