<?php

namespace k1fl1k\joyart\Console\Commands;

use Illuminate\Console\Command;
use k1fl1k\joyart\Services\SafebooruService;

class FetchSafebooruCommand extends Command
{
    protected $signature = 'fetch:safebooru {count} {userId}';// Додаємо параметр ліміту з дефолтним значенням 10
    protected $description = 'Отримати та зберегти зображення з Safebooru API';

    public function __construct(protected SafebooruService $safebooruService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $count = (int) $this->argument('count');
        $userId = (string) $this->argument('userId');

        $safebooruService = new SafebooruService();
        $safebooruService->fetchAndStoreArtworks($count, $userId);

        $this->info("{$count} artworks have been fetched and stored for user ID: {$userId}");
    }
}

