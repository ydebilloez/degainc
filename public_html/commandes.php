<?php
include(dirname(__FILE__).'/phpMyEditHeader.php');

function phpMyEditPageFooter($inst) {
    $commande = $inst->{'rec'};
    if (in_array($inst->{'page_type'}, array('A', 'C', 'V', 'D'))) {
        $sql = "
SELECT concat(`comdetails`.`pr_code`, ' - ', `products`.`pr_name`) AS 'Produit',
        `quantite` AS 'Qte',
        `commentaires` AS 'Commentaires'
FROM `comdetails`, `products`
WHERE `products`.`pr_code` = `comdetails`.`pr_code`
  AND `commande_id` = $commande";
        $rows = $inst->FetchDB($sql, 'a');
        if ($rows) {
            echo "<br />\n";
            echo "<hr class='gradientline' data-caption='Details'>\n";
            echo "<table class='pme-main'><tr class='pme-header'>\n";
            foreach($rows[0] as $key => $cell) echo "<th class='pme-header'>$key</th>";
            echo '</tr>' . "\n";
            foreach ($rows as $row) {
                echo "<tr class='pme-row'>";
                foreach($row as $cell) echo "<td class='pme-value'>$cell</td>";
                echo "</tr>\n";
            }
            echo '</table>' . "\n";
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

$opts['tb'] = 'commandes';

// custom settings overwriting general edit defaults
$opts['display']['query'] = false;
$opts['display']['sort'] = false;

// page operations
$opts['cgi']['prefix']['data'] = 'commandes_';
$opts['cgi']['persist'] = array('oper' => $_REQUEST['oper'],
                                'status' => $_REQUEST['status']);

$opts['sort_field'] = array('date_commande');
if ($opts['cgi']['persist']['oper'] == 'List') {
    $oper = 'View';
    $opts['options'] = 'VF';
    if ($opts['cgi']['persist']['status'] == 'Open') {
        $title = "Commandes (achat et vente)";
        $opts['filters'] = "`date_paiement` IS NULL";
    } else {
        $title = "Commandes complétées";
        $opts['filters'] = "`date_paiement` IS NOT NULL";
        $opts['sort_field'] = array('date_commande','date_paiement');
    }
} else {
    if ($opts['cgi']['persist']['status'] == 'Closed') {
        $oper = 'View';
        $opts['options'] = 'VF';
    } else {
        $oper = 'Change';
        $opts['options'] = 'ACVDF';
    }
    $title = "Commandes " . $opts['cgi']['persist']['oper'];
    if ($opts['cgi']['persist']['status'] == '') {
        $opts['filters'] = "`co_type` = '" . $opts['cgi']['persist']['oper'] . "'";
    } else if ($opts['cgi']['persist']['status'] == 'Open') {
        $opts['filters'] = "`co_type` = '" . $opts['cgi']['persist']['oper'] . "' AND `date_paiement` IS NULL";
    } else {
        $opts['filters'] = "`co_type` = '" . $opts['cgi']['persist']['oper'] . "' AND `date_paiement` IS NOT NULL";      
        $opts['sort_field'] = array('date_commande','date_paiement');
    }
}

if ($opts['cgi']['persist']['oper'] == 'Achat') {
    $partner = 'Fournisseur';
} else if ($opts['cgi']['persist']['oper'] == 'Vente') {
    $partner = 'Client';
} else if ($opts['cgi']['persist']['oper'] == 'Fabrication') {
    $partner = 'Usine';
} else {
    $partner = 'Partner';
}

// Name of field which is the unique key
$opts['key'] = 'rowid';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

$opts['fdd']['co_type'] = array(
         'name' => 'Type',
       'select' => 'R',
       'maxlen' => '5',
       'values' => array(
                  "Achat",
                  "Vente"),
           'js' => array('required' => true)
);
if ($opts['cgi']['persist']['oper'] != 'List') { 
    $opts['fdd']['co_type']['options'] = 'AVCPDR';
    $opts['fdd']['co_type']['default'] = $opts['cgi']['persist']['oper'];
}
if ($partner == 'Usine') {
    $opts['fdd']['co_type']['values'] = array('Fabrication');
}

$opts['fdd']['rowid'] = array(
         'name' => 'ID',
       'select' => 'T',
      'options' => 'VDLR',
       'maxlen' => '10',
         'sort' => true
);

$opts['fdd']['date_commande'] = array(
         'name' => 'Date commande',
       'select' => 'T',
      'default' => date('Y-m-d'),
       'maxlen' => '10',
         'sort' => true
);
if ($opts['cgi']['persist']['oper'] != 'List') {
    $opts['fdd']['date_commande']['name'] = 'Date ' . $opts['cgi']['persist']['oper'];
}

if ($opts['cgi']['persist']['status'] == 'Closed') {
    $opts['fdd']['date_paiement'] = array(
             'name' => 'Date paiement',
           'select' => 'T',
          'default' => date('Y-m-d'),
          'options' => 'VDLR',
           'maxlen' => '10',
             'sort' => true
    );
}

$opts['fdd']['pa_code'] = array(
         'name' => $partner,
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
if ($opts['cgi']['persist']['oper'] != 'List') { 
    $opts['fdd']['pa_code']['values']['filters'] = '`pa_type` = "' . $partner .'"';
}
if ($partner == 'Usine') {
    $opts['cgi']['persist']['name'] = 'Centre de Fabrication';
}

$opts['fdd']['articles'] = array(
         'name' => 'Articles',
       'select' => 'T',
      'options' => 'VL',
          'css' => array('postfix' => 'detailsbutton'),
      'URLdisp' => $oper . ' $value article(s)',
          'URL' => 'comdetails.php?oper=' . $opts['cgi']['persist']['oper'] . '&commande_id=$key'
);
if ($oper == 'View') {
    $opts['fdd']['articles']['options'] = 'L';
}
if ($opts['cgi']['persist']['oper'] != 'Fabrication') {
    $opts['fdd']['prixtotal'] = array(
             'name' => 'Prix total',
           'select' => 'T',
          'options' => 'VDLR',
             'sort' => true
    );
}
$opts['fdd']['commentaires'] = array(
         'name' => 'Commentaires',
       'select' => 'T',
     'textarea' => array('rows' => 5, 'cols' => 80)
);

// Now important call to phpMyEdit

echo '
<script>
    PME_js_setPageTitle("' . $title . '");
</script>
';

new phpMyEdit_MultiQuery($opts);

//eof

