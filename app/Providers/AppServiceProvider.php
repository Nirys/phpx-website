<?php

namespace App\Providers;

use App\Enums\DomainStatus;
use App\Models\ExternalGroup;
use App\Models\Group;
use App\Services\PHPX;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		app()->singleton('phpx', PHPX::class);
	}

	public function boot(): void
	{
		Model::unguard();

		if (App::isProduction()) {
			URL::forceScheme('https');
		}

		Actions::registerCommands();

		Route::middleware('web')->group(fn() => Actions::registerRoutes());

		$this->sharePhpxNetwork();
	}

	protected function sharePhpxNetwork(): void
	{
		$this->callAfterResolving(Factory::class, function (Factory $view) {
			$data = app('phpx')->getNetwork();

			/** @var \Illuminate\Support\Collection<string, Group|ExternalGroup> $network */
			$network = collect($data)
				->map(function (array $record) {
					[$fqcn, $attributes] = $record;
					return (new $fqcn)->newFromBuilder($attributes);
				});

			$view->share('phpx_network', $network);
		});
	}
}
