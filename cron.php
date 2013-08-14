<?php
/**
 * 
 * Author: deshbandhu
 * Author URI: http:/mailin.fr
 */
set_time_limit(0);
ini_set('memory_limit', '64M');
$_SERVER['HTTP_HOST'] = 'wp_trunk';
ob_start();
$wp_load = '../../../wp-load.php';
if (!is_file($wp_load))
    exit;
require_once($wp_load);
require_once(ABSPATH.'wp-admin/includes/admin.php');
ob_end_clean();
if ($_GET['token'] != md5(get_option('mailin_apikey')))
    die('Invalid token');
function curlRequest($data)
{
    $url = 'http://ws.mailin.fr/'; //WS URL
    $ch  = curl_init();
    $ndata = '';
    if (is_array($data))
    {
        foreach ($data as $key => $value)
            $ndata .= $key.'='.urlencode($value).'&';
    } else
        $ndata = $data;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Expect:'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $ndata);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
$api_key = get_option('mailin_apikey');
$mailin_apikey_status = get_option('mailin_apikey_status');
$mailin_manage_subscribe = get_option('mailin_manage_subscribe');
if ($api_key == false || $mailin_apikey_status == 0 || $mailin_manage_subscribe == 0)
		exit;
function getListUsers($api_key, $list_ids)
{
    if ($api_key == '')
        return;
    $data = array();
    $data['webaction'] = 'DISPLAYLISTDATABLACK';
    $data['key'] = $api_key;
    $data['listids'] = $list_ids;
    $return = curlRequest($data);
    return json_decode($return);
}


global $wpdb;

$table = $wpdb->prefix.'mailin_subscribers ';
$api_key = get_option('mailin_apikey');
$mailin_apikey_status = get_option('mailin_apikey_status');
$mailin_manage_subscribe = get_option('mailin_manage_subscribe');
if ($api_key == false || $mailin_apikey_status == 0 || $mailin_manage_subscribe == 0)
    return false;
$lists = get_option('mailin_lists');
$lists = unserialize($lists);
$final_data = array();
foreach ($lists as $data)
    $final_data[] = $data->id;
$list_ids = '';
if (!empty($final_data))
    $list_ids = implode('|', $final_data);
if ($list_ids == '')
    return;
$list_users = getListUsers($api_key, $list_ids);
if (!empty($list_users->result))
{
    foreach ($list_users->result as $key => $lists)
    {
        if (!empty($lists))
        {
            foreach ($lists as $users)
            {
                if (isset($users->blacklisted))
                {
                    if ($users->blacklisted == '1')
                        $sql = 'UPDATE '.$table.' SET subscribed = "0" WHERE email = "'.strtolower(trim($users->email)).'" ';
                    else
                        $sql = 'UPDATE '.$table.' SET subscribed = "1" WHERE email = "'.strtolower(trim($users->email)).'" ';              
                       $myrows = $wpdb->query($sql);
                }
            }
        }
    }
    echo 'Cron executed successfully';
}
