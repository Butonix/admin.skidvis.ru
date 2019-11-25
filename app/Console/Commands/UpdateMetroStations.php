<?php

namespace App\Console\Commands;

use App\Models\Cities\City;
use App\Models\Organizations\Point;
use Illuminate\Console\Command;

class UpdateMetroStations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-metro-stations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(City $city, Point $point)
    {
//		$city->updateMetroStations();
		$point->updateMetroStations();
    }
}
