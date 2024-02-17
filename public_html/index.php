<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Access-Control-Allow-Origin" content="*" />
    <meta http-equiv="Cache-control" content="max-age=900, stale-while-revalidate=60" />
    <link rel="stylesheet" href="css/phpMyEdit.css" type="text/css" />
    <link rel="stylesheet" href="examples/phpMyEditExamples.css" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="images/pme-icon.png" />
    <script type="text/javascript" src="js/phpMyEdit.js"></script>
    <title>Dega Inc.</title>
    <!-- allow to embed page specific js/css -->
    <script type="text/javascript">
    </script>
    <style type="text/css">
/* override display go back to menu element */
menu.pme-menu li:first-child {
  display: none;
}
    </style>
</head>
<?php
function retrieveMyEditVersion() {
    $str = '>=5.7.6';
    $file = dirname(__FILE__) . '/doc/VERSION';
    if (@file_exists($file) && @is_readable($file)) {
        if (($f = fopen($file, 'r')) != false) {
            $str = trim(fread($f, 4096));
            fclose($f);
            if (strpos($str, ' ') !== false || strlen($str) > 9) {
                $str = '>5.7.5'; /* we capture error silently */
            }
        }
    }
    return $str;
}
?>
<body class='pme-main'>
    <h1>Dega Inc.</h1>
    <hr class='gradientline' data-caption='Agent vendeur' />
    <?php include(dirname(__FILE__).'/menu-seller.inc'); ?>
    <hr class='gradientline' data-caption='Functions utilisateur' />
    <?php include(dirname(__FILE__).'/menu-user.inc'); ?>
    <hr class='gradientline' data-caption='Gestion système' />
    <?php include(dirname(__FILE__).'/menu.inc'); ?>
    <hr class='gradientline' data-caption='Additional functions' />
    <p>Multi functional, intelligent data system. Based on version <span id='PME-version'>
        <?php echo retrieveMyEditVersion(); ?></span> of phpMyEdit.</p>
    <p>Start by setting up your data, reports will be generated based on available data.</p>
    <menu class='pme-menu'>
        <li />
        <li><a href='examples/getstarted-index.php'>System configuration</a></li>
        <li />
    </menu>
</body>
</html>