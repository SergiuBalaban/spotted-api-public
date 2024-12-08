<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class Response extends JsonResponse
{
    protected $message;

    protected $errors = [];

    protected $data = [];

    /**
     * Returns the json response.
     *
     * @return JsonResponse
     */
    public function send()
    {
        $this->formatResponse();
        return $this->json($this->response, $this->getStatusCode());
    }

    /**
     * Set response data.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        if ($data instanceof Collection) {
            $this->data = $data->toArray();
        } elseif ($data instanceof AnonymousResourceCollection || $data instanceof JsonResource) {
            $this->data = $data->resource;
            if($data->resource instanceof LengthAwarePaginator) {
                $this->data = $data->resource->toArray();
            }
        } else {
            $this->data = (array)$data;
        }

        return $this;

    }

    /**
     * Append response data object.
     *
     * @param array $data
     * @return $this
     */
    public function appendData(array $data = [])
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Set response message error.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors($errors = [])
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Set response message.
     *
     * @param $type
     * @param $title
     * @param string $description
     * @return $this
     * @throws \Exception
     */
    private function setMessage($type, $title = '', $description = '')
    {
        if (!is_string($title) || !is_string($description)) {
            throw new \Exception("Message not set correctly", 430);
        }

        $this->message = (object)[
            'type'        => $type,
            'title'       => $title,
            'description' => $description
        ];

        return $this;
    }

    /**
     * Set success message.
     *
     * @param string $title
     * @param string $description
     * @return Response
     * @throws \Exception
     */
    public function withSuccess($title = '', $description = '')
    {
        return $this->setMessage('success', $title, $description);
    }

    /**
     * Set warning message.
     *
     * @param string $title
     * @param string $description
     * @return Response
     * @throws \Exception
     */
    public function withWarning($title = '', $description = '')
    {
        return $this->setMessage('warning', $title, $description);
    }

    /**
     * Set error message.
     *
     * @param string $title
     * @param string $description
     * @return Response
     * @throws \Exception
     */
    public function withError($title = '', $description = '')
    {
        return $this->setMessage('error', $title, $description);
    }

    /**
     * Set info message.
     *
     * @param string $title
     * @param string $description
     * @return Response
     * @throws \Exception
     */
    public function withInfo($title = '', $description = '')
    {
        return $this->setMessage('info', $title, $description);
    }

    /**
     * Set the token array structure.
     *
     * @param $token
     * @return Response
     */
    public function setJwtToken($token)
    {
        $data = array_merge($this->data, [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Carbon::now()->addYear()->timestamp
        ]);

        return $this->setData($data);
    }

    /**
     * Returns the json response with item.
     *
     * @param $item
     * @param null $appends
     * @param array $without
     * @return JsonResponse
     */
    public function sendItem($item, $appends = null, $without = [])
    {
        $array_to_send = $item->toArray();
        if($appends) {
            $array_to_send['appends'] = $appends;
        }

        if (!empty($without)) {
            $array_to_send = array_diff_key($array_to_send, $without);
        }
        $this->setData($array_to_send);
        $this->formatResponse();
        return $this->send();
    }

    /**
     * Create the JsonResponse from inputs
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * Create response array.
     */
    private function formatResponse()
    {
        $this->response = $this->data;

        if (!empty($this->message)) {
            $this->response['message'] = $this->message;
        }

        if (!empty($this->errors)) {
            $this->response['errors'] = $this->errors;
        }
    }

}
