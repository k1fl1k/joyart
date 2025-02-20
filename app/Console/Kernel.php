<?php

namespace k1fl1k\joyart\Console;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use k1fl1k\joyart\Console\Commands\FetchSafebooruCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        FetchSafebooruCommand::class, // Реєстрація кастомної команди
    ];

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

