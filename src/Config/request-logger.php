<?php

return [
    /**
     * Where logs will be saved.
     */
    'storage_path' => '/logs/request-logger.log',

    /**
     * Which Request params should be excluded from logging
     */
    'param_exceptions' => ['password', 'password_confirmation'],

    /**
     * Use queue for storing logs, if no - false, yes - queue name (default, log or etc.)
     */
    'queue' => 'default',
];
