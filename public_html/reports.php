<?php
include(dirname(__FILE__).'/phpMyEditHeader.php');
?>

<?php

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

function phpMyEditPageHeader(&$inst) {
    if (in_array($inst->{'page_type'}, array('C'))) {
        $report = $inst->QueryDB("SELECT * FROM `reports` WHERE `rowid` = " . $inst->{'rec'});
        $title = $report['re_name'];
    } else {
        $title = 'Rapports';
    }

    $inst->{'labels'}['Apply'] = 'Mettre à jour le rapport';

echo '
<script>
    PME_js_setPageTitle("' . $title . '");
</script>
';
}

function phpMyEditPageFooter($inst) {
    if (in_array($inst->{'page_type'}, array('C'))) {        
        $report = $inst->QueryDB("SELECT * FROM `reports` WHERE `rowid` = " . $inst->{'rec'});

        /*
        echo "<hr class='gradientline' data-caption='Query'>\n";
        PrintAssociateLine($report);
        */

        echo "<hr class='gradientline' data-caption='Report'>\n";

        $where =  " WHERE `date_commande` >= str_to_date('" .
                $report['date_debut'] . "','%Y-%m-%d')";
        if (!empty($report['date_fin'])) {
            $where .= " AND `date_commande` <= str_to_date('" .
                $report['date_fin'] . "','%Y-%m-%d')";
        }
        $where .= " AND `co_type` = '" . $report['re_type'] . "'";

        $sql = "SELECT `commandes`.`rowid` AS 'Commande',
                `date_commande`,
                concat(`commandes`.`pa_code`, ' - ', `pa_name`) AS 'Partner',
                `date_paiement` AS 'Payé le',
                `prixtotal` AS 'Total'
         FROM `commandes`, `partners` " . $where . "
            AND `commandes`.`pa_code` = `partners`.`pa_code`";

        echo "<br />\n";
        PrintAssociateTable($inst, $sql);

        $sql = "SELECT `commande_id` AS 'Commande', 
                        concat(`comdetails`.`pr_code`, ' - ' , `pr_name`) AS 'Product',
                        concat(`quantite`, ' ', `pr_unite`) AS 'Volume'
                FROM `comdetails`, `products`
                      WHERE `commande_id` IN (SELECT `commandes`.`rowid`
                      FROM `commandes` " . $where .")
                      AND `comdetails`.`pr_code` = `products`.`pr_code`";

        echo "<br />\n";
        PrintAssociateTable($inst, $sql);

        $sql = "SELECT sum(`prixtotal`) AS 'Total' FROM `commandes` " . $where;

        echo "<br />\n";
        PrintAssociateTable($inst, $sql);
    }

    $sql = "SELECT `re_name`, `re_type` FROM `reports`";
    echo "<br />\n";
    PrintAssociateTable($inst, $sql);
}

function PrintAssociateLine($report) {
    echo "<table class='pme-main'><tr class='pme-header'>\n";
    foreach($report as $key => $cell) echo "<th class='pme-header'>$key</th>";
    echo '</tr><tr>' . "\n";
    foreach($report as $key => $cell) echo "<td class='pme-value'>$cell</td>";
    echo '</tr></table>' . "\n";
}

function PrintAssociateTable($inst, $sql) {
    $rows = $inst->FetchDB($sql, 'a');
    echo "<table class='pme-main'><tr class='pme-header'>\n";
    foreach($rows[0] as $key => $cell) echo "<th class='pme-header'>$key</th>";
    echo '</tr>' . "\n";
    foreach ($rows as $row) {
        echo "<tr class='pme-row'>";
        foreach($row as $key => $cell) echo "<td class='pme-value'>$cell</td>";
        echo "</tr>\n";
    }
    echo '</table>' . "\n";
}

$opts['tb'] = 'reports';

// custom settings overwriting general edit defaults
$opts['display']['query'] = false;
$opts['display']['sort'] = false;
$opts['options'] = 'CL';
$opts['navigation'] = 'DT';
$opts['cgi']['prefix']['sys'] = '';
$opts['cgi']['prefix']['operation'] = '';

$opts['buttons']['C']['down'] = array('more',
    array( 'name' => 'cancel', 'value' => 'Retour vers la liste' ));

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
      'options' => 'VDR',
       'maxlen' => '10'
);
$opts['fdd']['re_name'] = array(
         'name' => 'Rapport',
       'select' => 'T',
      'options' => 'L',
          'css' => array('postfix' => 'detailslink'),
      'URLdisp' => '$value',
          'URL' => '$page?fl=0&fm=0&sfn[0]=0&operation=Change&rec=$key',
       'maxlen' => '60'
);
$opts['fdd']['re_type'] = array(
         'name' => 'Type',
       'select' => 'T',
      'options' => 'LR',
       'maxlen' => '11',
       'values' => array(
                  "Fabrication",
                  "Achat",
                  "Vente")
);
$opts['fdd']['date_debut'] = array(
         'name' => 'Date debut',
       'select' => 'T',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
         'sqlw' => 'IF($val_qas = "", NULL, $val_qas)'
);
$opts['fdd']['date_fin'] = array(
         'name' => 'Date fin',
       'select' => 'T',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
         'sqlw' => 'IF($val_qas = "", NULL, $val_qas)'
);

// Now important call to phpMyEdit

new phpMyEdit_MultiQuery($opts);

//eof

