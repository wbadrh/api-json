<?php

namespace wbadrh\JSON_API;

// http://route.thephpleague.com/
use Psr\Http\Message\ResponseInterface;

/**
 * Response
 */
class Response
{
    /**
     * JSON response class
     *
     * @param ResponseInterface $response PSR-7 HTTP response message
     * @param Integer           $status   HTTP status (e.g: 200)
     * @param Mixed             $result   Array, String or Object to send as JSON response
     */
    public function dispatch(ResponseInterface $response, int $status, $result)
    {
        // Build output array
        $contents = [
            'status' => $status,
            'result' => $result,
        ];

        // Convert output to JSON
        $contents = json_encode($contents, JSON_PRETTY_PRINT);

        // Write Response to Body
        $response->getBody()->write($contents);

        // PSR-7 response
        return $response->withStatus($status); 
    }

    /**
     * Available Exceptions
     *
     * @return Array HTTP Exceptions
     */
    public function exceptions()
    {
        return [
            400 => [
                'message' => 'The request cannot be fulfilled due to bad syntax.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\BadRequestException; }
            ],
            401 => [
                'message' => 'Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\UnauthorizedException; }
            ],
            403 => [
                'message' => 'The request was a valid request, but the server is refusing to respond to it.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\ForbiddenException; }
            ],
            404 => [
                'message' => 'The requested resource could not be found but may be available again in the future.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\NotFoundException; }
            ],
            405 => [
                'message' => 'A request was made of a resource using a request method not supported by that resource; for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\MethodNotAllowedException; }
            ],
            406 => [
                'message' => 'The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\NotAcceptableException; }
            ],
            409 => [
                'message' => 'Indicates that the request could not be processed because of conflict in the request, such as an edit conflict in the case of multiple updates.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\ConflictException; }
            ],
            410 => [
                'message' => 'Indicates that the resource requested is no longer available and will not be available again.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\GoneException; }
            ],
            411 => [
                'message' => 'The request did not specify the length of its content, which is required by the requested resource.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\LengthRequiredException; }
            ],
            412 => [
                'message' => 'The server does not meet one of the preconditions that the requester put on the request.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\PreconditionFailedException; }
            ],
            415 => [
                'message' => 'The request entity has a media type which the server or resource does not support.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\UnsupportedMediaException; }
            ],
            417 => [
                'message' => 'The server cannot meet the requirements of the Expect request-header field.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\ExpectationFailedException; }
            ],
            428 => [
                'message' => 'The origin server requires the request to be conditional.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\PreconditionRequiredException; }
            ],
            429 => [
                'message' => 'The user has sent too many requests in a given amount of time.',
                'throw'   => function(){ throw new \League\Route\Http\Exception\TooManyRequestsException; }
            ],
            451 => [
                'message' => 'The resource is unavailable for legal reasons..',
                'throw'   => function(){ throw new \League\Route\Http\Exception\UnavailableForLegalReasonsException; }
            ]
        ];
    }

    /**
     * Throw HTTP Exception
     *
     * @param Integer $status HTTP status (self::EXCEPTIONS)
     */
    public function exception(int $status)
    {
        // Find & throw exeption from anonymous function
        $this->exceptions()[$status]['throw']();
    }
}
