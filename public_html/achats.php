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
 * This file was manually updated.
 */

require_once(dirname(__FILE__).'/lib/phpMyEdit.class.php');
require_once(dirname(__FILE__).'/lib/phpMyEditDB.php');
require_once(dirname(__FILE__).'/phpMyEditDefaults.php');

$opts['tb'] = 'commandes';

// custom settings overwriting general edit defaults
$opts['display']['sort'] = false;

// filter on subset
$opts['filters'] = "`co_type` = 'Achat' AND `date_paiement` IS NULL";

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
$opts['fdd']['date_commande'] = array(
         'name' => 'Date Achat',
       'select' => 'T',
      'default' => date('Y-m-d'),
       'maxlen' => '10',
         'sort' => true
);
$opts['fdd']['co_type'] = array(
         'name' => 'Operation',
       'select' => 'R',
      'options' => 'AVCPDR',
       'maxlen' => '5',
       'values' => array(
                  "Achat",
                  "Vente"),
      'default' => 'Achat',
           'js' => array('required' => true)
);
$opts['fdd']['pa_code'] = array(
         'name' => 'Fournisseur',
       'select' => 'T',
       'maxlen' => '8',
           'js' => array('required' => true),
       'values' => array('table'  => 'partners',
                         'column' => 'pa_code',
                         'description' => array('columns' => array('pa_code', 'pa_name', 'pa_type'),
                                                'divs'    => array (' - ',' (',')')),
                         'filters' => 'pa_type = "Fournisseur"'
                        ),
         'sort' => true
);
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80)
);
$opts['fdd']['articles'] = array(
         'name' => '# articles',
       'select' => 'T',
      'options' => 'VL',
          'css' => array('postfix' => 'detailsbutton'),
      'URLdisp' => 'Article(s): $value',
          'URL' => 'comdetails.php?ro=rw&commande_id=$key&operation=Achat'
);

// possibly initialise page further before going to main function

if (function_exists('phpMyEditHeaderInit')) { phpMyEditHeaderInit($opts); }

// Now important call to phpMyEdit

echo '
<script>
    PME_js_setPageTitle("Achats en cours");
</script>
';

new phpMyEdit($opts);

//eof

