<?php

namespace App\Providers;

use App\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\Http\ViewComposers\UserFieldsComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::component('shared._card', 'card');

        $this->app->bind(LengthAwarePaginator::class, \App\LengthAwarePaginator::class);

        Blade::directive('render', function ($expresion) {
            $parts = explode(',', $expresion, 2);

            $component = $parts[0];

            $args = trim($parts[1] ?? '[]');

            return "<?php echo  app('App\Http\ViewComponents\\\\'.{$component}, {$args})->toHtml() ?>";
        });

        Builder::macro('whereQuery', function ($subquery, $value) {
            $this->addBinding($subquery->getBindings());
            $this->where(DB::raw("({$subquery->toSql()})"), $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Sortable::class, function ($app) {
            return new Sortable(request()->url());
        });
    }
}
