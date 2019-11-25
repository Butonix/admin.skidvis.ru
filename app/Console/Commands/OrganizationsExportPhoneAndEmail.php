<?php

namespace App\Console\Commands;

use App\Models\Communications\Phone;
use App\Models\Organizations\Organization;
use Illuminate\Console\Command;

class OrganizationsExportPhoneAndEmail extends Command
{

	protected $signature = 'organizations-export-phone-and-email';

	protected $description = 'Command description';

	public function __construct()
	{
		parent::__construct();
	}


	public function handle()
	{
		Organization::where(function ($q){
			$q->doesntHave('phone')->orDoesntHave('email');
		})->chunk(100, function ($orgs) {
			foreach ($orgs as $org) {
				$res = $org->exportPhoneAndEmailFromPoint();
				dump('ID:'.$org->getKey().' '.$res. ' - ' .$org->getPhone(). ' - ' .$org->getEmail());
			}
		});
	}

}
