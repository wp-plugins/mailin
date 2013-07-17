<?php
/**
Plugin Name: Mailin
Plugin URI: http://mailin.fr
Description: Synchronize your WordPress contacts with Mailin platform and send transactional emails easily to your customers.
Version: 1.5
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
define('MAILIN_VER', '1.0.0');
define('WP_EMAIL_TEMPLATE_FOLDER', dirname(plugin_basename(__FILE__)));
define('WP_EMAIL_TEMPLATE_DIR', WP_CONTENT_DIR.'/plugins/'.WP_EMAIL_TEMPLATE_FOLDER);
initiallizeMailinConstants();
if (!class_exists('MailinApi'))
    require_once('mailinapi.class.php');
require_once('compatibility.php');
/**
 * init mailin plugin
 */
function mailinInit()
{
    mailinLoadResources();
    //Internationalize the plugin
    $i18n_file_name = 'mailin_lang';
    $locale         = apply_filters('plugin_locale', get_locale(), $i18n_file_name);
    $filename       = MAILIN_LANG_DIR.$i18n_file_name.'-'.$locale.'.mo';
    load_textdomain('mailin_i18n', $filename);
}
add_action('init', 'mailinInit');
include_once('mailin_widget.php');
/**
 * Loads the appropriate JS and CSS resources depending on
 * settings and context (admin or not) *
 * @return void
 */
function mailinLoadResources()
{
    wp_enqueue_style('mailin_wp_css', MAILIN_URL.'css/mailin_plugin.css');
}

/**
 * Loads resources for the Mailin admin page
 * @return void
 */
function mailinLoadResourcesAdmin()
{
    wp_enqueue_style('mailin_admin_css', MAILIN_URL.'css/admin.css');
}
add_action('load-settings_page_mailin_options', 'mailinLoadResourcesAdmin');
/**
 *
 * @param initialize smpt setting
 */
function wpSmtp($phpmailer)
{
    $admin_info = get_userdata(1);
    if (!get_option('mailer') || (get_option('mailer') == 'smtp' && !get_option('mailin_smtp_host')))
        return;
    $phpmailer->Mailer = 'smtp';
    if (defined('WPMS_SET_RETURN_PATH'))
        $phpmailer->From = WPMS_SET_RETURN_PATH;
    else
        $phpmailer->From = $admin_info->user_email;
    
    if (defined('WPMS_MAIL_FROM_NAME'))
        $phpmailer->FromName = WPMS_MAIL_FROM_NAME;
    else
        $phpmailer->FromName = $admin_info->display_name;
    $phpmailer->Sender = $phpmailer->From; //Return-Path
    $phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To
    $phpmailer->Host       = get_option('mailin_smtp_host');
    $phpmailer->SMTPSecure = get_option('mailin_smtp_ssl') == 'none' ? '' : get_option('mailin_smtp_ssl');
    $phpmailer->Port       = get_option('mailin_smtp_port');
    $phpmailer->SMTPAuth   = get_option('mailin_smtp_auth');
    //$phpmailer->SMTPDebug = 2;
    if ($phpmailer->SMTPAuth)
    {
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = get_option('mailin_smtp_user');
        $phpmailer->Password = get_option('mailin_smtp_pass');
    }
}

$mailinsmtp           = get_option('mailin_smtp');
$api_key              = get_option('mailin_apikey');
$mailin_apikey_status = get_option('mailin_apikey_status');


if ($mailinsmtp == 1 && $api_key != '' && $mailin_apikey_status == 1)
    add_action('phpmailer_init', 'wpSmtp');
register_activation_hook(__FILE__, 'mailinsmtpActivation');
/**
 * Mailin smtp with default value
 */
function mailinsmtpActivation()
{
    $mailinsmtp_options = array('mail_from' => '',
								'mail_from_name' => '',
								'mailer' => 'mail',
								'mail_set_return_path' => 'false',
								'mailin_smtp_host' => 'localhost',
								'mailin_smtp_port' => '25',
								'mailin_smtp_ssl' => 'none',
								'mailin_smtp_auth' => false,
								'mailin_smtp_user' => '',
								'mailin_smtp_pass' => '');
    // Create the required options...
    foreach ($mailinsmtp_options as $name => $val)
        add_option($name, $val);
}
/**
 * Update lists and campaigns on refreshing a page
 *
 */
$m_obj    = new MailinApi();
$api_key = get_option('mailin_apikey');
if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'mailin_options' && $api_key != '' && $_SERVER['REQUEST_METHOD'] == 'GET')
    $m_obj->updateUserLists($api_key);

/**
 * Gets or sets message
 * @return string/bool depending on get/set
 **/
function mailinMessages($msg = null)
{
    global $mailin_msg;
    if (!is_array($mailin_msg))
        $mailin_msg = array();
    if (is_null($msg))
        return implode('', $mailin_msg);
    $mailin_msg[] = $msg;
    return true;
}
function mailinFormSubmit()
{
    $action = '';
    if (isset($_POST['mailin_form_action']))
    {
        $action = trim($_POST['mailin_form_action']);
        if ($action == '')
            return;
        elseif ($action == 'sync_users')
        {
            $m_obj = new MailinApi();
            if ($m_obj->syncUsers())
                $message = '<div class="alert alert-success"" >'.__('Users synchronized successfully', 'mailin_i18n').'</div>';
            else
                $message = '<div class="alert alert-error" >'.__('Please choose atleast one list.', 'mailin_i18n').'</div>';
            mailinMessages($message);
        }elseif ($action == 'apikey_update')
        {
            //VALIDATE AND UPFATE API KEY
            $m_obj = new MailinApi();
            $m_obj->handleApikeyFormSubmit(strip_tags(stripslashes($_POST['mailin_apikey'])));
            if (empty($m_obj->mailin_error))
            {
                $message = implode('<br/>', $m_obj->mailin_success);
                $message = '<div class="alert alert-success"" >'.$message.'</div>';
                mailinMessages($message);
            } else
            {
                $message = implode('<br/>', $m_obj->mailin_error);
                $message = '<div class="alert alert-error" >'.$message.'</div>';
                mailinMessages($message);
            }
        } elseif ($action == 'update_list')
        {
            $mailin_list = isset($_POST['mailin_list']) ? $_POST['mailin_list'] : '';
            $m_obj = new MailinApi();
            $m_obj->handleUpdateListFormSubmit($mailin_list);
            if (!empty($m_obj->mailin_error))
            {
                $message = implode('<br/>', $m_obj->mailin_error);
                $message = '<div class="alert alert-error" >'.$message.'</div>';
                mailinMessages($message);
            }
            if (!empty($m_obj->mailin_success))
            {
                $message = implode('<br/>', $m_obj->mailin_success);
                $message = '<div class="alert alert-success"" >'.$message.'</div>';
                mailinMessages($message);
            }
        } elseif ($action == 'subscribe_form_submit')
        {
            $m_obj = new MailinApi();
            $m_obj->handleNewsletterSubscribeSubmit();
            if (!empty($m_obj->mailin_error))
            {
                $message = implode('<br/>', $m_obj->mailin_error);
                $message = '<div class="alert alert-error" >'.$message.'</div>';
                mailinMessages($message);
            }
            if (!empty($m_obj->mailin_success))
            {
                $message = implode('<br/>', $m_obj->mailin_success);
                $message = '<div class="alert alert-success"" >'.$message.'</div>';
                mailinMessages($message);
            }
        }
    }
}
/*
 * mailin admin setting page
 */
function mailinSettingsAdminPage()
{
    // CHECK IF API KEY EXISTS
    $api_key = get_option('mailin_apikey');
    // Send test email if requested
    if (isset($_POST['smtp_mailin']) && isset($_POST['to']))
    {
        $to = $_POST['to'];
        $lang = get_bloginfo('language');
        if ($lang == 'fr-FR')
        {
            $subject  = '[Mailin SMTP] e-mail de test';
            $from     = 'contact@mailin.fr';
            $fromname = 'Mailin';
        }
        else
        {
            $subject  = '[Mailinblue SMTP] test email';
            $fromname = 'Mailinblue';
            $from     = 'contact@mailinblue.com';
        }
        $failed = 0;
        $message = emailTemplate();
        define('WPMS_MAIL_FROM_NAME', $fromname);
        define('WPMS_SET_RETURN_PATH', $from); // Sets $phpmailer->Sender if true
        if (!empty($to) && !empty($subject) && !empty($message))
        { 
            try {
					$result = wp_mail($to, $subject, $message,  $headers = "Content-Type: text/html\r\n", '');
					if ($result == true)
					{
						$success_message = '<div id="message" class="alert alert-success"><p><strong>'.__('Mail sent', 'mailin_i18n').'</strong></p></div>';
						mailinMessages($success_message);
					} else{
						$failed = 1;
					}
				}
			catch (phpmailerException $e) {
                $failed = 1;
            }
        } else{
            $failed = 1;
		}
       
        if ($failed)
        {
            $error_message = '<div id="message" class="alert alert-error"><p><strong>'.__('Mail not sent', 'mailin_i18n').'</strong></p></div>';
            mailinMessages($error_message);
        }
    }
    
?>
<div class="wrap">
<?php
	if (mailinMessages() != '')
	{
		?>
		<div id="mc_message" >
		<?php
		echo mailinMessages();
		?>
		</div>
		<?php
	}
	if ($api_key)
		require 'listings.php';
	else
		require 'api_form.php';
?>
</div>
<?php
}
add_action('admin_menu', 'adminMenus');
add_action('init', 'mailinFormSubmit');
/*
 * show menu in admin
 */
function adminMenus()
{
    add_options_page('Mailin Setup', 'Mailin setup', 'administrator', 'mailin_options', 'mailinSettingsAdminPage');
}
/*
 * show link in admin
 */
function mailinActionLinks($links)
{
    $settings_page = add_query_arg(array(
        'page' => 'mailin_options'
    ), admin_url('options-general.php'));
    $settings_link = '<a href="'.esc_url($settings_page).'">'.__('Settings', 'mailin_i18n').'</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'mailinActionLinks', 10, 1);
/*
 * initilize constant
 */
	function initiallizeMailinConstants()
	{
		$locations = array('plugins' => array(
								'dir' => WP_PLUGIN_DIR,
								'url' => plugins_url()
								),
								'template' => array(
								'dir' => trailingslashit(get_template_directory()).'plugins/',
								'url' => trailingslashit(get_template_directory_uri()).'plugins/'
								));
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
		//filesystem path
		define('MAILIN_DIR', $mailin_dir);
		/* Lang location */
		define('MAILIN_LANG_DIR', trailingslashit(MAILIN_DIR).'lang/');
		// plugin folder url
		define('MAILIN_URL', $mailin_url);
		global $wpdb;
		// subscribers table name
		define('MAILIN_SUBSCRIBERS', $wpdb->prefix.'mailin_subscribers');
	}
	/*
	* Create subscribers table upon installation
	*/
	function mailinInstall()
	{
		global $wpdb;
		$sql = 'CREATE TABLE '.MAILIN_SUBSCRIBERS.' (
		id int(11) NOT NULL AUTO_INCREMENT,
		email VARCHAR(255) DEFAULT "" NOT NULL,
		fname VARCHAR(55) DEFAULT "" NOT NULL,
		lname VARCHAR(55) DEFAULT "" NOT NULL,
		list VARCHAR(255) DEFAULT "" NOT NULL,
		subscribed TINYINT(1) DEFAULT "1" NOT NULL,
		client TINYINT(1) DEFAULT "0" NOT NULL,
		create_date datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
		UNIQUE KEY id (id)
		);';
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/*
	*mail template
	*/
	function emailTemplate()
	{
		$lang = get_bloginfo('language');
		if ($lang == 'fr-FR')
			$file = 'mailinsmtp_conf.html';
		else
			$file = 'mailinsmtp_conf_en.html';
		if (file_exists(STYLESHEETPATH.'/'.$file))
		{
			$header_template_path = STYLESHEETPATH.'/emails/'.$file;
			$header_template_url  = get_stylesheet_directory_uri().'/emails/'.$file;
		} else
			$header_template_path = WP_EMAIL_TEMPLATE_DIR.'/emails/'.$file;
		$template_html = file_get_contents($header_template_path);
		if ($template_html == false)
		{
			$ch = curl_init($header_template_url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$template_html = curl_exec($ch);
			curl_close($ch);
		}
		return $template_html;
	}

	/*
	* when we deactive plugin call this function
	*/
	function mailinRemove()
	{
		update_option('mailin_list_selected', '');
		update_option('mailin_apikey', '');
		update_option('mailin_smtp', '');
		update_option('mailin_lists', '');
		update_option('mailin_manage_subscribe', '');
		update_option('mailin_apikey_status', '');
		update_option('mailin_unsubscribe', '');
		$mailinsmtp_options = array('mail_from' => '',
									'mail_from_name' => '',
									'mailer' => 'mail',
									'mail_set_return_path' => 'false',
									'mailin_smtp_host' => 'localhost',
									'mailin_smtp_port' => '25',
									'mailin_smtp_ssl' => 'none',
									'mailin_smtp_auth' => false,
									'mailin_smtp_user' => '',
									'mailin_smtp_pass' => '');
		// Create the required options...
		foreach ($mailinsmtp_options as $name => $val)
			update_option($name, $val);
	}
	/*
	* when we delete plugin call this function
	*/
	function mailinDelete()
	{
		delete_option('mailin_apikey');
		delete_option('mailin_list_selected');
		delete_option('widget_mailin_widget');
		delete_option('mailin_smtp');
		delete_option('mailin_lists');
		delete_option('mailin_unsubscribe');
		$mailinsmtp_options = array('mail_from' => '',
									'mail_from_name' => '',
									'mailer' => 'mail',
									'mail_set_return_path' => 'false',
									'mailin_smtp_host' => 'localhost',
									'mailin_smtp_port' => '25',
									'mailin_smtp_ssl' => 'none',
									'mailin_smtp_auth' => false,
									'mailin_smtp_user' => '',
									'mailin_smtp_pass' => '');
		// Create the required options...
		foreach ($mailinsmtp_options as $name => $val)
			delete_option($name);
		global $wpdb;
		$table = $wpdb->prefix.'mailin_subscribers';
		$wpdb->query('DROP TABLE IF EXISTS '.$table);
	}
/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'mailinInstall');
/* Runs on plugin deactivation*/
register_deactivation_hook(__FILE__, 'mailinRemove');
/* Runs on plugin unistalling*/
register_uninstall_hook(__FILE__, 'mailinDelete');
class SiteUsers
{
	public static function init()
	{
		// Change the user's display name after insertion
		add_action('user_register', array(
			__CLASS__,
			'registerNewlyAddedUser'
		));
	}
    /*
     * This function is called when a new user is created
     * User is added in mailinlist
     */
    public static function registerNewlyAddedUser($user_id = null)
    {
        if ($user_id != null)
        {
            $info = get_userdata($user_id);
           
			
            if (is_object($info))
            {
				$user = get_user_by( 'email', $info->data->user_email );
				
                if (isset($info->data->user_email) && $info->data->user_email != '')
                {
					
                    $user_nicename = isset($info->data->user_nicename) ? $info->data->user_nicename : '';
                    $fname         = isset($user->first_name) ? $user->first_name : '';
                    $lname         = isset($user->last_name) ? $user->last_name : '';
                    $selected_list = get_option('mailin_list_selected');
                    $api_key       = get_option('mailin_apikey');
                    $m_obj          = new MailinApi();
                    if (!$m_obj->syncronizeSetting())
							return false;
                    $m_obj->createRegistrationUser($api_key, $info->data->user_email, $selected_list, $fname, $lname);
                    $m_obj->updateSubscribers($info->data->user_email, $selected_list, $fname, $lname, 1, 1);
                }
            }
        }
        wp_update_user($args);
    }
}
SiteUsers::init();
?>
