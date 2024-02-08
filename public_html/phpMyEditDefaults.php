<?php

/*
 * phpMyEditDefaults.php, part of phpMyEdit - instant MySQL table editor and code generator
 */

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';
$opts['buttons']['L']['down'] = array('-<<','-<','-add','-view','-change','-copy','-delete',
                                    '->','->>','-goto','-goto_combo');
$opts['buttons']['V']['down'] = array('-change','cancel');

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'DG';

// Display special page elements
$opts['display'] = array(
    'form'  => true,
    'query' => false,
    'sort'  => true,
    'time'  => false,
    'tabs'  => true
);

// Set default prefixes for variables
$opts['js']['prefix']               = 'PME_js_';
$opts['dhtml']['prefix']            = 'PME_dhtml_';
$opts['cgi']['prefix']['operation'] = 'PME_op_';
$opts['cgi']['prefix']['sys']       = 'PME_sys_';
$opts['cgi']['prefix']['data']      = 'PME_data_';

/* Get the user's default language and use it if possible or you can
   specify particular one you want to use. Refer to official documentation
   for list of available languages. */
$opts['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '-UTF8';

$opts['logtable'] = 'changelog';