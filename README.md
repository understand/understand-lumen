## Laravel Lumen service provider for Understand.io

[![Build Status](https://travis-ci.org/understand/understand-lumen.svg)](https://travis-ci.org/understand/understand-lumen)
[![Latest Stable Version](https://poser.pugx.org/understand/understand-lumen/v/stable.svg)](https://packagist.org/packages/understand/understand-lumen)
[![Latest Unstable Version](https://poser.pugx.org/understand/understand-lumen/v/unstable.svg)](https://packagist.org/packages/understand/understand-lumen)
[![License](https://poser.pugx.org/understand/understand-lumen/license.svg)](https://packagist.org/packages/understand/understand-lumen)
[![HHVM Status](http://hhvm.h4cc.de/badge/understand/understand-lumen.svg)](http://hhvm.h4cc.de/package/understand/understand-lumen)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/understand/understand-lumen/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/understand/understand-lumen/?branch=master)

> You may also be interested in our [Laravel 4](https://github.com/understand/understand-laravel), [Laravel 5 service provider](https://github.com/understand/understand-laravel5) or [Monolog Understand.io handler](https://github.com/understand/understand-monolog)



### Introduction

This packages provides a full abstraction for Understand.io and provides extra features to improve Lumen's default logging capabilities. It is essentially a wrapper around our [Understand Monolog handler](https://github.com/understand/understand-monolog) to take full advantage of Understand.io's data aggregation and analysis capabilities.

### Quick start

1. Add this package to your project via composer:

    ```
    composer require understand/understand-lumen
    ```

2. In `bootstrap/app.php` enable `Dotenv` (around line 5) AND `$app->withFacades();` (around line 22):

    ```php
    Dotenv::load(__DIR__.'/../'); // around line 5

    ...

    $app->withFacades(); // around line 22
    ```

3. In `bootstrap/app.php` register the UnderstandLumenServiceProvider:

    ```php
    $app->register(Understand\UnderstandLumen\UnderstandLumenServiceProvider::class);
    ```

4. Create a new file as `config/understand_lumen.php` (note that you may need to create the `config` directory) with the following contents:

    ```php
    <?php

    return [
    
        /**
         * Specify which handler to use - sync or async.
         *
         * Note that the async handler will only work in systems where
         * the CURL command line tool is installed
         */
        'handler' => env('UNDERSTAND_HANDLER', 'sync'),
    
        /**
         * Your input token from Understand.io
         */
        'token' => env('UNDERSTAND_INPUT_KEY'),
    
        /**
         * Specifies whether logger should throw an exception of issues detected
         */
        'silent' => env('UNDERSTAND_SILENT', true),
    
        /**
         * Specify additional field providers for each log
         * E.g. sha1 version session_id will be appended to each "Log::info('event')"
         */
        'meta' => [
            'user_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUserId',
            'session_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getSessionId',
            'request_id' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getProcessIdentifier',
            'url'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUrl',
            'client_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientIp',
            //'server_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getServerIp',
            //'user_agent' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientUserAgent',
            //'environment' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getEnvironment',
            //'request_method'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getRequestMethod',
        ],
    ];
    ```

5. Set your input key (from Understand.io) in your `.env` file as `UNDERSTAND_INPUT_KEY`

    ```php
    UNDERSTAND_INPUT_KEY=your-input-key-from-understand
    ```

6. Open `app/Exceptions/Handler.php` and adjust the `report` method:

    ```php
    public function report(Exception $e)
    {
        $encoder = new \UnderstandMonolog\Encoder\ExceptionEncoder();

        app('Psr\Log\LoggerInterface')->error($e->getMessage(), $encoder->exceptionToArray($e));
    }
    ```

7. Send your first event

    ```php
    $app->get('/', function () use ($app) {

        Log::info('test log 123');

        return $app->welcome();
    });

    ```

### Additional meta data (field providers)
You may wish to capture additional meta data with each event. For example, it can be very useful to capture the request url with exceptions, or perhaps you want to capture the current user's ID. To do this, you can specify custom field providers via the config.

```php
/**
 * Specify additional field providers for each log
 * E.g. sha1 version session_id will be appended to each "Log::info('event')"
 */
'meta' => [
    'user_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUserId',
    'session_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getSessionId',
    'request_id' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getProcessIdentifier',
    'url'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUrl',
    'client_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientIp',
    //'server_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getServerIp',
    //'user_agent' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientUserAgent',
    //'environment' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getEnvironment',
    //'request_method'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getRequestMethod',
],
```

The Understand.io service provider contains a powerful field provider class which provides default providers, and you can create or extend new providers.

```php
dd(Understand\UnderstandLumen\UnderstandFieldProviderFacade::getSessionId());
// output: c624e355b143fc050ac427a0de9b64eaffedd606
```

#### Default field providers
The following field providers are included in this package:

- `getSessionId` - return sha1 version of session id
- `getUrl` - return current url (e.g. `/my/path?with=querystring`).
- `getRequestMethod` - return request method (e.g. `POST`).
- `getServerIp` - return server IP.
- `getClientIp` - return client IP.
- `getClientUserAgent` - return client's user agent.
- `getEnvironment` - return Lumen environment (e.g. `production`).
- `getProcessIdentifier` - return unique token which is unique for every request. This allows you to easily group all events which happen in a single request.
- `getUserId` - return current user id. This is only available if you make sure of the default Laravel Lumen auth or the cartalyst/sentry package. Alternatively, if you make use of a different auth package, then you can extend the `getUserId` field provider and implement your own logic.

#### How to extend create your own methods or extend the field providers
```php
Understand\UnderstandLumen\UnderstandFieldProviderFacade::extend('getMyCustomValue', function()
{
    return 'my custom value';
});

dd(Understand\UnderstandLumen\UnderstandFieldProviderFacade::getMyCustomValue());
```

#### Example
Lets assume that you have defined a custom field provider called `getMyCustomValue` (as above). You should then add this to your config file as follows:

```php
'meta' => [
    ...
    'custom_value' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getMyCustomValue',
    ...
]
```

This additional meta data will then be automatically appended to all of your Lumen log events (`Log::info('my_custom_event')`), and will appear as follows:

```json

{
  "message": "my_custom_event",
  "custom_value":"my custom value"
}
```


### How to send data asynchronously

##### Async handler
By default each log event will be sent to Understand.io's api server directly after the event happens. If you generate a large number of logs, this could slow your app down and, in these scenarios, we recommend that you make use of a async handler. To do this, change the config parameter `handler` to `async`.

```php
/**
 * Specify which handler to use - sync or async.
 *
 * Note that the async handler will only work in systems where
 * the CURL command line tool is installed
 */
'handler' => 'async',
```

The async handler is supported in most of the systems - the only requirement is that CURL command line tool is installed and functioning correctly. To check whether CURL is available on your system, execute following command in your console:

```
curl -h
```

If you see instructions on how to use CURL then your system has the CURL binary installed and you can use the ```async``` handler.

> Keep in mind that Lumen allows you to specify different configuration values in different environments. You could, for example, use the async handler in production and the sync handler in development.


### Configuration

```php
return [

    /**
     * Specify which handler to use - sync or async.
     *
     * Note that the async handler will only work in systems where
     * the CURL command line tool is installed
     */
    'handler' => env('UNDERSTAND_HANDLER', 'sync'),

    /**
     * Your input token from Understand.io
     */
    'token' => env('UNDERSTAND_INPUT_KEY'),

    /**
     * Specifies whether logger should throw an exception of issues detected
     */
    'silent' => env('UNDERSTAND_SILENT', true),

    /**
     * Specify additional field providers for each log
     * E.g. sha1 version session_id will be appended to each "Log::info('event')"
     */
    'meta' => [
        'user_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUserId',
        'session_id'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getSessionId',
        'request_id' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getProcessIdentifier',
        'url'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getUrl',
        'client_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientIp',
        //'server_ip'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getServerIp',
        //'user_agent' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getClientUserAgent',
        //'environment' => 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getEnvironment',
        //'request_method'=> 'Understand\UnderstandLumen\UnderstandFieldProviderFacade::getRequestMethod',
    ],
];
```

### Requirements
##### UTF-8
This package uses the json_encode function, which only supports UTF-8 data, and you should therefore ensure that all of your data is correctly encoded. In the event that your log data contains non UTF-8 strings, then the json_encode function will not be able to serialize the data.

http://php.net/manual/en/function.json-encode.php

### License

The Laravel Lumen Understand.io service provider is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
