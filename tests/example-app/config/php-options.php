<?php

$options = [
    'displayErrors' => 1,
    'displayStartupErrors' => 1,
    'errorReporting' => E_ALL,
    'xdebug.maxNestingLevel' => 10000
];


//Enable full UTF-8 support
if (function_exists('mb_http_output')) {

    mb_http_output('UTF-8');
    ob_start('mb_output_handler');
}


return $options;