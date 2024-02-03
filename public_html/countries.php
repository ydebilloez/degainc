<?php
include(dirname(__FILE__).'/phpMyEditHeader.php');
?>

<?php

/*
 * IMPORTANT NOTE: This generated file contains only a subset of huge amount
 * of options that can be used with phpMyEdit. To get information about all
 * features offered by phpMyEdit, please check the documentation. It is available
 * on the phpMyEdit pages or in the manuals folder. Some information can also be
 * found in the lib/configoptions.md file.
 *
 * https://sourceforge.net/projects/phpmariaedit/
 *
 * This file was generated by:
 *
 *                    phpMyEdit version: 5.7.6
 *        lib/phpMyEdit.class.php class: 5.7.6
 *            phpMyEditSetup.php script: 5.7.6
 *                     generated script: 5.7.6
 *
 * This file was NOT manually updated.
 */

require_once(dirname(__FILE__).'/lib/phpMyEdit.class.php');
require_once(dirname(__FILE__).'/lib/phpMyEditDB.php');
require_once(dirname(__FILE__).'/phpMyEditDefaults.php');

$opts['tb'] = 'countries';

// Name of field which is the unique key
$opts['key'] = 'rowid';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';
// Sorting field(s)
$opts['sort_field'] = array('rowid');

/* please refer to lib/phpMyEditInfo.php for additional options
   that can be added in this file
*/

$opts['fdd']['rowid'] = array(
         'name' => 'Rowid',
        '_type' => 'int(10) unsigned',
       'select' => 'T',
      'options' => 'VDR', // auto increment
     '_options' => 'auto_increment',
       'maxlen' => '10',
           'js' => array('required' => true),
      'default' => '0',
         '_key' => 'PRI',
         'sort' => true
);
$opts['fdd']['co_name'] = array(
         'name' => 'Co name',
        '_type' => 'varchar(60)',
       'select' => 'T',
       'maxlen' => '60',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['co_name_en'] = array(
         'name' => 'Co name en',
        '_type' => 'varchar(60)',
       'select' => 'T',
       'maxlen' => '60',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_code'] = array(
         'name' => 'Co code',
        '_type' => 'char(2)',
       'select' => 'T',
       'maxlen' => '2',
           'js' => array('required' => true),
         '_key' => 'UNI',
         'sort' => true
);
$opts['fdd']['co_alpha3code'] = array(
         'name' => 'Co alpha3code',
        '_type' => 'char(3)',
       'select' => 'T',
       'maxlen' => '3',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_numcode'] = array(
         'name' => 'Co numcode',
        '_type' => 'char(3)',
       'select' => 'T',
       'maxlen' => '3',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_telprefix'] = array(
         'name' => 'Co telprefix',
        '_type' => 'char(6)',
       'select' => 'T',
       'maxlen' => '6',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_internetsuffix'] = array(
         'name' => 'Co internetsuffix',
        '_type' => 'varchar(30)',
       'select' => 'T',
       'maxlen' => '30',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_flagimg'] = array(
         'name' => 'Co flagimg',
        '_type' => 'blob',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80),
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_imgfromsky'] = array(
         'name' => 'Co imgfromsky',
        '_type' => 'longblob',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80),
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_introduction'] = array(
         'name' => 'Co introduction',
        '_type' => 'text',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80),
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_centre'] = array(
         'name' => 'Co centre',
        '_type' => 'point',
       'select' => 'T',
       'maxlen' => '0',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_area'] = array(
         'name' => 'Co area',
        '_type' => 'polygon',
       'select' => 'T',
       'maxlen' => '0',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['creation_date'] = array(
         'name' => 'Creation date',
        '_type' => 'timestamp',
       'select' => 'T',
      'options' => 'AVCPDR', // filled automatically (MySQL feature)
       'maxlen' => '26',
           'js' => array('required' => true),
      'default' => 'CURRENT_TIMESTAMP',
         'sort' => true
);

// possibly initialise page further before going to main function

if (function_exists('phpMyEditHeaderInit')) { phpMyEditHeaderInit($opts); }

// now copy php variables over to js variables
// protect sensitive variables so they cannot be read
$cleanopts = $opts;
unset($cleanopts['hn']); unset($cleanopts['pt']);
unset($cleanopts['un']); unset($cleanopts['pw']);

echo '
<script>
    var phpOpts = ' . json_encode($cleanopts) . ';
    try {
        if (typeof PME_js_init === \'function\') {
            PME_js_init(phpOpts);
        }
    } catch(err) {
        console.log(err);
    }
</script>
';

// Now important call to phpMyEdit

new phpMyEdit($opts);

//eof
