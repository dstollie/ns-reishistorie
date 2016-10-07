<?php

namespace App\Library\NS;

use App\Providers\AppServiceProvider;

class NSServiceProvider extends AppServiceProvider
{
	public function register()
	{
		$this->app->singleton('ns', NS::class);
	}
}