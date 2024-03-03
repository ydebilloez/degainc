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
        <!--
menu.pme-menu li.not-horizontal {
    display: inherit;
}
menu.pme-menu li.not-vertical {
    display: none;
}
form.pme_login_logout input {
    width: 80%;
    padding: 10px;
    margin: 10px 0px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
form.pme_login_logout button {
    padding: 10px;
    margin: auto;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
}
#pme_login_err {
    border-radius: 5px;
    color: white;
    background-color: red;
    padding: 0px 5px;
}
form.pme_login_logout button.pme_logout_btn {
    background-color: red;
}
    -->
    </style>
</head>
<?php
require_once dirname(__FILE__) . "/lib/sessioncontroller.php";

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
<body class='pme-main force-landscape'>
    <h1>Dega Inc.</h1>
    <p class='portrait-alert'>Veuillez utiliser ce système en mode paysage.</p>
    <hr class='gradientline' data-caption='Agent vendeur' />
    <?php include(dirname(__FILE__).'/menu-seller.inc'); ?>
    <hr class='gradientline' data-caption='Functions utilisateur' />
    <?php include(dirname(__FILE__).'/menu-user.inc'); ?>
    <hr class='gradientline' data-caption='Gestion système' />
    <?php include(dirname(__FILE__).'/menu-admin.inc'); ?>
<section id='pme_login_logout'>
    <?php
        // check if the user is logged in
        if (!sessionCtl_isLoggedIn()) {
    ?>
    <hr class='gradientline' data-caption='Switch to administration mode' />
        <form id="pme_login" class="pme_login_logout">
        <input type="hidden" name="action" value="login" />
        <div>
            <label for="pme_user">Utilisateur</label>
            <input type="text" name="pme_user" id="pme_user" placeholder="Utilisateur" required />
        </div>
        <div>
            <label for="pme_password">Mot de passe</label>
            <input type="password" name="pme_password" id="pme_password" placeholder="Mot de passe" required />
        </div>
        <div id="pme_login_err"></div>
        <button type="submit" class="pme_login_btn" id="connect_label" onclick='Connect(this.form, event);'>Connect</button>
        <!--
        <button type="submit" class="pme_login_btn" id="login_label" onclick='Login(this.form, event);'>Login</button>
        -->
        </form>
    <?php
        } else {
    ?>
    <hr class='gradientline' data-caption='Switch to user mode' />
        <form id="pme_logout" class="pme_login_logout">
        <input type="hidden" name="action" value="logout" />
        <button type="submit" class="pme_logout_btn" id="disconnect_label" onclick='Disconnect(this.form, event);'>Disconnect</button>
    <!--
        <button type="submit" class="pme_logout_btn" id="logout_label" onclick='Logout(this.form, event);'>Logout</button>
        </form>
    <hr class='gradientline' data-caption='Test' />
        <form id="pme_test" class="pme_login_logout" action="/api/login-logout.php" method="post">
        <input type="hidden" name="action" value="test" />
        <button type="submit" class="pme_login_btn" id="test_label">Test post</button>
        </form>
        <form id="pme_test2" class="pme_login_logout" action="/api/login-logout.php" method="get">
        <input type="hidden" name="action" value="test2" />
        <button type="submit" class="pme_login_btn" id="test2_label">Test get</button>
    -->
        </form>
    <?php
        }
    ?>
    </section>
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
    <script type="text/javascript">
        //<![CDATA[
function Connect(fo, ev) {
    ev.preventDefault();
    return pushFormToAPI(fo, '/api/login-logout.php', 'POST', CaptureAPIResult, CaptureError);
}
function Login(fo, ev) {
    ev.preventDefault();
    return pushFormToServer(fo, '/api/login-logout.php', 'POST', CaptureFormResult, CaptureError);
}
function Disconnect(fo, ev) {
    ev.preventDefault();
    // clear cookie client side as location reload will resubmit existing cookie
    let domain = location.host; // make sure to remove column if domain is using port
    document.cookie = 'pme_login_key=; Max-Age=0; path=/; SameSite=Strict; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=' + domain.split(':').shift();
    return pushFormToAPI(fo, '/api/login-logout.php', 'GET', CaptureAPIResult, CaptureError);
}
function Logout(fo, ev) {
    ev.preventDefault();
    return pushFormToServer(fo, '/api/login-logout.php', 'GET', CaptureFormResult, CaptureError);
}

function CaptureAPIResult(api, result) {
    //debug_log('CaptureAPIResult');
    if (result != null) debug_log(result);
    location.reload();
}

function CaptureError(api, result) {
    //debug_log('CaptureError');
    if (result != null) console.error(result);
}

function CaptureFormResult(api, result) {
    //debug_log('CaptureFormResult');
    if ((result != null) && (!result.ok)) debug_log(result);
}

function getAnyClass(obj) {
    if (typeof obj === "undefined") return "undefined";
    if (obj === null) return "null";
    return obj.constructor.name;
}

// push form to API
async function pushFormToAPI(formData, apiPath, methode = 'POST', callBack = null, callError = null) {
    if (getAnyClass(formData) != 'FormData') {
        formData = new FormData(formData);
    }
    pushFormDataToAPI(formData, apiPath, methode, callBack, callError);
}

// push formData to API
async function pushFormDataToAPI(formData, apiPath, methode = 'POST', callBack = null, callError = null) {
    let classData = {};
    formData.forEach((value, key) => classData[key] = value);
    pushJSONToAPI(classData, apiPath, methode, callBack, callError);
}

// push any class to API using JSON
async function pushJSONToAPI(classData, apiPath, methode = 'POST', callBack = null, callError = null) {
    let myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');
    myHeaders.append('Sec-Fetch-Site', 'cross-site');

    let requestParam = {
            method: methode,
            mode: 'cors',
            headers: myHeaders
    }
    if (methode != 'GET') {
        let classJSON = JSON.stringify(classData);
        //debug_log(classJSON);
        requestParam['body'] = classJSON;
    } else {
        // encode parameters on link
        apiPath += '?' + new URLSearchParams(classData);
    }
    fetch(apiPath, requestParam)
        .then(function(response) {
            //debug_log('pushJSONToAPI response');
            if (!response.ok) debug_log(response);
            return response.json();
        })
        .then(function(responseData) {
            //debug_log('pushJSONToAPI responseData');
            if (callBack != null) {
                callBack(apiPath, responseData);
            } else {
                console.log('No callback defined for ' + apiPath + ' call');
                if (!responseData.ok) debug_log(responseData);
            }
        })
        .catch(function(err) {
            if (callError != null) {
                callError(apiPath, err);
            } else {
                console.error('No error calback defined for ' + apiPath + ' call');
                debug_log(err);
            }

        });
}

// push form to server
async function pushFormToServer(form, serverPath, methode = 'POST', callBack = null, callError = null) {
    let myHeaders = new Headers();
    if ((methode == 'POST') || (methode == 'PUT')) {
        //myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');
        myHeaders.append('Content-Type', 'multipart/form-data');
    } else {
        //myHeaders.append('Content-Type', 'text/html; charset=UTF-8');
    }
    let formData = new FormData(form);

    let requestParam = {
            method: methode,
            mode: 'cors',
            headers: myHeaders
    }
    if (methode != 'GET') {
        debug_log(formData);
        requestParam['body'] = formData;
        debug_log(requestParam);
    } else {
        // encode parameters on links
        serverPath += '?' + new URLSearchParams(formData);
    }

    fetch(serverPath, requestParam)
        .then(function(response) {
            debug_log('pushFormDataToServer response');
            debug_log(response);
            if (!response.ok) debug_log(response);
            return response;
        })
        .then(function(responseData) {
            debug_log('pushFormDataToServer responseData');
            if (callBack != null) {
                callBack(serverPath, responseData);
                debug_log(response);
            } else {
                console.log('No callback defined for ' + serverPath + ' call');
                debug_log(responseData);
            }
        })
        .catch(function(err) {
            if (callError != null) {
                callError(serverPath, err);
            } else {
                console.error('No error calback defined for ' + serverPath + ' call');
                debug_log(err);
            }

        });
}

function debug_log(e) {
    //console.log(e);
}

        //]]>
    </script>
</html>