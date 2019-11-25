<?php

namespace App\Console\Commands;

use App\Models\Communications\Phone;
use App\Models\Organizations\Organization;
use App\Models\Organizations\Point;
use Illuminate\Console\Command;

class ReplaceCityInPoints extends Command
{

	protected $signature = 'replace-city-in-points';

	protected $description = 'Command description';

	public function __construct()
	{
		parent::__construct();
	}


	public function handle()
	{
		Point::whereNotNull('full_street')->chunk(100, function ($items) {
			foreach ($items as $item) {
				$item->street = $item->getStreetReplacedCity();
				$item->save();
			}
		});
	}

}
