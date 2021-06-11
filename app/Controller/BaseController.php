<?php

namespace App\Controller;

class BaseController
{
    /**
     * Success response
     *
     * @param array|null $data
     */
    public function success($data)
    {
        return $this->response([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Error response
     *
     * @param string $message
     */
    public function error($message)
    {
        return $this->response([
            'status' => 'error',
            'message' => $message
        ]);
    }

    /**
     * Use to return on function success/error
     *
     * @param array $arr
     * @return json
     */
    private function response($arr)
    {
        header('Content-type: application/json');
        return json_encode($arr);
    }
}
