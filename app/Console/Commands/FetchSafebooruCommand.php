<?php

namespace k1fl1k\joyart\Console\Commands;

use Illuminate\Console\Command;
use k1fl1k\joyart\Services\SafebooruService;

class FetchSafebooruCommand extends Command
{
    protected $signature = 'fetch:safebooru {limit=100}'; // Додаємо параметр ліміту з дефолтним значенням 10
    protected $description = 'Отримати та зберегти зображення з Safebooru API';

    public function __construct(protected SafebooruService $safebooruService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $limit = (int) $this->argument('limit'); // Отримуємо переданий параметр
        $this->info("Отримання {$limit} зображень з Safebooru API...");
        $this->safebooruService->fetchAndStoreArtworks($limit);
        $this->info("Дані успішно отримані та збережені!");
    }
}

