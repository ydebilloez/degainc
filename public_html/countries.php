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
         'name' => 'ID',
       'select' => 'T',
      'options' => 'VDR', // auto increment
       'maxlen' => '10',
           'js' => array('required' => true),
      'default' => '0',
         'sort' => true
);
$opts['fdd']['co_name'] = array(
         'name' => 'Name',
       'select' => 'T',
       'maxlen' => '60',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['co_name_en'] = array(
         'name' => 'Name (English)',
       'select' => 'T',
       'maxlen' => '60',
         'sort' => true
);
$opts['fdd']['co_code'] = array(
         'name' => 'Code',
       'select' => 'T',
       'maxlen' => '2',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['co_alpha3code'] = array(
         'name' => 'Alpha code',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '3',
         'sort' => true
);
$opts['fdd']['co_numcode'] = array(
         'name' => 'Numeric code',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '3',
         'sort' => true
);
$opts['fdd']['co_telprefix'] = array(
         'name' => 'Tel prefix',
       'select' => 'T',
       'maxlen' => '6',
         'sort' => true
);
$opts['fdd']['co_internetsuffix'] = array(
         'name' => 'Internet domain',
       'select' => 'T',
       'maxlen' => '30',
         'sort' => true
);
$opts['fdd']['co_flagimg'] = array(
         'name' => 'Flag',
        '_type' => 'blob',
       'select' => 'T',
      'options' => 'VDR',
     'textarea' => array('rows' => 5, 'cols' => 80),
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_imgfromsky'] = array(
         'name' => 'Co imgfromsky',
        '_type' => 'longblob',
       'select' => 'T',
      'options' => 'VDR',
     'textarea' => array('rows' => 5, 'cols' => 80),
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_introduction'] = array(
         'name' => 'Introduction',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80),
         'sort' => true
);
$opts['fdd']['co_centre'] = array(
         'name' => 'Centre point',
        '_type' => 'point',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '0',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['co_area'] = array(
         'name' => 'Area',
        '_type' => 'polygon',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '0',
        '_null' => 'YES',
         'sort' => true
);
$opts['fdd']['creation_date'] = array(
         'name' => 'Creation date',
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

