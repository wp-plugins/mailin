<?php

/**
Plugin Name: Mailin
Plugin URI: http://mailin.fr
Description: The Mailin plugin provides quick and easy way to synchronize subscribers from wordpress site to Mailin website account and vice versa.
Version: 1.0.0
Author: Mailin.fr
Author URI: http://www.mailin.fr
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//Mailin version
define('MAILIN_VER', '1.0.0');

//Initiallize mailin constants
Initiallize_mailin_constants();


// Get Mailin API class in application domain
if (!class_exists('mailin_API'))
{
  require_once('mailinapi.class.php');
}

// Include Compatibility functions
require_once('compatibility.php');


/**
 * Initillize mailin plugin
 * @return void
 */
function mailin_init()
{

  mailin_load_resources();

  //Internationalize the plugin
  $i18n_file_name = 'mailin_lang';
  $locale = apply_filters( 'plugin_locale', get_locale(), $i18n_file_name);

  $filename = MAILIN_LANG_DIR.$i18n_file_name.'-'.$locale.'.mo';
  load_textdomain('mailin_i18n', $filename);

}

add_action( 'init', 'mailin_init');


//Initilize Widget
include_once('mailin_widget.php');

/**
 * Loads JS and CSS
 * @return void
 */
function mailin_load_resources()
{
    wp_enqueue_style('mailin_wp_css', MAILIN_URL.'css/mailin_plugin.css');
}



/**
 * Loads Mailin Admin page css
 * @return void
 */
function mailin_load_resources_admin()
{
    wp_enqueue_style('mailin_admin_css', MAILIN_URL.'css/admin.css');
}

add_action('load-settings_page_mailin_options', 'mailin_load_resources_admin');



/**
 * Update lists and campaigns on refreshing a page
 *
 */

$mObj = new mailin_API();
$api_key = get_option('mailin_apikey');


if(is_admin() &&  isset($_GET['page']) &&  $_GET['page'] == 'mailin_options' &&  $api_key != '' && $_SERVER['REQUEST_METHOD'] == 'GET'){

    $mObj->updateUserLists($api_key);
    $mObj->updateUserCampaigns($api_key);

}

/**
 * Gets or sets error/success message
 * @return string/bool depending on get/set
 **/
function mailin_messages($msg = null)
{

  global $mailin_msg;

  if (!is_array($mailin_msg))
  {
      $mailin_msg = array();
  }

  if (is_null($msg))
  {
      return implode('', $mailin_msg);
  }

  $mailin_msg[] = $msg;
  return true;
}


/**
 * Handles forms submit
 * @return void , sets error/success message
 **/
function mailin_form_submit()
{

  $action =  '';

  if(isset($_POST['mailin_form_action']))
  {
      $action = trim($_POST['mailin_form_action']);
      if($action == '')
      {
          return;

      }elseif($action == 'sync_users') {

          $mObj = new mailin_API();
          $mObj->syncUsers();

          $message = '<p class="mailin_success" >'.__('Users synchronized successfully', 'mailin_i18n').'</p>';
          mailin_messages($message);

      }elseif($action == 'list_details') {

          $mObj = new mailin_API();
          $listDetails = $mObj->getListDetails($_POST['list_id']);

      }elseif($action == 'apikey_update') {

          $mObj = new mailin_API();
          $mObj->handle_apikey_form_submit(strip_tags(stripslashes($_POST['mailin_apikey'])));

          if(empty($mObj->_mailin_error))
          {

              $message = implode('<br/>', $mObj->_mailin_success);
              $message = '<p class="mailin_success" >'.$message.'</p>';
              mailin_messages($message);

          }else{

              $message = implode('<br/>', $mObj->_mailin_error);
              $message = '<p class="mailin_error" >'.$message.'</p>';
              mailin_messages($message);

          }
      }elseif($action == 'logout') {

          $mObj = new mailin_API();
          $mObj->handle_logout_form_submit();


      }elseif($action == 'update_list'){

          $mailin_list = isset($_POST['mailin_list']) ? $_POST['mailin_list'] : '' ;

          $mObj = new mailin_API();
          $mObj->handle_updatelist_form_submit($mailin_list);

          if(!empty($mObj->_mailin_error))
          {
              $message = implode('<br/>', $mObj->_mailin_error);
              $message = '<p class="mailin_error" >'.$message.'</p>';
              mailin_messages($message);
          }

          if(!empty($mObj->_mailin_success))
          {
              $message = implode('<br/>', $mObj->_mailin_success);
              $message = '<p class="mailin_success" >'.$message.'</p>';
              mailin_messages($message);
          }

      }elseif($action == 'update_campaigns') {

          $mailin_list = isset($_POST['mailin_list']) ? $_POST['mailin_list'] : '' ;

          $mObj = new mailin_API();
          $mObj->updateUserCampaigns();

          $message = '<p class="mailin_success" >'.__('Campaigns updated successfully', 'mailin_i18n').'</p>';
          mailin_messages($message);


      }elseif($action == 'subscribe_form_submit'){

          $mObj = new mailin_API();
          $mObj->handle_newsletter_subscribe_submit();

          if(!empty($mObj->_mailin_error))
          {
              $message = implode('<br/>', $mObj->_mailin_error);
              $message = '<p class="mailin_error" >'.$message.'</p>';
              mailin_messages($message);
          }

          if(!empty($mObj->_mailin_success))
          {
              $message = implode('<br/>', $mObj->_mailin_success);
              $message = '<p class="mailin_success" >'.$message.'</p>';
              mailin_messages($message);
          }
      }
  }
}


add_action('init', 'mailin_form_submit');

/**
 * Loads the view of Mailin admin page
 * @return void
 **/
function mailin_settings_admin_page()
{

  // CHECK IF API KEY EXISTS
  $api_key = get_option('mailin_apikey');

  ?>
  <div class="wrap">
      <div style="float:left;margin-bottom:10px;width:670px;">
          <div class="icon32" id="icon-options-general"><br></div>
          <h2><?php esc_html_e('Mailin Setup', 'mailin_i18n');?> </h2>
      </div>

    <?php
    if (mailin_messages() != '')
    {
    ?>
      <div id="mc_message" class="mailin_row"><?php echo mailin_messages(); ?></div>
    <?php
    }

    if($api_key != '')
    {
        require "listings.php";
    }else{
        require "api_form.php";
    }
    ?>

   </div>
   <?php
}

add_action('admin_menu', 'admin_menu');


/**
 * Adds setup navigation link under settings
 * @return void
 **/
function admin_menu()
{
    add_options_page('Mailin Setup', 'Mailin setup', 'administrator', 'mailin_options', 'mailin_settings_admin_page');
}



/**
 * Links Mailin setup page to URL
 * @return setup page link
 **/
function mailin_action_links($links)
{
    $settings_page = add_query_arg(array('page' => 'mailin_options'), admin_url('options-general.php'));
    $settings_link = '<a href="'.esc_url($settings_page).'">'.__('Settings', 'Mailin setup' ).'</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'mailin_action_links', 10, 1);


/**
 * Sets up Plugin URL , Plugin directory , Plugin Language Directory
 * SETUP subscriber table name
 * @return setup page link
 **/
function Initiallize_mailin_constants()
{

    $locations = array(
      'plugins' => array(
        'dir' => WP_PLUGIN_DIR,
        'url' => plugins_url()
      ),
      'template' => array(
        'dir' => trailingslashit(get_template_directory()).'plugins/',
        'url' => trailingslashit(get_template_directory_uri()).'plugins/',
      )
    );

    $mailin_dirbase = trailingslashit(basename(dirname(__FILE__)));
    $mailin_dir = trailingslashit(WP_PLUGIN_DIR).$mailin_dirbase;
    $mailin_url = trailingslashit(WP_PLUGIN_URL).$mailin_dirbase;

    foreach ($locations as $key => $loc)
    {
        $dir = trailingslashit($loc['dir']).$mailin_dirbase;
        $url = trailingslashit($loc['url']).$mailin_dirbase;
        if (is_file($dir.basename(__FILE__)))
        {
            $mailin_dir = $dir;
            $mailin_url = $url;
            break;
        }
    }

    //MAILIN DIRECTORY PATH
    define('MAILIN_DIR', $mailin_dir);

    //MAILIN LANGUAGE DIRECTORY PATH
    define('MAILIN_LANG_DIR', trailingslashit(MAILIN_DIR).'lang/');

    //MAILIN PLUGIN DIRECTORY PATH
    define('MAILIN_URL', $mailin_url);

    global $wpdb;

    //Subscribers table name
    define('MAILIN_SUBSCRIBERS', $wpdb->prefix.'mailin_subscribers');

}

/*
 * Create subscribers table upon installation
*/
function mailin_install()
{

    global $wpdb;
    $sql = "CREATE TABLE ".MAILIN_SUBSCRIBERS." (
    id int(11) NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) DEFAULT '' NOT NULL,
    fname VARCHAR(55) DEFAULT '' NOT NULL,
    lname VARCHAR(55) DEFAULT '' NOT NULL,
    list VARCHAR(255) DEFAULT '' NOT NULL,
    subscribed TINYINT(1) DEFAULT '1' NOT NULL,
    create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    UNIQUE KEY id (id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


/*
 * Called plugin is removed
 * We do not remove previous data
*/
function mailin_remove()
{


}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'mailin_install');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'mailin_remove' );


class siteUsers
{

    static function init()
    {
        add_action( 'user_register', array( __CLASS__, 'register_newly_added_user' ) );
    }

    /*
    * This function is called when a new user is created
    * User is added in mailinlist
    */
    static function register_newly_added_user( $user_id = null )
    {

        if($user_id != null)
        {
            $info = get_userdata( $user_id );

            if(is_object($info))
            {
                if(isset($info->data->user_email) && $info->data->user_email != '')
                {
                    $user_nicename = isset($info->data->user_nicename) ? $info->data->user_nicename : '';
                    $selected_list = get_option('mailin_list_selected');
                    $mObj = new mailin_API();
                    $mObj->updateSubscribers($info->data->user_email , $selected_list, $user_nicename  , '' );
                }
            }
        }
        wp_update_user( $args );
    }

}

siteUsers::init();


?>
