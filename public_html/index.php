<?php

require __DIR__ . '/../vendor/autoload.php';

// 1) Path to file
//$routes = __DIR__ . '/../routes.json';

// 2) JSON configuration as String
//$routes = '{...}';

// 3) Array configuration
$routes = [
    'domain' => [
        'scheme' => 'http',
        'host'   => 'api-json.test'
    ],
    'routes' => [
        [
            /*
                - GET
                - POST
                - PUT
                - PATCH
                - DELETE
                - HEAD
                - OPTIONS
            */
            "method"     => "GET", 
            /*
                {argument}
                {argument:expression}
                - number
                - word
                - alphanum_dash
                - slug
                - uuid
                - [0-9]+
            */
            "route"      => "/",
            /*
                your\Namespace\Controller::action
            */
            "controller" => "wbadrh\JSON_API\Controller::response"
        ],
        [
            "method"     => "GET",
            "route"      => "/{name}",
            "controller" => "wbadrh\JSON_API\Controller::response_args"
        ]
    ]
];

new \wbadrh\JSON_API\Router($routes);
