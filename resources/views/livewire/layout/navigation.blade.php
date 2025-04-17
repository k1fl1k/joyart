<?php

use k1fl1k\joyart\Livewire\Actions\Logout;
use k1fl1k\joyart\Models\Tag;
use Livewire\Volt\Component;

new class extends Component {
    public $tags;

    public function mount()
    {
        // Завантажуємо теги
        $this->tags = Tag::whereNull('parent_id')->with('subtags')->get();
    }
}; ?>

<nav x-data="{ open: false }" class="">
    <x-header :tags="$tags"/>
</nav>
