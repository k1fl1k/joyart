<?php

namespace k1fl1k\joyart\Console\Commands;

use Illuminate\Console\Command;
use k1fl1k\joyart\Services\SafebooruService;

class FetchSafebooruCommand extends Command
{
    protected $signature = 'fetch:safebooru';
    protected $description = 'Отримати та зберегти зображення з Safebooru API';

    public function __construct(protected SafebooruService $safebooruService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Отримання даних з Safebooru API...");
        $this->safebooruService->fetchAndStoreArtworks();
        $this->info("Дані успішно отримані та збережені!");
    }
}
