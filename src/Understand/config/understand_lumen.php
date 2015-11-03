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