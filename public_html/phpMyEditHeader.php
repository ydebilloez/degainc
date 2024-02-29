<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Dega System Inc.</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/pme-icon.png" />
    <link rel="stylesheet" href="css/phpMyEdit.css" />
    <link rel="stylesheet" href="examples/phpMyEditExamples.css" type="text/css" />
    <script type="text/javascript" src="js/phpMyEdit.js"></script>
    <style type="text/css">
/* override default vertical menu layout */
menu.pme-menu li {
  display: inline-block;
}
menu.pme-menu li:not(:last-child) {
  padding-right: 10px;
}
    </style>
</head>
<body class='pme-main force-landscape'>

<?php
include(dirname(__FILE__).'/menu-seller.inc');
$menutype = $_REQUEST['menu'];
if (isset($menutype)) {
    include(dirname(__FILE__).'/menu-'.$menutype.'.inc');
}
?>

<h3 id='PME-pagetitle'>PME Header - Table name</h3>

<p class='portrait-alert'>Veuillez utiliser ce système en mode paysage.</p>

<hr class='gradientline' />
