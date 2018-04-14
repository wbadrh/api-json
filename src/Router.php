<?php

namespace wbadrh\JSON_API;

use \Exception;

/**
 * Router from configuration
 */
class Router
{
    // Allowed HTTP methods
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    /**
     * Initiate router
     *
     * @param Mixed $configuration Path to routes.json || JSON configuration as string || Array configuration
     */
    function __construct($configuration)
    {
        // Build configuration when no array is passed
        // Possible:
        // 1) Path to file
        // 2) JSON configuration as String
        // 3) Array configuration
        if (!is_array($configuration)) {
            // typeof JSON
            if ($configuration[0] !== '{') {
                // Fetch contents from file path
                $configuration = file_get_contents($configuration);
            }

            // Parse JSON
            $configuration = json_decode($configuration, true); // as array
        }

        // Validate HTTP scheme, hostname & Route configuration
        self::validator(
            $configuration['domain']['scheme'],
            $configuration['domain']['host'],
            $configuration['routes']
        );
        
        // https://github.com/wbadrh/api
        new \wbadrh\API\Router(
            $configuration['domain']['scheme'],
            $configuration['domain']['host'],
            $this->application($configuration['routes'])
        );
    }

    /**
     * Application routes
     *
     * @param Array $routes Configuration routes
     * @return Callable RouteGroup mapper
     */
    private function application(array $routes)
    {
        // RouteGroup Callable
        return function(\League\Route\RouteGroup $router) use ($routes)
        {
            // Build application routes based on configuration
            foreach ($routes as $route) {
                // Map request to router
                $router->map($route['method'], $route['route'], $route['controller']);
            }
        };
    }

    /**
     * Validate URL
     *
     * @param String $url URL
     */
    public static function validate_url(String $url)
    {
        // https://mathiasbynens.be/demo/url-regex
        // @diegoperini (502 chars)
        $regex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.1';
        $regex .= '68(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d';
        $regex .= '|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?';
        $regex .= ':[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';

        if (preg_match($regex, $url))
            return true;

        return false;
    }

    /**
     * Validate HTTP request scheme
     *
     * @param String $scheme HTTP scheme [http, https]
     */
    public static function validate_scheme(String $scheme)
    {
        if ($scheme !== 'https' && $scheme !== 'http')
            throw new Exception('Scheme: "' . $scheme . '" is not a valid scheme. Use one of: [http, https].');
    }

    /**
     * Validate Domain
     *
     * @param String $scheme HTTP scheme [http, https]
     * @param String $host   HTTP host   [domain.ext]
     */
    public static function validate_domain(String $scheme, String $host)
    {
        // Dynamic URL
        $url = $scheme . '://' . $host;

        // Validate URL
        if (self::validate_url($url)) {
            // Validate HTTP request scheme
            self::validate_scheme(parse_url($url)['scheme']);
        } else {
            throw new Exception('Invalid URL: "' . $url . '"');
        }
    }

    /**
     * Validate scheme, host & routes
     *
     * @param String $scheme HTTP scheme [http, https]
     * @param String $host   HTTP host   [domain.ext]
     * @param Array $routes Route group configuration
     */
    public static function validator(String $scheme, String $host, array $routes)
    {
        // Validate Domain
        self::validate_domain($scheme, $host);

        // Validate Routes
        foreach ($routes as $route)
        {
            if (!isset($route['method']))
                throw new Exception('Method is not set in routes.');

            if (!isset($route['route']))
                throw new Exception('Route is not set in routes.');

            if (!isset($route['controller']))
                throw new Exception('Controller is not set in routes.');

            // Validate available HTTP methods
            if (!in_array($route['method'], self::METHODS))
                throw new Exception('Method: "' . $route['method'] . '" is not a valid method. Use one of: [' . implode(', ', self::METHODS) . '].');
        }
    }
}
