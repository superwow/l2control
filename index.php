<?php
session_start();

define ('_ACM_VALID', 1);

require './classes/config.class.php';
require './config.php';
require './libs/Smarty.class.php';
require './classes/system.class.php';
require './classes/mysql.class.php';
require './classes/database.class.php';
require './classes/smtp.class.php';
require './classes/phpmailer.class.php';
require './classes/email.class.php';
require './classes/core.class.php';
require './classes/account.class.php';
require './classes/world.class.php';
require './classes/character.class.php';

header("Content-Type: text/html; charset=".CONFIG::g()->core_iso_type);

if(file_exists('./install.php'))
	echo('<div style="font-size: 20px; background-color: #FFF; color:#000;"><strong><center><br />Warning: The install file can be see. Please delete install.php before start ACM on your live server.<br /><br /></center></strong></div>');

SmartyObject::getInstance()->assign('session_id', '');

if(SID != '') {
	SmartyObject::getInstance()->assign('session_id', '?'.SID);
	DEBUG::add(LANG::getInstance()->i18n('_cookie_prob'));
}

$action = (!empty($_GET['action'])) ? $_GET['action'] : 'index';
$action = (!empty($_POST['action'])) ? $_POST['action'] : $action;

$action = htmlentities($action);
$action = htmlspecialchars($action);

//------------------------------------------------------------------
// Display
//------------------------------------------------------------------

SmartyObject::getInstance()->assign('vm_title', LANG::getInstance()->i18n('_title'));
SmartyObject::getInstance()->assign('vm_title_page', LANG::getInstance()->i18n('_title_page'));

SmartyObject::getInstance()->assign('vm_charset_type', CONFIG::g()->core_iso_type);

// Navigation labels for l2moon theme
SmartyObject::getInstance()->assign('vm_nav_home', LANG::getInstance()->i18n('_nav_home'));
SmartyObject::getInstance()->assign('vm_nav_stats', LANG::getInstance()->i18n('_nav_stats'));
SmartyObject::getInstance()->assign('vm_nav_top', LANG::getInstance()->i18n('_nav_top'));
SmartyObject::getInstance()->assign('vm_nav_map', LANG::getInstance()->i18n('_nav_map'));
SmartyObject::getInstance()->assign('session_url', (SID != '') ? '&'.SID : '');

$core = new CORE();

if(method_exists($core, $action))
	$core->$action();
else
	$core->index();

SmartyObject::getInstance()->display();

?>