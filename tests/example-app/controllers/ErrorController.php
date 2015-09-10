<?php

namespace My\App\Controller;

use Tale\Base\Controller;

class ErrorController extends Controller
{

    public function notFoundAction()
    {

        return [
            'code' => 404,
            'message' => 'Not found',
            'request' => isset( $this->request ) ? $this->request : null
        ];
    }
}