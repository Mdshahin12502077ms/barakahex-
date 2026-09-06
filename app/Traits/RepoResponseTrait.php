<?php namespace App\Traits;

trait RepoResponseTrait {



    public function formatResponse(bool $status, string $msg, string $redirect_to, $data = null, string $flash_type = '') : object
    {

        if ($flash_type == '') {
            $flash_type = $status ? 'success' : 'error'; // flashMessageWarning
        }

        return (object) array(
            'status'         => $status,
            'msg'            => $msg,
            // 'description'    => $msg,
            'data'           => $data,
            'id'             => ! is_array($data) && $data != '' ? $data : 0,
            'redirect_to'    => $redirect_to,
            'redirect_class' => $flash_type
        );
    }

    public function successResponse(int $code, string $msg, $data = null, int $status = 1, string $description = '') : object
    {
        return (object) array(
            'status'        => $status,
            'success'       => true,
            'code'          => $code,
            'message'       => $msg,
            'description'   => $description,
            'data'          => $data,
            'errors'        => null,
        );
    }

    public function errorResponse(int $code, string $msg, $errors = null, int $status = 0, string $description = '') : object
    {
        return (object) array(
            'status'    => $status,
            'success'   => false,
            'code'      => $code,
            'message'   => $msg,
            'description' => $description,
            'data'      => null,
            'errors'    => $this->getFormattedErrors($code, $errors, $msg),
        );
    }

    private function getFormattedErrors(int $code, $errors, string $reason = '', string $description = '') : object
    {
        if ($description == '') {
            $description = $reason;
        }

        return (object) [
            'fields'        => $errors,
            'error_as_string' => $this->getErrorAsString($errors),
            'reason'        => $reason,
            'description'   => $description,
            'error_code'    => $code,
            'link'          => ''
        ];
    }

}
