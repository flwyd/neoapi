<?php

namespace App\Http;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class JsonApi {

    /*
     * Return a JSON API successful response. Indicate attributes were stored
     * "as-is" to the database.
     * @var object $response response() to send JSON
     * @var Model $record model record to obtain name and id from
     */

    public static function successResponse(object $response, Model $record) {
        return $response->json([
            'data' => [
                'type' => self::jsonName(class_basename($record)),
                'id'   => $record->id,
                'attributes' => [],
            ]
        ]);
    }

    /*
     * construct a JSON API error response
     * @var object $response response() to send JSON
     * @var integer $status the HTTP status code to send back
     * @var (array|string) $errorMessages a string or array of strings to send back
     */
    public static function errorResponse($response, $status, $errorMessages)
    {
        $errorRows = [ ];

        if (!is_array($errorMessages)) {
            $errorMessages = [ $errorMessages ];
        }

        foreach ($errorMessages as $message) {
            $errorRows[] = [
                'status' => $status,
                'title'  => $message
            ];
        }

        return $response->json([ 'errors' => $errorRows ], $status);
    }

    public static function deleteSuccess($response) {
        return $response->json([ 'meta' => [ 'status' => 'success' ]]);
    }
    /*
     * Convert a string to a JSON API name (aka dash case)
     * @var string $value String to dashizer into a JSON API name
     */

    public static function jsonName(string $value): string
    {
        return Str::snake(Str::camel($value), '-');
    }
}
