<?php

namespace Blashbrook\PAPIClient\Console\Commands;

use Blashbrook\PAPIClient\Services\PatronStatClassCodeService;
use Illuminate\Console\Command;

// Import the new service class

class UpdatePatronStatCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'papi:fetch-patronstatclasscodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches Patron Statistical Class Codes from Polaris and populates the local Database.';

    /**
     * The ApiDataFetcher service instance.
     *
     * @var PatronStatClassCodeService
     */
    protected PatronStatClassCodeService $patronStatClassCode;

    /**
     * Create a new command instance.
     *
     * The service is injected automatically by Laravel's service container.
     *
     * @param  PatronStatClassCodeService  $patronStatClassCode
     * @return void
     */
    public function __construct(PatronStatClassCodeService $patronStatClassCode)
    {
        parent::__construct();
        $this->patronStatClassCode = $patronStatClassCode;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data fetch from external API...');

        $this->patronStatClassCode->fetch();

        $this->info('Successfully imported Patron Statistical Class Codes from Polaris into local Database.');

        return Command::SUCCESS;
    }
}
