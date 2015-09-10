<?php

use Tale\Base\Controller;

return [
     //These are mainly examples
    '/blog/:controller?/:action?/:id?.:format?' => function($data) {

        $data['module'] = 'blog';

        Controller::dispatch($data);
    },

    '/acp/:controller?/:action?/:id?.:format?' => function($data) {

        $data['module'] = 'acp';

        Controller::dispatch($data);
    },

    //This is the main route
    '/:controller?/:action?/:id?.:format?' => function($data) {

        Controller::dispatch($data);
    }
];