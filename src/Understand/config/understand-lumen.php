<?php

return [

    'handler' => env('UNDERSTAND_HANDLER', 'sync'),
    'token' => env('UNDERSTAND_INPUT_KEY'),
    'silent' => env('UNDERSTAND_SILENT', true),

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