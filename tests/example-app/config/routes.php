<?php

use Tale\Base\Controller;

return [
    '/:controller?/:action?/:id?.:format?' => function($data) {

        return Controller::dispatch($data);
    }
];