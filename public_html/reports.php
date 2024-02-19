<?php
include(dirname(__FILE__).'/phpMyEditHeader.php');
include_once(dirname(__FILE__).'/functions.inc');

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
        $title = '';
    }

    $inst->{'labels'}['Apply'] = 'Mettre à jour le rapport';

echo '
<script>
    PME_js_setPageTitle("' . $title . '");
</script>
<style>
    table.pme-main.pme-list tr.pme-header th.pme-header,
    table.pme-main.pme-list tr.pme-row td.pme-navigation,
    table.pme-navigation tr.pme-navigation td.pme-stats,
    table.pme-navigation tr.pme-navigation td.pme-message {
        display: none;
    }
</style>
';
}

function phpMyEditPageFooter($inst) {
    if (in_array($inst->{'page_type'}, array('C'))) {        
        $report = $inst->QueryDB("SELECT * FROM `reports` WHERE `rowid` = " . $inst->{'rec'});
        $type = $report['re_type'];

        if ($type == 'Fabrication') {
            $commandeTitle = 'Batch';
        } else {
            $commandeTitle = 'Commande';
        }

        //PrintAssociateLine($report);

        echo "<hr class='gradientline' data-caption='Rapport " . $type . "'>\n";

        $where =  " WHERE `date_commande` >= str_to_date('" .
                $report['date_debut'] . "','%Y-%m-%d')";
        if (!empty($report['date_fin'])) {
            $where .= " AND `date_commande` <= str_to_date('" .
                $report['date_fin'] . "','%Y-%m-%d')";
        }
        $where .= " AND `co_type` = '" . $type . "'";

        $sql = "SELECT `commandes`.`rowid` AS '" . $commandeTitle . "',
                `date_commande`,
                concat(`commandes`.`pa_code`, ' - ', `pa_name`) AS 'Partner',
                `date_paiement` AS 'Payé le',
                FORMAT(`prixtotal`,2) AS 'Total'
         FROM `commandes`, `partners` " . $where . "
            AND `commandes`.`pa_code` = `partners`.`pa_code`";
        PrintAssociateTable($inst, $sql);

        $sql = "SELECT `commande_id` AS '" . $commandeTitle . "', 
                        concat(`comdetails`.`pr_code`, ' - ' , `pr_name`) AS 'Produit',
                        `quantite` AS 'Qté',
                        `pr_unite` AS 'Unité'
                FROM `comdetails`, `products`
                      WHERE `commande_id` IN (SELECT `commandes`.`rowid`
                      FROM `commandes` " . $where .")
                      AND `comdetails`.`pr_code` = `products`.`pr_code`";
        PrintAssociateTable($inst, $sql);

        if ($type != 'Fabrication') {
            $sql = "SELECT FORMAT(sum(`prixtotal`),2) AS 'Total' FROM `commandes` " . $where;
            PrintAssociateTable($inst, $sql);
        }

        if ($type == 'Vente') {
            echo "<hr class='gradientline' data-caption='Coût de production'>\n";
            $sql = "

SELECT `ig`.`in_name` AS 'Ingrédient',
        FORMAT(SUM(`cd`.`quantite` * `pc`.`quantite`),2) as 'Volume',
        `ig`.`in_prixunite` AS 'Prix/unité',
        FORMAT(SUM(`cd`.`quantite` * `pc`.`quantite`) * `ig`.`in_prixunite`,2) AS 'Coût'
FROM `comdetails` cd, `prodcomposition` pc, `ingredients` ig
WHERE `commande_id` IN (SELECT `commandes`.`rowid` FROM `commandes` " . $where .")
    AND `cd`.`pr_code` = `pc`.`pr_code`
    AND `pc`.`in_code` = `ig`.`in_code`
GROUP BY `ig`.`in_code`

UNION

SELECT  '<strong>Total</strong>' AS 'Ingrédient',
        '' as 'Volume',
        '' AS 'Prix/unité',
        FORMAT(SUM(`cd`.`quantite` * `pc`.`quantite` * `ig`.`in_prixunite`),2) AS 'Coût'
FROM `comdetails` cd, `prodcomposition` pc, `ingredients` ig
WHERE `commande_id` IN (SELECT `commandes`.`rowid` FROM `commandes` " . $where .")
    AND `cd`.`pr_code` = `pc`.`pr_code`
    AND `pc`.`in_code` = `ig`.`in_code`";

            PrintAssociateTable($inst, $sql);
        }
    }
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

$opts['fdd']['rowid'] = array(
         'name' => 'ID',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '10'
);
$opts['fdd']['re_name'] = array(
         'name' => 'Liste des Rapports',
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
      'options' => 'R',
       'maxlen' => '11',
       'values' => array(
                  "Fabrication",
                  "Achat",
                  "Vente")
);
$opts['fdd']['date_debut'] = array(
         'name' => 'Date debut',
       'select' => 'T',
      'options' => 'C',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
         'sqlw' => 'IF($val_qas = "", NULL, $val_qas)'
);
$opts['fdd']['date_fin'] = array(
         'name' => 'Date fin',
       'select' => 'T',
      'options' => 'C',
       'maxlen' => '10',
      'default' => date('Y-m-d'),
         'sqlw' => 'IF($val_qas = "", NULL, $val_qas)'
);

// Now important call to phpMyEdit

new phpMyEdit_MultiQuery($opts);

//eof

