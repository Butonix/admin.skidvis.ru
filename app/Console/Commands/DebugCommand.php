<?php

namespace App\Console\Commands;

use App\Models\Files\File;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DebugCommand extends Command
{

	protected $signature = 'debug';

	protected $description = 'Command description';

	public function __construct()
	{
		parent::__construct();
	}


	public function handle()
	{

	}

}
