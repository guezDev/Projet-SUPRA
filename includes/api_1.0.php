<?php
/*
	api.php : VERSION
	js : version
	html : version, css?vN, js?vN
	*/
define('VERSION', '0.1.0');
$__token_ok = FALSE;                // SECURITY CHECK !!!!

require_once 'params.php';
require_once 'db.php';
require_once 'utils.php';
require_once '1.0/check.php';       // MANDATORY !!!!
require_once '1.0/connection.php';
require_once '1.0/etudiants.php';
// require_once '1.0/xxx.php';


$app->get('/', function ($req, $resp) {
   return buildResponse($resp, 'Welcome to SUPRA API!');
});
?>