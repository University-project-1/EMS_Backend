<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

if (! function_exists('successResponse')) {
    function successResponse($data = null, string $message = 'Success', int $status = 200)
    {
        if ($data instanceof AnonymousResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            $paginator = $data->resource;

            $data = [
                'data' => $data->collection,
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}

if (! function_exists('errorResponse')) {
    function errorResponse(?string $message = null, mixed $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'    => $errors,
        ], $code);
    }
}


/*
====================
2xx SUCCESS
====================

200 OK
The request was successful and returned the requested data.

201 Created
A new resource was successfully created.

202 Accepted
The request was accepted for processing but not completed yet.

203 Non-Authoritative Information
The returned metadata may come from a third-party source.

204 No Content
The request was successful but there is no content to return.

205 Reset Content
The client should reset the document view.

206 Partial Content
Only part of the resource is returned (used in range requests).

207 Multi-Status
Returns multiple status codes (mainly used in WebDAV).

208 Already Reported
Members of a DAV binding have already been reported.

226 IM Used
The server fulfilled the request using instance manipulations.


====================
4xx CLIENT ERRORS
====================

400 Bad Request
The request is malformed or invalid.

401 Unauthorized
Authentication is required or failed.

402 Payment Required
Reserved for future use.

403 Forbidden
The server understood the request but refuses to authorize it.

404 Not Found
The requested resource does not exist.

405 Method Not Allowed
The HTTP method is not allowed for this resource.

406 Not Acceptable
The server cannot produce a response matching the client's requirements.

407 Proxy Authentication Required
Authentication with a proxy is required.

408 Request Timeout
The client took too long to send the request.

409 Conflict
The request conflicts with the current state of the resource.

410 Gone
The resource has been permanently removed.

411 Length Required
Content-Length header is required.

412 Precondition Failed
A request condition was not met.

413 Payload Too Large
The request body is too large.

414 URI Too Long
The URL is too long.

415 Unsupported Media Type
The request media format is not supported.

416 Range Not Satisfiable
The requested range cannot be fulfilled.

417 Expectation Failed
The server cannot meet the Expect request header.

418 I'm a Teapot
April Fools' joke status code.

422 Unprocessable Entity
Validation failed.

425 Too Early
The server is unwilling to process a request that might be replayed.

426 Upgrade Required
The client must switch to a different protocol.

429 Too Many Requests
Too many requests were sent in a given time.


====================
5xx SERVER ERRORS
====================

500 Internal Server Error
A generic server error occurred.

501 Not Implemented
The server does not support the request method.

502 Bad Gateway
Invalid response from an upstream server.

503 Service Unavailable
The server is temporarily unavailable.

504 Gateway Timeout
The upstream server failed to respond in time.

505 HTTP Version Not Supported
The HTTP version is not supported.

507 Insufficient Storage
The server cannot store the representation needed.

508 Loop Detected
An infinite loop was detected.

510 Not Extended
Further extensions are required.

511 Network Authentication Required
Client must authenticate to gain network access.
 */
