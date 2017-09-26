<?php

if (empty($_REQUEST['mscdek_action'])) {
	die('Access denied');
}

//if (!empty($_REQUEST['action'])) {$_REQUEST['ms2_action'] = $_REQUEST['action'];}

require dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';