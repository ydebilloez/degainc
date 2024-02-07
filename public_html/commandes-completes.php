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

$opts['tb'] = 'commandes';

// custom settings overwriting general edit defaults
$opts['options'] = 'VF';

// filter on subset
$opts['filters'] = "`date_paiement` IS NOT NULL";

// Name of field which is the unique key
$opts['key'] = 'rowid';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('date_commande');

/* please refer to lib/phpMyEditInfo.php for additional options
   that can be added in this file
*/

$opts['fdd']['rowid'] = array(
         'name' => 'ID',
       'select' => 'T',
      'options' => 'VDR', // auto increment
       'maxlen' => '10',
         'sort' => true
);
$opts['fdd']['co_type'] = array(
         'name' => 'Type',
       'select' => 'R',
       'maxlen' => '5',
       'values' => array(
                  "Achat",
                  "Vente"),
           'js' => array('required' => true)
);
$opts['fdd']['date_commande'] = array(
         'name' => 'Date commande',
       'select' => 'T',
       'maxlen' => '10',
         'sort' => true
);
$opts['fdd']['date_paiement'] = array(
         'name' => 'Date paiement',
       'select' => 'T',
      'options' => 'VDLR',
       'maxlen' => '10',
         'sort' => true
);
$opts['fdd']['pa_code'] = array(
         'name' => 'Partner',
       'select' => 'T',
       'maxlen' => '8',
           'js' => array('required' => true),
       'values' => array('table'  => 'partners',
                         'column' => 'pa_code',
                         'description' => array('columns' => array('pa_code', 'pa_name', 'pa_type'),
                                                'divs'    => array (' - ',' (',')'))
                        ),
         'sort' => true
);
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80)
);

// possibly initialise page further before going to main function

if (function_exists('phpMyEditHeaderInit')) { phpMyEditHeaderInit($opts); }

// Now important call to phpMyEdit

echo '
<script>
    PME_js_setPageTitle("Commandes complétées");
</script>
';

new phpMyEdit($opts);

//eof

