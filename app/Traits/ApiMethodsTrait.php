<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiMethodsTrait
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param  mixed  $statusCode
     * @return self
     */
    public function setStatusCode(mixed $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondNotFound(string $message = 'Not found.'): mixed
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondUnprocessableEntity(string $message = 'Missing parameter.'): mixed
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondUnauthorised(string $message = 'Unauthorised.', $data = []): mixed
    {
        return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)->respondWithError($message, $data);
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondInternalError(string $message = 'Something went wrong.'): mixed
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * @param  string  $message
     * @param  array  $data
     * @return mixed
     */
    public function respondCreated(string $message = 'Record created.', array $data = []): mixed
    {
        return $this->setStatusCode(Response::HTTP_CREATED)->respondWithSuccess($message, $data);
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondUnprocessableEntityRequest(string $message = 'Record created.'): mixed
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param  string  $message
     * @return mixed
     */
    public function respondBadRequest(string $message = 'Record created.'): mixed
    {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    /**
     * @param  string  $message
     * @param  array  $data
     * @return mixed
     */
    public function respondWithError(string $message, array $data = []): mixed
    {
        if ( ! empty($data)) {
            return $this->respond([
                'error' => [
                    'message' => $message,
                    'data' => $data,
                    'status_code' => $this->getStatusCode(),
                ],
            ]);
        }

        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode(),
            ],
        ]);
    }

    /**
     * @param  string  $message
     * @param  mixed  $data
     * @return mixed
     */
    public function respondWithSuccess(string $message,  $data = []): mixed
    {
        return $this->respond([
            'success' => [
                'message' => $message,
                'data' => $data,
                'status_code' => $this->getStatusCode(),
            ],
        ]);
    }

    /**
     * @param  array  $data
     * @param  array  $headers
     * @return mixed
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function rawSql($queryBuilder){
        $sql=$queryBuilder->toSql();
        $bindings=$queryBuilder->getBindings();
        foreach ($bindings as $binding) {
            $sql = preg_replace('/\?/', "'{$binding}'", $sql, 1);
        }
        return $sql;
    }
}
