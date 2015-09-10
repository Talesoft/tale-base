<?php

use Tale\Base\Controller;

return [
    '/:controller?/:action?/:id?.:format?' => function($data) {

        Controller::dispatch($data);
    },

    //These are mainly examples
    '/blog/:controller?/:action?/:id?.:format?' => function($data) {

        Controller::dispatch($data);
    }
];