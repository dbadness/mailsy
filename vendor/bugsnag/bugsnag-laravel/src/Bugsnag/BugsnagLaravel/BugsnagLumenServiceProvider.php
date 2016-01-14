<?php namespace Bugsnag\BugsnagLaravel;

use Illuminate\Support\ServiceProvider;

class BugsnagLumenServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->configure('bugsnag');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bugsnag', function ($app) {
            $config = isset($app['config']['services']['bugsnag']) ? $app['config']['services']['bugsnag'] : null;
            if (is_null($config)) {
                $config = $app['config']['bugsnag'] ?: $app['config']['bugsnag::config'];
            }

            $client = new \Bugsnag_Client($config['api_key']);
            $client->setStripPath(base_path());
            $client->setProjectRoot(base_path() . '/app');
            $client->setAutoNotify(false);
            $client->setBatchSending(false);
            $client->setReleaseStage($app->environment());
            $client->setNotifier(array(
                                     'name'    => 'Bugsnag Lumen',
                                     'version' => '1.6.3',
                                     'url'     => 'https://github.com/bugsnag/bugsnag-laravel'
                                 ));

            if (isset($config['notify_release_stages']) && is_array($config['notify_release_stages'])) {
                $client->setNotifyReleaseStages($config['notify_release_stages']);
            }

            if (isset($config['endpoint'])) {
                $client->setEndpoint($config['endpoint']);
            }

            if (isset($config['filters']) && is_array($config['filters'])) {
                $client->setFilters($config['filters']);
            }

            if (isset($config['proxy']) && is_array($config['proxy'])) {
                $client->setProxySettings($config['proxy']);
            }

            // Check if someone is logged in.
            try {
                if ($app['auth']->check()) {
                    // User is logged in.
                    $user = $app['auth']->user();

                    // If these attributes are available: pass them on.
                    $client->setUser(array('id' => $user->getAuthIdentifier()));
                }
            } catch (\Exception $e) {
                // Do nothing.
            }

            return $client;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array("bugsnag");
    }
}
