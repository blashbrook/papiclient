<?php

namespace Blashbrook\PAPIClient\Console\Commands;

use Blashbrook\PAPIClient\Services\PatronCodeService;
use Illuminate\Console\Command;

class UpdatePatronCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'papi:fetch-patroncodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches Patron Codes from Polaris API and populates the local Database.';

    /**
     * The PatronCodeFetcher service instance.
     *
     * @var PatronCodeService
     */
    protected PatronCodeService $patronCodeFetcher;

    /**
     * Create a new command instance.
     *
     * The service is injected automatically by Laravel's service container.
     *
     * @param  PatronCodeService  $patronCodeFetcher
     * @return void
     */
    public function __construct(PatronCodeService $patronCodeFetcher)
    {
        parent::__construct();
        $this->patronCodeFetcher = $patronCodeFetcher;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data fetch from external API...');

        /*           try {*/
        // Call the service to perform the core logic.
        $this->patronCodeFetcher->fetch();

        $this->info('Successfully imported Patron Codes from Polaris.');

        return Command::SUCCESS;
    }
}
