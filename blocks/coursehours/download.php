<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
require_once('locallib.php');

$currentyear = required_param('currentyear', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
//$category = required_param('category', PARAM_INT);
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
block_coursehours_list_sheet($currentyear, $userid);
die;