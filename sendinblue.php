<?php
/**
Plugin Name: SendinBlue Subscribe Form And WP SMTP
Plugin URI: https://www.sendinblue.com/?r=wporg
Description: Easily send emails from your WordPress blog using SendinBlue SMTP and easily add a subscribe form to your site
Version: 2.2.3
Author: SendinBlue
Author URI: https://www.sendinblue.com/?r=wporg
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

if(!class_exists('Mailin')) {
    require_once('inc/mailin.php');
}

/**
 * Application entry point. Contains plugin startup class that loads on <i> sendinblue_init </i> action.
 * @package SIB
 */

if(!class_exists('SIB_Manager'))
{
    register_deactivation_hook( __FILE__, array('SIB_Manager', 'deactivate'));
    register_activation_hook( __FILE__, array('SIB_Manager', 'install'));
    register_uninstall_hook(__FILE__, array('SIB_Manager', 'uninstall'));

    require_once('page/page-home.php');
    require_once('page/page-form.php');
    require_once('page/page-lists.php');
    require_once('page/page-campaigns.php');
    require_once('page/page-statistics.php');
    require_once('widget/widget_form.php');
    require_once('model/model-contacts.php');

    class SIB_Manager
    {
        /**
         * plugin main setting option name
         */
        const main_option_name = 'sib_main_option';

        /**
         * plugin main setting option name
         */
        const account_option_name = 'sib_account_option';

        /**
         * plugin main setting option name
         */
        const home_option_name = 'sib_home_option';

        /**
         * plugin main setting option name
         */
        const access_token_option_name = 'sib_token_store';

        /**
         * plugin signup setting option name
         */
        const form_signup_option_name = 'sib_signup_option';

        /**
         * plugin confirmation message option name
         */
        const form_confirmation_option_name = 'sib_confirm_option';

        /**
         * plugin subscription setting option name
         */
        const form_subscription_option_name = 'sib_subscription_option';

        /**
         * plugin attribute list option name
         */
        const attribute_list_option_name = 'sib_attribute_option';

        /**
         * plugin smtp details option name
         */
        const attribute_smtp_name = 'sib_smtp_details';

        /**
         * Plugin directory path value. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_dir;

        /**
         * Plugin url. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_url;

        /**
         * Plugin name. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_name;

        /**
         * Access key
         */
        public static $access_key;

        /**
         * secret key
         */
        public static $secret_key;

        /**
         * access_token
         */
        public static $access_token;

        /**
         * start time that got access token
         */
        public static $start_time_access_token;

        /**
         * send confirmation email "yes" or "no"
         */
        public static $is_confirm_email;

        /**
         * use double optin ? "yes" or "no"
         */
        public static $is_double_optin;

        /**
         * use redirect url ? "yes" or "no"
         */
        public static $is_redirect_url_click;

        /**
         * template id for email
         */
        public static $template_id;

        /**
         * sender id
         */
        public static $sender_id;

        /**
         * redirect url after subscription in email
         */
        public static $redirect_url;

        /**
         * redirect url after subscription
         */
        public static $redirect_url_click;

        /**
         * alert message when success to subscribe
         */
        public static $alert_success_message;

        /**
         * alert message when generate the general error
         */
        public static $alert_error_message;

        /**
         * alert message when already exist subscriber in contact list
         */
        public static $alert_exist_subscriber;

        /**
         * alert message when input email is invalid
         */
        public static $alert_invalid_email;

        /**
         * subscription form html
         */
        public static $sib_form_html;

        /**
         * store instance
         */
        public static $instance;

        /**
         * store list id
         */
        public static $list_id;

        /**
         * store activate email flag for smtp
         */
        public static $activate_email;

        /**
         * account email
         */
        public static $account_email;

        /**
         * account data
         */
        public static $account_data;

        /**
         * account user name
         */
        public static $account_user_name;

        /**
         * smtp details
         */
        public static $smtp_details;

        /**
         * Private member to store reference count of form
         */
        private $reference_form_count = 0;

        /**
         * Private member to store state of form
         */
        private $state_of_form = '';

        /**
         * Private member to store reference id of form
         */
        private $reference_id = -1;

        /**
         * Class constructor
         * Sets plugin url and directory and adds hooks to <i>init</i>. <i>admin_menu</i>
         */
        function __construct()
        {
            // get basic info
            self::$plugin_dir = plugin_dir_path(__FILE__);
            self::$plugin_url = plugins_url('', __FILE__);
            self::$plugin_name = plugin_basename(__FILE__);

            // api details for sendinblue
            $general_settings = get_option(self::main_option_name, array());
            self::$access_key = $general_settings['access_key'];
            self::$secret_key = $general_settings['secret_key'];

            $access_token_settings = get_option(self::access_token_option_name, array());
            self::$access_token = $access_token_settings['access_token'];

            // list id
            $home_options = get_option(self::home_option_name, array());
            self::$list_id = $home_options['list_id'];
            self::$activate_email = $home_options['activate_email'];

            // get sign up parameters
            $signup_settings = get_option(self::form_signup_option_name, array());
            self::$is_confirm_email = $signup_settings['is_confirm_email'];
            self::$is_double_optin = $signup_settings['is_double_optin'];
            self::$redirect_url = $signup_settings['redirect_url'];
            self::$redirect_url_click = $signup_settings['redirect_url_click'];
            self::$is_redirect_url_click = $signup_settings['is_redirect_url_click'];
            self::$template_id = $signup_settings['template_id'];
            self::$sender_id = $signup_settings['sender_id'];

            // get alert message parameters
            $alert_settings = get_option(self::form_confirmation_option_name, array());
            self::$alert_success_message = $alert_settings['alert_success_message'];
            self::$alert_error_message = $alert_settings['alert_error_message'];
            self::$alert_exist_subscriber = $alert_settings['alert_exist_subscriber'];
            self::$alert_invalid_email = $alert_settings['alert_invalid_email'];

            // get sign up form html
            $form_settings = get_option(self::form_subscription_option_name, array());
            self::$sib_form_html = $form_settings['sib_form_html'];

            // get account info
            $account_settings = get_option(self::account_option_name, array());
            self::$account_email = $account_settings['account_email'];
            self::$account_user_name = $account_settings['account_user_name'];
            self::$account_data = $account_settings['account_data'];

            self::$instance = $this;

            add_action('admin_init', array(&$this, 'admin_init'), 9999);
            add_action('admin_menu', array(&$this, 'admin_menu'), 9999);

            add_action('wp_print_scripts', array(&$this,'frontend_register_scripts'), 9999);
            add_action('wp_head', array(&$this, 'wp_head_ac'), 999);

            add_action('wp_ajax_sib_validate_process', array('SIB_Page_Home', 'ajax_validation_process'));
            add_action('wp_ajax_sib_change_list', array('SIB_Page_Home', 'ajax_change_list'));
            add_action('wp_ajax_sib_activate_email_change', array('SIB_Page_Home', 'ajax_activate_email_change'));
            add_action('wp_ajax_sib_send_email', array('SIB_Page_Home', 'ajax_send_email'));
            add_action('wp_ajax_sib_change_template', array('SIB_Page_Form', 'ajax_change_template'));

            if(self::is_done_validation() == true) {
                add_shortcode('sibwp_form', array(&$this, 'sibwp_form_shortcode'));
                // register widget
                add_action( 'widgets_init', array(&$this,'sib_create_widget') );
            }

            add_action('init', array(&$this, 'init'));

            // check if updated into new configuration.
            $use_new_version = get_option('sib_use_new_version', '0');
            if($use_new_version == '0') {
                $sib_form_html = <<<EOD
<p class="sib-email-area">
    <label class="sib-email-area">Email Address</label>
    <input type="email" class="sib-email-area" name="email" required="required">
</p>
<p class="sib-NAME-area">
    <label class="sib-NAME-area">Name</label>
    <input type="text" class="sib-NAME-area" name="NAME" >
</p>
<p>
    <input type="submit" class="sib-default-btn" value="Subscribe">
</p>
EOD;

                update_option('sib_use_new_version', '1');

                $form_settings = array(
                    'sib_form_html' => stripcslashes($sib_form_html),
                    'available_attributes' => array('NAME')
                );
                update_option(self::form_subscription_option_name, $form_settings);
            }

            // smtp details
            self::$smtp_details = get_option(SIB_Manager::attribute_smtp_name, null);
            if((self::is_done_validation()) && (self::$smtp_details == null)) {
                self::update_smtp_details();
            }
            $home_settings = get_option(SIB_Manager::home_option_name, array());
            if((self::is_done_validation()) && (self::$smtp_details['relay'] == false) ) {

                $home_settings['activate_email'] = 'no';
                update_option(SIB_Manager::home_option_name, $home_settings);
            }

            add_action('phpmailer_init', array(&$this, 'smtp_hook'));

        }

        /**
         * Initialize method. called on <i>init</i> action
         */
        function init()
        {
            // sign up process
            if(isset($_POST['sib_form_action']) && ($_POST['sib_form_action'] == 'subscribe_form_submit')) {
                $this->signup_process();
            }

            // unsubscribe
            if(isset($_GET['sib_action']) && ($_GET['sib_action'] == 'unsubscribe')) {
                $this->unsubscribe();
                exit;
            }

            // subscribe
            if(isset($_GET['sib_action']) && ($_GET['sib_action'] == 'subscribe')) {
                $this->subscribe();
                exit;
            }
        }

        /** hook admin_init */
        function admin_init()
        {
            add_action('admin_post_sib_setting_signup', array('SIB_Page_Form', 'save_setting_signup'));
            add_action('admin_post_sib_setting_confirmation', array('SIB_Page_Form', 'save_setting_confirm'));
            add_action('admin_post_sib_setting_subscription', array('SIB_Page_Form', 'save_setting_subscription'));
            SIB_Manager::LoadTextDomain();
            $this->register_scripts();
            $this->register_styles();
        }

        /** hook admin_menu */
        function admin_menu()
        {
            SIB_Manager::LoadTextDomain();
            new SIB_Page_Home();
            new SIB_Page_Lists();
            new SIB_Page_Campaigns();
            new SIB_Page_Statistics();
            new SIB_Page_Form();
        }

        /** register script for admin page */
        function register_scripts()
        {
            wp_register_script('sib-bootstrap-js', self::$plugin_url . '/js/bootstrap/js/bootstrap.min.js', array('jquery'), null);
            wp_register_script('sib-admin-js', self::$plugin_url . '/js/admin.js', array('jquery'), null);
        }

        /** register stylesheet for admin page */
        function register_styles()
        {
            wp_register_style('sib-bootstrap-css', self::$plugin_url . '/js/bootstrap/css/bootstrap.css', array(), null, 'all');
            wp_register_style('sib-fontawesome-css', self::$plugin_url . '/css/fontawesome/css/font-awesome.css', array(), null, 'all');

            wp_register_style('sib-admin-css', self::$plugin_url . '/css/admin.css', array(), null, 'all');
        }

        /**
         * Registers scripts for frontend
         */
        function frontend_register_scripts()
        {

        }

        function wp_head_ac()
        {

        }

        /**
         * Install method is called once install this plugin.
         * create tables, default option ...
         */
        static function install()
        {
            // default option when activate
            $home_settings = array(
                'activate_email' => 'no'
            );
            update_option(self::home_option_name, $home_settings);

            // set sign up parameters
            $signup_settings = array(
                'is_confirm_email' => 'yes',
                'is_double_optin' => 'no',
                'template_id' => '-1',
                'redirect_url' => '',
                'redirect_url_click' => '',
                'is_redirect_url_click' => 'no',
                'sender_id' => '-1'
            );
            update_option(self::form_signup_option_name, $signup_settings);

            // set alert message parameters
            $signup_settings = array(
                'alert_success_message' => __('Thank you, you have successfully registered !', 'sib_lang'),
                'alert_error_message' =>  __('Something wrong occured', 'sib_lang'),
                'alert_exist_subscriber' =>  __('You have already registered', 'sib_lang'),
                'alert_invalid_email' =>  __('Your email address is invalid', 'sib_lang')
            );
            update_option(self::form_confirmation_option_name, $signup_settings);

            // set sign up form html
            $sib_form_html = <<<EOD
<p class="sib-email-area">
    <label class="sib-email-area">Email Address</label>
    <input type="email" class="sib-email-area" name="email" required="required">
</p>
<p class="sib-NAME-area">
    <label class="sib-NAME-area">Name</label>
    <input type="text" class="sib-NAME-area" name="NAME" >
</p>
<p>
    <input type="submit" class="sib-default-btn" value="Subscribe">
</p>
EOD;

            update_option('sib_use_new_version', '1');

            $form_settings = array(
                'sib_form_html' => stripcslashes($sib_form_html),
                'available_attributes' => array('NAME')
            );
            update_option(self::form_subscription_option_name, $form_settings);

            $account_settings = array(
                'account_email' => '',
                'account_user_name' => '',
                'account_data' => array()
            );
            update_option(self::account_option_name, $account_settings);

            SIB_Model_Contact::create_table();
        }

        /**
         * get email template by type (test, confirmation, double-optin)
         * return @values : array ( 'html_content' => '...', 'text_content' => '...' );
         */
        static function get_email_template($type='test')
        {
            $lang = get_bloginfo('language');
            if ($lang == 'fr-FR')
                $file = 'temp_fr-FR';
            else
                $file = 'temp';


            $file_path = self::$plugin_dir . '/inc/templates/' . $type .'/';

            // get html content
            $html_content = file_get_contents($file_path . $file . '.html');

            // get text content
            $text_content = file_get_contents($file_path . $file . '.txt');

            $templates = array('html_content' => $html_content, 'text_content' => $text_content);

            return $templates;
        }

        /**
         * Uninstall method is called once uninstall this plugin
         * delete tables, options that used in plugin
         */
        static function uninstall()
        {
            $setting = array();
            update_option(SIB_Manager::main_option_name, $setting);

            $home_settings = array(
                'activate_email' => 'no'
            );
            update_option(SIB_Manager::home_option_name, $home_settings);

            // delete access_token
            $token_settings = array();
            update_option(SIB_Manager::access_token_option_name, $token_settings);

            // remove account info
            SIB_Page_Home::remove_account_info();
        }

        /**
         * Deactivate method is called once deactivate this plugin
         */
        static function deactivate()
        {

        }

        /**
         * Check that have done validation process already.
         */
        static function is_done_validation()
        {
            if((self::$access_key != '') && (self::$secret_key != ''))
                return true;
            else
                return false;
        }

        /**
         * Register widget
         */
        function sib_create_widget()
        {
            register_widget( 'SIB_Widget_Subscribe' );
        }

        /**
         * Generate subscription form html
         */
        function generate_form_box($widget_attribute=null)
        {
            $sign_settings = get_option(SIB_Manager::form_subscription_option_name);
            $html = $sign_settings['sib_form_html'];
            $avail_atts = $sign_settings['available_attributes'];

            if($widget_attribute != null) {
                // or set default values if not present
                $widget_title = stripslashes( $widget_attribute['widget_title'] );
                $button_text = stripslashes( $widget_attribute['button_text'] );
                $sib_list = esc_attr( $widget_attribute['sib_list'] );
                $displays = array();
                foreach($avail_atts as $att)
                {
                    if(isset($widget_attribute['disp_att_' . $att])) {
                        $displays['disp_att_' . $att] = esc_attr( $widget_attribute['disp_att_' . $att] );
                    } else {
                        $displays['disp_att_' . $att] = 'yes';
                    }
                }
            } else {
                $home_settings = get_option(self::home_option_name);
                $sib_list = $home_settings['list_id'];
            }

            $this->reference_form_count ++;

            ?>
            <form id="sib_form_<?php echo $this->reference_form_count; ?>-form" method="post" class="sib_signup_form">
                <input type="hidden" name="sib_form_action" value="subscribe_form_submit">
                <input type="hidden" name="sib_form_list_id" value="<?php echo $sib_list; ?>">
                <input type="hidden" name="reference_id" value="<?php echo $this->reference_form_count; ?>">
                <div class="sib_signup_box_inside">
                    <?php
                    if($widget_attribute != null) {
                    ?>
                        <p style="margin-bottom: 20px;">
                            <span class="sib_widget_title"><?php echo $widget_title; ?></span>
                        </p>
                    <?php
                    }
                    if($this->reference_id == $this->reference_form_count) {
                        if($this->state_of_form == 'success') {
                        ?>
                            <p class="sib-alert-message sib-alert-message-success">
                                <?php echo SIB_Manager::$alert_success_message; ?>
                            </p>
                        <?php
                        } elseif ($this->state_of_form == 'failure') {
                        ?>
                            <p class="sib-alert-message sib-alert-message-error sib-alert-error-general">
                                <?php echo SIB_Manager::$alert_error_message; ?>
                            </p>
                        <?php
                        } elseif ($this->state_of_form == 'already_exist') {
                        ?>
                            <p class="sib-alert-message sib-alert-message-warning sib-alert-error-subscriber">
                                <?php echo SIB_Manager::$alert_exist_subscriber; ?>
                            </p>
                        <?php
                        } elseif ($this->state_of_form == 'invalid') {
                        ?>
                            <p class="sib-alert-message sib-alert-message-error sib-alert-error-general">
                                <?php echo SIB_Manager::$alert_invalid_email; ?>
                            </p>
                        <?php
                        }
                    }
                    echo $html;
                    ?>
                </div>
            </form>
            <style>
                span.sib_widget_title{
                    font-weight: bold;
                }
                form#sib_form_<?php echo $this->reference_form_count; ?>-form {
                    padding: 5px;
                    -moz-box-sizing:border-box;
                    -webkit-box-sizing: border-box;
                    box-sizing: border-box;
                }
                form#sib_form_<?php echo $this->reference_form_count; ?>-form p{
                    line-height: 100%;
                    margin: 10px 0px 0px 0px;
                    padding: 0px;
                }
                form#sib_form_<?php echo $this->reference_form_count; ?>-form input[type=text],form#sib_form_<?php echo $this->reference_form_count; ?>-form input[type=email] {
                    width: 100%;
                    max-width: 300px;
                    box-shadow: none;
                    border: 1px solid #bbbbbb;
                    height: 30px;
                    margin: 0px;
                    margin-top: 5px;

                }
                form#sib_form_<?php echo $this->reference_form_count; ?>-form button.sib-default-btn, form#sib_form_<?php echo $this->reference_form_count; ?>-form input[type=submit].sib-default-btn {
                    margin: 0px;
                    margin-top:10px;
                    margin-bottom: 5px;
                    color:#fff;
                    background-color: #444444;
                    border-color: #2E2E2E;
                    padding: 6px 12px;
                    font-size: 14px;
                    font-weight:400;
                    line-height: 1.4285;
                    text-align: center;
                    cursor: pointer;
                    vertical-align: middle;
                    -webkit-user-select:none;
                    -moz-user-select:none;
                    -ms-user-select:none;
                    user-select:none;
                    white-space: normal;
                    background-image:none;
                    border:1px solid transparent;
                    border-radius: 4px;
                }
                form#sib_form_<?php echo $this->reference_form_count; ?>-form button.sib-default-btn:hover, form#sib_form_<?php echo $this->reference_form_count; ?>-form input[type=submit].sib-default-btn:hover {
                    background-color: #333333;
                }
                p.sib-alert-message {
                    padding: 15px;
                    margin-bottom: 20px;
                    border: 1px solid transparent;
                    border-radius: 4px;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                }
                p.sib-alert-message-error {
                    background-color: #f2dede;
                    border-color: #ebccd1;
                    color: #a94442;
                }
                p.sib-alert-message-success {
                    background-color: #dff0d8;
                    border-color: #d6e9c6;
                    color: #3c763d;
                }
                p.sib-alert-message-warning {
                    background-color: #fcf8e3;
                    border-color: #faebcc;
                    color: #8a6d3b;
                }
            </style>
            <script>
                 <?php
                 if($widget_attribute != null) {
                 ?>
                     jQuery('form#sib_form_<?php echo $this->reference_form_count; ?>-form input[type="submit"]').attr('value', "<?php echo stripslashes($button_text); ?>");
                     <?php
                      foreach($avail_atts as $att)
                      {
                          if($displays['disp_att_' . $att] != 'yes') {
                          ?>
                             jQuery('form#sib_form_<?php echo $this->reference_form_count; ?>-form .sib-<?php echo $att; ?>-area').hide();
                             jQuery('form#sib_form_<?php echo $this->reference_form_count; ?>-form input.sib-<?php echo $att; ?>-area').attr('disabled', 'true');
                         <?php
                         }
                      }
                 }

                 if($this->reference_id == $this->reference_form_count) {
                     if($this->state_of_form == 'success' && SIB_Manager::$is_redirect_url_click == 'yes' && SIB_Manager::$redirect_url_click != '') {
                     // process after click subscribe
                     ?>
                        window.location.href = '<?php echo SIB_Manager::$redirect_url_click; ?>';
                     <?php
                     }
                 }
                ?>
            </script>
        <?php
        }

        /**
         * shortcode for sign up form
         */
        function sibwp_form_shortcode($atts)
        {
            ob_start();
            $this->generate_form_box();

            $output_string = ob_get_contents();;
            ob_end_clean();
            return $output_string;
        }

        /**
         * Sign up process
         */
        function signup_process()
        {
            $email = $_POST['email'];
            if(!is_email($email))
                return;

            $attributes = get_option(SIB_Manager::attribute_list_option_name);
            $info = array();
            foreach($attributes as $attribute)
            {
                if(isset($_POST[$attribute['name']])) {
                    if($attribute['type'] == 'float') {
                        $info[$attribute['name']] = floatval($_POST[$attribute['name']]);
                    } else {
                        $info[$attribute['name']] = esc_attr($_POST[$attribute['name']]);
                    }
                }
            }
            $list_id = $_POST['sib_form_list_id'];
            $ref_id = $_POST['reference_id'];

            $error = '';
            if(SIB_Manager::$is_double_optin == 'yes') {
                // double optin process
                $error = $this->double_optin_signup($email, $info, $list_id);
            } else {
                if(SIB_Manager::$is_confirm_email == 'yes') {
                    $error = $this->confirm_signup($email, $info, $list_id);
                } else {
                    $error = $this->simple_signup($email, $info, $list_id);
                }
            }

            $this->reference_id = $ref_id;
            $this->state_of_form = $error;
        }

        /**
         * Simple signup
         */
        function simple_signup($email, $info, $list_id)
        {

            $response = $this->validation_email($email, $list_id);
            if($response['code'] != 'success')
                return $response['code'];

            $listid = $response['listid'];
            array_push($listid, $list_id);
            $listid_unlink = null;

            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);

            $response = $mailin->create_update_user($email, $info, 0, $listid,null);
            if($response['code'] == 'success')
                return 'success';

            return 'failure';
        }

        /**
         * confirm signup
         */
        function confirm_signup($email, $info, $list_id)
        {
            $response = $this->validation_email($email, $list_id);
            if($response['code'] != 'success')
                return $response['code'];

            $template_id = SIB_Manager::$template_id;
            $listid = $response['listid'];
            array_push($listid, $list_id);
            $listid_unlink = null;

            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);

            $response = $mailin->create_update_user($email, $info, 0, $listid, null);

            // db store

            $data = SIB_Model_Contact::get_data_by_email($email);
            if($data == false) {
                $uniqid = uniqid();
                $data = array(
                    'email' => $email,
                    'info' => maybe_serialize($info),
                    'code' => $uniqid,
                    'is_activate' => 1,
                    'extra' => 0
                );
                SIB_Model_Contact::add_record($data);
            } else {
                $uniqid = $data['code'];
            }


            // send confirmation email
            $this->send_email('confirm', $email, $uniqid, $list_id, $template_id);

            if($response['code'] == 'success')
                return 'success';

            return 'failure';

        }

        /**
         * Double optin
         */
        function double_optin_signup($email, $info, $list_id)
        {
            $response = $this->validation_email($email, $list_id);
            if($response['code'] != 'success')
                return $response['code'];

            // db store

            $data = SIB_Model_Contact::get_data_by_email($email);
            if($data == false) {
                $uniqid = uniqid();
                $data = array(
                    'email' => $email,
                    'info' => maybe_serialize($info),
                    'code' => $uniqid,
                    'is_activate' => 0,
                    'extra' => 0
                );
                SIB_Model_Contact::add_record($data);
            } else {
                $uniqid = $data['code'];
            }

            // send double optin email
            $this->send_email('double-optin', $email, $uniqid, $list_id);

            return 'success';
        }

        /**
         * Validation email
         */
        function validation_email($email, $list_id)
        {
            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            $response = $mailin->get_user($email);
            if($response['code'] == 'failure') {
                $ret = array(
                    'code' => 'success',
                    'listid' => array()
                );
                return $ret;
            }

            if($response['data']['blacklisted'] == 1) {
                $ret = array(
                    'code' => 'invalid',
                    'listid' => array()
                );
                return $ret;
            }

            $listid = $response['data']['listid'];
            if(!in_array($list_id, $listid)) {
                $ret = array(
                    'code' => 'success',
                    'listid' => $listid
                );
                return $ret;
            }

            $ret = array(
                'code' => 'already_exist',
                'listid' => $listid
            );
            return $ret;
        }

        /**
         * Send mail
         * @params (type, to_email, to_info, list_id)
         */
        function send_email($type, $to_email, $code, $list_id, $template_id='-1')
        {
            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            // set subject info
            if($type == 'confirm') {
                $subject = __('Subscription confirmed', 'sib_lang');
            } elseif($type == "double-optin") {
                $subject = __('Please confirm subscription', 'sib_lang');
            }

            // get sender info
            if(SIB_Manager::$sender_id == '-1') {
                $sender_email = __('no-reply@sendinblue.com', 'sib_lang');
                $sender_name = __('SendinBlue', 'sib_lang');
            } else {
                $senders = SIB_Page_Form::get_sender_lists();
                $sender_email = SIB_Manager::$sender_id;
                foreach($senders as $sender) {
                    if($sender_email == $sender['from_email']) {
                        $sender_name = $sender['from_name'];
                        break;
                    }
                }
            }

            if($sender_email == '') {
                $sender_email = __('no-reply@sendinblue.com', 'sib_lang');
                $sender_name = __('SendinBlue', 'sib_lang');
            }

            // get template html and text
            $template_contents = self::get_email_template($type);
            $html_content = $template_contents['html_content'];
            $text_content = $template_contents['text_content'];

            if($type=="confirm" && $template_id != '-1') {
                $response = $mailin->get_campaign($template_id);
                if($response['code'] == 'success') {
                    $html_content = $response['data'][$template_id]['html_content'];
                }
            }

            // send mail
            $to = array($to_email => '');
            $from = array($sender_email, $sender_name);
            $null_array = array();
            $site_domain = str_replace('https://', '', home_url());
            $site_domain = str_replace('http://', '', $site_domain);

            $html_content = str_replace('{title}', $subject, $html_content);
            $html_content = str_replace('{site_domain}', $site_domain, $html_content);
            $html_content = str_replace('{unsubscribe_url}', add_query_arg(array('sib_action' => 'unsubscribe', 'code' => $code, 'li'=>$list_id), home_url()), $html_content);
            $html_content = str_replace('{subscribe_url}', add_query_arg(array('sib_action' => 'subscribe', 'code' => $code, 'li'=>$list_id), home_url()), $html_content);

            $text_content = str_replace('{site_domain}', home_url(), $text_content);

            if(SIB_Manager::$activate_email == 'yes') {
                $headers = array();
                $mailin->send_email($to,$subject,$from,$html_content,$text_content,$null_array,$null_array,$from,$null_array,$headers);
            } else {
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: ' . $sender_name . ' <' . $sender_email . '>' . "\r\n";
                mail($to_email, $subject, $html_content, $headers);
            }
        }

        /**
         * Unsubscribe process
         */
        function unsubscribe()
        {
            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            $code = esc_attr($_GET['code']);
            $list_id = intval($_GET['li']);

            $contact_info = SIB_Model_Contact::get_data_by_code($code);

            if($contact_info != false) {

                $email = $contact_info['email'];
                $response = $mailin->get_user($email);

                if($response['code'] == 'success') {
                    $attributes = $response['data']['attributes'];

                    $listid = $response['data']['listid'];

                    $blacklisted = $response['data']['blacklisted'];
                    $listid = array_diff($listid, array($list_id));

                    if(count($listid) == 0) {

                        $mailin->delete_user($email);
                        SIB_Model_Contact::remove_record($contact_info['id']);
                    } else {
                        $mailin->create_update_user($email, $attributes, $blacklisted, $listid, null);
                    }
                }
            }
            ?>
            <body style="margin:0; padding:0;">
            <table style="background-color:#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tbody>
                <tr style="border-collapse:collapse;">
                    <td style="border-collapse:collapse;" align="center">
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20"></td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20">
                                    <div style="font-family:arial,sans-serif; color:#61a6f3; font-size:20px; font-weight:bold; line-height:28px;">
                                        <?php _e('Unsubscribe', 'sib_lang'); ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20"></td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td align="left">

                                    <div style="font-family:arial,sans-serif; font-size:14px; margin:0; line-height:24px; color:#555555;">
                                        <br>
                                        <?php _e('Your request has been taken into account.', 'sib_lang'); ?><br>
                                        <br>
                                        <?php _e('The user has been unsubscribed', 'sib_lang'); ?><br>
                                        <br>
                                        -SendinBlue</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            </body>
            <?php
            exit;
        }

        /**
         * Subscribe process
         */
        function subscribe()
        {
            $site_domain = str_replace('https://', '', home_url());
            $site_domain = str_replace('http://', '', $site_domain);

            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            $code = esc_attr($_GET['code']);
            $list_id = intval($_GET['li']);

            $contact_info = SIB_Model_Contact::get_data_by_code($code);

            if($contact_info != false) {
                $email = $contact_info['email'];
                $response = $mailin->get_user($email);

                if($response['code'] == 'success') {
                    $listid = $response['data']['listid'];
                } else {
                    $listid = array();
                }

                if((SIB_Manager::$is_confirm_email == 'yes') && (in_array($list_id, $listid) == false)) {
                    $this->send_email('confirm', $email, $code, $list_id);
                }

                array_push($listid, $list_id);
                $attribues = maybe_unserialize($contact_info['info']);
                $mailin->create_update_user($email, $attribues, 0, $listid, null);
            }

            if(SIB_Manager::$redirect_url != '') {
                wp_redirect(SIB_Manager::$redirect_url);
                exit;
            }
            ?>
            <body style="margin:0; padding:0;">
            <table style="background-color:#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tbody>
                <tr style="border-collapse:collapse;">
                    <td style="border-collapse:collapse;" align="center">
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20"></td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20">
                                    <div style="font-family:arial,sans-serif; color:#61a6f3; font-size:20px; font-weight:bold; line-height:28px;">
                                        <?php _e('Thank you for subscribing', 'sib_lang'); ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20"></td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td align="left">

                                    <div style="font-family:arial,sans-serif; font-size:14px; margin:0; line-height:24px; color:#555555;">
                                        <br>
                                        <?php echo __('You have just subscribed to the newsletter of ', 'sib_lang') . $site_domain . ' .'; ?><br><br>
                                        <?php _e('-SendinBlue', 'sib_lang'); ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" border="0" width="540">
                            <tbody>
                            <tr>
                                <td style="line-height:0; font-size:0;" height="20">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            </body>
            <?php
            exit;
        }

        /** update access token */
        public static function update_access_token()
        {
            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            $mailin->delete_token(self::$access_token);

            $access_response = $mailin->get_access_tokens();
            if($access_response['code'] != 'success') {
                $access_response = $mailin->get_access_tokens();
            }
            $access_token = $access_response['data']['access_token'];
            $token_settings = array(
                'access_token' => $access_token
            );

            update_option(SIB_Manager::access_token_option_name, $token_settings);
            return $access_token;
        }

        /** update smtp details */
        public static function update_smtp_details()
        {
            $mailin = new Mailin('https://api.sendinblue.com/v1.0', SIB_Manager::$access_key, SIB_Manager::$secret_key);
            $response = $mailin->get_smtp_details();
            if($response['code'] == 'success') {
                if($response['data']['relay_data']['status'] == 'enabled') {
                    self::$smtp_details = $response['data']['relay_data']['data'];
                    update_option(self::attribute_smtp_name, self::$smtp_details);
                    return true;
                } else {
                    self::$smtp_details = array(
                        'relay' => false
                    );
                    update_option(self::attribute_smtp_name, self::$smtp_details);
                    $home_settings = get_option(self::home_option_name, array());
                    $home_settings['activate_email'] = 'no';
                    update_option(SIB_Manager::home_option_name, $home_settings);
                    return false;
                }
            }

            return false;
        }

        /**
         * Hook phpmailer_init
         */
        function smtp_hook($phpmailer)
        {
            $admin_info = get_userdata(1);
            $home_settings = get_option(self::home_option_name, array());
            if($home_settings['activate_email'] != 'yes')
                return;
            if(self::$smtp_details['relay'] == false)
                return;
            $phpmailer->Mailer = 'smtp';
            //$phpmailer->From = $admin_info->user_email;
            //$phpmailer->FromName = $admin_info->display_name;
            $phpmailer->Sender = $phpmailer->From; //Return-Path
            $phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To
            $phpmailer->Host       = self::$smtp_details['relay'];
            $phpmailer->SMTPSecure = 'true';
            $phpmailer->Port       = self::$smtp_details['port'];
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Username = self::$smtp_details['username'];
            $phpmailer->Password = self::$smtp_details['password'];
        }

      /**
       * Load Text domain.
       */
        static function LoadTextDomain() {
          // load lang file
          $i18n_file_name = 'sib_lang';
          $locale         = apply_filters('plugin_locale', get_locale(), $i18n_file_name);
          $filename       = plugin_dir_path(__FILE__). '/lang/' .$i18n_file_name.'-'.$locale.'.mo';
          load_textdomain('sib_lang', $filename);
        }
    }

    /**
     * Plugin entry point Process.
     * */

    add_action( 'sendinblue_init', 'sendinblue_init' );

    function sendinblue_init() {
        new SIB_Manager();
    }

    do_action( 'sendinblue_init' );
}
