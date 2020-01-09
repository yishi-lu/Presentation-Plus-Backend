<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * It is as service provider for init the class container.
 * And the configuration file is <b> myServiceBean.php </b> under the config folder.
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 */
class MyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $beans = config('myServiceBean');
        foreach ($beans as $i => $impl) {
            if ($impl['singleton']) {
                $this->app->singleton($i, function () use ($impl) {
                    return $this->app->make($impl['class']);
                }, empty($impl['shared']) ? null : $impl['shared']);
            } else {
                $this->app->bind($i, function () use ($impl) {
                    return $this->app->make($impl['class']);
                }, empty($impl['shared']) ? null : $impl['shared']);
            }
        }
    }
}
