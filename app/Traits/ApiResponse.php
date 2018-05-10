<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /*
     * Default status code
     */
    protected $statusCode = Response::HTTP_OK;

    public function setStatusCode($status)
    {
        $this->statusCode = $status;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function respondNotFound($message = 'Not found')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)
            ->respondError($message);
    }

    public function respondCreated($message = 'Created.')
    {
        return $this->setStatusCode(Response::HTTP_CREATED)
            ->respond($message);
    }

    public function respondServerError($message = 'Server Error.')
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->respond($message);
    }

    public function respondError($message, $headers = [])
    {
        return response()->json(
            ['error' => $message],
            $this->getStatusCode(),
            $headers
        );
    }

    public function respondImage($image)
    {
        return response($image, $this->statusCode)
            ->header('Content-Type', 'image/jpg');
    }

    public function respond($data, $headers = [])
    {
        return response()->json(
            ['data' => $data],
            $this->getStatusCode(),
            $headers
        );
    }
}
