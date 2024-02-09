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

$opts['tb'] = 'operations';

// custom settings overwriting general edit defaults
$opts['cgi']['prefix']['data'] = 'operations_';
$opts['cgi']['persist'] = array('oper' => $_REQUEST['oper']);
$operation = $opts['cgi']['persist']['oper'];

if ($operation == 'Vente') {
    $opts['filters'] = "`commande_id` in (select `commandes`.`rowid` from `commandes` where `commandes`.`co_type` = 'Vente')"; //AND `date_paiement` IS NULL
    $filter = "`co_type` = 'Vente'";
    $title = 'Paiment par client';
} else {
    // operation = Livraison
    $opts['filters'] = "`commande_id` in (select `commandes`.`rowid` from `commandes` where `commandes`.`co_type` = 'Achat')"; //AND `date_paiement` IS NULL
    $filter = "`co_type` = 'Achat'";
    $title = 'Reglement fournisseur';
}

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
         'name' => 'Commande',
       'select' => 'T',
       'maxlen' => '10',
       'values' => array('table'  => 'commandes',
                         'column' => 'rowid',
                         'description' => array('columns' => array('date_commande', 'pa_code', 'commentaires'),
                                                'divs'    => array (' - ', ' - ')),
                         'filters' => $filter
                        ),
         'sort' => true
);
$opts['fdd']['date_operation'] = array(
         'name' => 'Date ' . $operation,
       'select' => 'T',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['value_operation'] = array(
         'name' => 'Montant ' . $operation,
       'select' => 'N',
       'maxlen' => '10',
      'default' => '0'
);
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
       'maxlen' => '255'
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
    PME_js_setPageTitle(" ' . $title . '");
</script>
';

// Now important call to phpMyEdit

new phpMyEdit($opts);

//eof

