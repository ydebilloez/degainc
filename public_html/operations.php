<?php
include(dirname(__FILE__).'/phpMyEditHeader.php');

function phpMyEditPageHeader($inst) {
    if (in_array($inst->{'page_type'}, array('C', 'V', 'D'))) {
        $sql = "SELECT `date_operation` FROM `operations` WHERE `rowid` = " . (string) $inst->{'rec'};
        $row = $inst->QueryDB($sql);
        $transDate = date_create($row['date_operation']);
    }
    if ($inst->{'page_type'} == 'C') {
        // do not allow to delete transactions which are older than today
        if ($transDate < date_create("today")) {
            $inst->{'fdd'}['commentaires']['options'] = 'ACVDFR';
            $inst->{'buttons'}['C']['down'] = array(array('name' => 'cancel',
                                                          'value' => 'Ne sera pas modifié'));
        }
        // only allow comments to be modified
        $inst->{'fdd'}['commande_id']['options'] = 'ACVDFR';
        $inst->{'fdd'}['value_operation']['options'] = 'ACVDFR';
        $inst->{'fdd'}['date_operation']['options'] = 'ACVDFR';
        $inst->recreate_displayed();
    } else if ($inst->{'page_type'} == 'V') {
        // disable modifify button
        if ($transDate < date_create("today")) {
            $inst->{'buttons'}['V']['down'] = array('cancel');
        }
    } else if ($inst->{'page_type'} == 'A') {
        // do only list unpaid orders
        $inst->{'fdd'}['commande_id']['values']['filters'] .= " AND `date_paiement` IS NULL";
        $inst->recreate_displayed();
    } else if ($inst->{'page_type'} == 'D') {
        // do not allow to delete transactions which are older than today
        if ($transDate < date_create("today")) {
            $inst->{'buttons'}['D']['down'] = array(array('name' => 'cancel',
                                                          'value' => 'Ne peut pas etre effacé'));
        }
    }

    if ($inst->{'page_type'} == 'L') {
        $sql = "SELECT count(`rowid`) AS 'Cnt' FROM `commandes` WHERE `date_paiement` IS NULL
                AND " . $inst->{'fdd'}['commande_id']['values']['filters'];
        $row = $inst->QueryDB($sql);
        if ($row['Cnt'] == '0') {
            $inst->{'buttons'}['L']['down'] = array(array('name' => 'cancel', 'value' => 'Pas de transactions ouvertes'));
        }
    }
}

/*
 * IMPORTANT NOTE: This generated file contains only a subset of huge amount
 * of options that can be used with phpMyEdit. To get information about all
 * features offered by phpMyEdit, please check the documentation. It is available
 * on the phpMyEdit pages or in the manuals folder. Some information can also be
 * found in the examples/configoptions.md file.
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

require_once(dirname(__FILE__).'/lib/extensions/phpMyEdit-multiquery.class.php');
require_once(dirname(__FILE__).'/lib/phpMyEditDB.php');
require_once(dirname(__FILE__).'/phpMyEditDefaults.php');

$opts['tb'] = 'operations';

// custom settings overwriting general edit defaults
$opts['cgi']['prefix']['data'] = 'operations_';
$opts['cgi']['persist'] = array('oper' => $_REQUEST['oper']);
if (empty($opts['cgi']['persist']['oper'])) $opts['cgi']['persist']['oper'] = 'Vente';
$operation = $opts['cgi']['persist']['oper'];
$opts['options'] = 'ACVDF';

if ($operation == 'Vente') {
    $opts['filters'] = "`commande_id` in (SELECT `commandes`.`rowid` FROM `commandes` WHERE `commandes`.`co_type` = 'Vente')";
    $filter = "`co_type` = 'Vente'";
    $transTitle = 'Paiement reçu';
    $title = 'Paiments par client';
} else {
    // operation = Livraison
    $opts['filters'] = "`commande_id` in (SELECT `commandes`.`rowid` FROM `commandes` WHERE `commandes`.`co_type` = 'Achat')";
    $filter = "`co_type` = 'Achat'";
    $transTitle = 'Paiement effectué';
    $title = 'Reglements fournisseur';
}

// Name of field which is the unique key
$opts['key'] = 'rowid';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';
// Sorting field(s)
$opts['sort_field'] = array('rowid');

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
                         'description' => array('columns' => array('date_commande', 'pa_code', 'prixtotal', 'commentaires'),
                                                'divs'    => array (' - ', ' - ', '$ - ')),
                         'filters' => $filter
                        ),
         'sort' => true
);
$opts['fdd']['value_operation'] = array(
         'name' => $transTitle,
       'select' => 'N',
       'maxlen' => '10',
      'default' => '',
           'js' => array('required' => true),
         'help' => '$',
         'sort' => true
);
$opts['fdd']['date_operation'] = array(
         'name' => 'Date paiement',
       'select' => 'T',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
       'maxlen' => '255',
         'sort' => true
);

echo '
<script>
    PME_js_setPageTitle(" ' . $title . '");
</script>
';

// Now important call to phpMyEdit

new phpMyEdit_MultiQuery($opts);

//eof

