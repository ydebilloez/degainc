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

// custom settings
$opts['options'] = 'ACVD';
$opts['display']['sort'] = false;

$opts['tb'] = 'products';

// Name of field which is the unique key
$opts['key'] = 'pr_code';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'char';
// Sorting field(s)
$opts['sort_field'] = array('pr_code');

/* please refer to lib/phpMyEditInfo.php for additional options
   that can be added in this file
*/

$opts['fdd']['pr_code'] = array(
         'name' => 'Code',
       'select' => 'T',
       'maxlen' => '8',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['pr_name'] = array(
         'name' => 'Name',
       'select' => 'T',
       'maxlen' => '60',
           'js' => array('required' => true),
         'sort' => true
);
$opts['fdd']['pr_type'] = array(
         'name' => 'Type',
       'select' => 'C',
       'maxlen' => '5',
       'values' => array(
                  "Achat",
                  "Vente"),
           'js' => array('required' => true),
      'default' => 'Achat',
         'sort' => true
);
$opts['fdd']['pr_unite'] = array(
         'name' => 'Unité',
       'select' => 'T',
       'maxlen' => '10',
      'default' => 'Pce',
           'js' => array('required' => true),
       'values' => array('table'  => 'pme_symbols',
                         'column' => 'sy_code',
                         'description' => array('columns' => array('sy_value')),
                         'filters' => 'sy_name = "UNITS"'
                        )
);
$opts['fdd']['pr_quantite'] = array(
         'name' => 'Quantité',
       'select' => 'N',
       'maxlen' => '10',
      'default' => '1.00'
);
$opts['fdd']['pr_prixunite'] = array(
         'name' => 'Prix Unité',
       'select' => 'N',
       'maxlen' => '10',
      'default' => '0.00',
         'sort' => true
);
$opts['fdd']['status_code'] = array(
         'name' => 'Status code',
       'select' => 'T',
      'options' => 'VDR',
       'maxlen' => '1',
      'default' => 'C',
       'values' => array('table'  => 'pme_statuscodes',
                         'column' => 'code',
                         'description' => array('columns' => array('code', 'status_name'),
                                                'divs'    => array (' - '))
                        )
);
$opts['fdd']['pr_ingredients'] = array(
         'name' => '# ingredients',
       'select' => 'T',
      'options' => 'VL',
          'css' => array('postfix' => 'detailsbutton'),
      'URLdisp' => 'Ingredient(s): $value',
          'URL' => 'prodcomposition.php?ro=rw&pr_code=$key'
);


// possibly initialise page further before going to main function

if (function_exists('phpMyEditHeaderInit')) { phpMyEditHeaderInit($opts); }

echo '
<script>
    PME_js_setPageTitle("Produits");
</script>
';

// Now important call to phpMyEdit

new phpMyEdit($opts);

//eof

