<?php 
$_SERVER['HTTP_HOST'] = 'wp_trunk';
$wp_load = '../../../wp-load.php';
if (!is_file($wp_load))
    exit;
require_once($wp_load);
require_once(ABSPATH.'wp-admin/includes/admin.php');
global $wpdb;
if (!class_exists('MailinApi'))
    require_once('mailinapi.class.php');
$m_obj  = new MailinApi();
if (isset($_POST))
{
	if ($_POST['token'] != md5(get_option('mailin_apikey')))
		die('Invalid token');
	$m_obj->manageSubscribe();
}
