<?php namespace Understand\UnderstandLumen;

use Illuminate\Support\ServiceProvider;
use UnderstandMonolog\Handler\UnderstandAsyncHandler;
use UnderstandMonolog\Handler\UnderstandSyncHandler;
use Understand\UnderstandLumen\TokenProvider;
use Understand\UnderstandLumen\FieldProvider;
use Understand\Processor\UnderstandProcessor;

class UnderstandLumenServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerTokenProvider();
        $this->registerFieldProvider();
        $this->registerMonologHandler();
    }

    /**
     * Register configuration file
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->app->configure('understand-lumen');

        $configPath = __DIR__ . '/../config/understand-lumen.php';
        $this->mergeConfigFrom($configPath, 'understand-lumen');
    }

    /**
     * Register monolog handler
     *
     * @return void
     */
    protected function registerMonologHandler()
    {
        $handlerType = $this->app['config']->get('understand-lumen.handler', 'sync');
        $inputToken = $this->app['config']->get('understand-lumen.token');
        $apiUrl = $this->app['config']->get('understand-lumen.url' ,'https://api.understand.io');
        $silent = $this->app['config']->get('understand-lumen.silent', true);
        $sslBundlePath = $this->app['config']->get('understand-lumen.ssl_bundle_path', false);
        $metaFields = $this->app['config']->get('understand-lumen.meta', []);

        if ($handlerType == 'async')
        {
            $handler = new UnderstandAsyncHandler($inputToken, $apiUrl, $silent, $sslBundlePath);
        }
        else
        {
            $handler = new UnderstandSyncHandler($inputToken, $apiUrl, $silent, $sslBundlePath);
        }

        $handler->pushProcessor(new UnderstandProcessor($metaFields));

        $this->app['Psr\Log\LoggerInterface']->pushHandler($handler);
    }

    /**
     * Register field provider
     *
     * @return void
     */
    protected function registerFieldProvider()
    {
        $this->app->bind('understand-lumen.field-provider', function($app)
        {
            $fieldProvider = new FieldProvider();
            $fieldProvider->setSessionStore($app['session.store']);
            $fieldProvider->setRequest($app['request']);
            $fieldProvider->setEnvironment($app->environment());
            $fieldProvider->setTokenProvider($app['understand-lumen.token-provider']);

            return $fieldProvider;
        });
    }

    /**
     * Register token provider
     *
     * @return void
     */
    protected function registerTokenProvider()
    {
        $this->app->singleton('understand-lumen.token-provider', function()
        {
            return new TokenProvider();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['understand-lumen.token-provider', 'understand-lumen.field-provider'];
    }
}