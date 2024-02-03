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

// custom settings
$opts['options'] = 'ACVD';
$opts['navigation'] = 'DB';
$opts['display']['sort'] = false;
$opts['buttons']['L']['down'] = array('-<<','-<','-add','-view','-change','-copy','-delete',
                                    '->','->>','-goto','-goto_combo');

$opts['tb'] = 'achats';

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
$opts['fdd']['commande_id'] = array(
         'name' => 'Commande ID',
       'select' => 'T',
       'maxlen' => '10',
  'values' => array(
    'table'  => 'commandes',
    'column' => 'rowid'
  ),
         'sort' => true
);
$opts['fdd']['date_achat'] = array(
         'name' => 'Date achat',
       'select' => 'T',
       'maxlen' => '10',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['prix_achat'] = array(
         'name' => 'Prix achat',
       'select' => 'N',
       'maxlen' => '10',
         'sort' => true
);
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
       'maxlen' => '255',
           'js' => array('required' => true),
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

