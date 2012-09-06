<?php
/**
 * Dummy CLI script that loads the WordPress environment
 * Author: Thorsten Ott
 * Author URI: http://hitchhackerguide.com
 */
set_time_limit( 0 );
ini_set( "memory_limit", "64M" );
$_SERVER['HTTP_HOST'] = 'wp_trunk'; // set this to the apache vhost name of your WordPress install

ob_start();
$wp_load =  '../../../wp-load.php';

//STOP CRON EXECUTION WHEN WORDPRESS IS NOT LOADED ON THIS FILE
if(!is_file($wp_load)){
    exit;
}

require_once($wp_load); // you need to adjust this to your path
require_once( ABSPATH . 'wp-admin/includes/admin.php' );
ob_end_clean();


function curl_request($data) {

  $url = 'http://ws.mailin.fr/'; //WS URL
  $ch = curl_init();

  $ndata='';

  if(is_array($data)){

      foreach($data AS $key=>$value){
          $ndata .=$key.'='.urlencode($value).'&';
      }

  }else{

      $ndata=$data;

  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
  curl_setopt($ch, CURLOPT_POST      ,1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS,$ndata);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

$api_key = get_option('mailin_apikey');

if($api_key == ''){
    exit;
}


function call_server($email, $api_key){

    $data = array();
    $data['webaction']='DISPLAYUSERDETAIL';
    $data['key']= $api_key;
    $data['email'] = $email;

    $return = json_encode(curl_request($data));
    $return = json_decode($return);
    return $return;

}

function getListUsers($api_key, $list_ids){

    if($api_key == '')  {
      return ;
    }

    $data = array();
    $data['webaction']='DISPLAYLISTDATABLACK';
    $data['key']=$api_key;

    $data['listids']= $list_ids;

    $return = curl_request($data);
    $return = json_decode($return);

    return $return;

}


global $wpdb;
$table = $wpdb->prefix."mailin_subscribers ";


$api_key = get_option('mailin_apikey');

$lists = get_option('mailin_lists');
$lists = unserialize($lists);

$final_data = array();
foreach($lists as $data){
         $final_data[] = $data->id;
}

$list_ids = '';
if(!empty($final_data)){
      $list_ids = implode('|' , $final_data);
}

if($list_ids == ''){
      return ;
}

$list_users = getListUsers($api_key , $list_ids);


if(!empty($list_users->result)){

    foreach($list_users->result as $key=>$lists){

        if(!empty($lists)){

            foreach($lists as $users){

                if(isset($users->blacklisted)){

                    if($users->blacklisted == '1'){
                       $sql = "UPDATE ".$table." SET subscribed = '0' WHERE email = '".strtolower(trim($users->email))."' " ;
                    }else{
                       $sql = "UPDATE ".$table." SET subscribed = '1' WHERE email = '".strtolower(trim($users->email))."' " ;
                    }
                    $myrows = $wpdb->query($sql);
                }
            }
        }
    }
}
