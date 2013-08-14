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
	$api_key = get_option('mailin_apikey');
	$selected_list = get_option('mailin_list_selected');
	$fname = '';
	$lname = '';
	$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
	$timezone_format = date_i18n($timezone_format);
	if (!empty($_POST['email']) && $_POST['newsletter'] == 1)
	{
		$m_obj->createUser($api_key, $_POST['email'], $selected_list, $fname, $lname);
		$sql = 'UPDATE '.MAILIN_SUBSCRIBERS.'  
				SET list = "'.$selected_list.'", 
				subscribed = "1", 
				create_date= "'.$timezone_format.'" 
				WHERE email = "'.trim($_POST['email']).'" ';
	}else
	{
		$m_obj->unSubUser($api_key, $_POST['email']);
		$sql = 'UPDATE '.MAILIN_SUBSCRIBERS.'
				SET list = "'.$selected_list.'",
				subscribed = "0",
				create_date= "'.$timezone_format.'" WHERE email = "'.trim($_POST['email']).'" ';
	}
	$wpdb->query($sql);
}
