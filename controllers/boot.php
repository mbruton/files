<?php

namespace extensions\files;
use \frameworks\adapt as adapt;

/* Prevent direct access */
defined('ADAPT_STARTED') or die;

$adapt = $GLOBALS['adapt'];



/*
 * Extend the root controller and add a view_files
 */
\application\controller_root::extend('view_files', function($_this){
    print 'foo';
    return $_this->load_controller("\\extensions\\files\\controller_files");
});


?>