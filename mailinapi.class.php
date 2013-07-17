<?php
class MailinApi
{
    public $mailin_error = array();
    public $mailin_success = array();
    /**
     * update user list
     */
    public function updateUserLists($api_key = null)
    {
        if ($api_key == '')
            return;
        elseif ($api_key != null)
            $api_key = get_option('mailin_apikey');
        $return_data = $this->getUserLists($api_key);
        if (isset($return_data->result) && !empty($return_data->result))
        {
            $list_data = $return_data->result;
            $lists    = serialize($list_data);
            update_option('mailin_lists', $lists);
        }
    }
    /**
     * check api key is validate or not
     */
    public function validateAPIkey($api_key = null)
    {
        $is_valid  = false;
        $error    = '';
        $list_data = array();
        if ($api_key == '')
            $error = 'Please enter API key';
        else
        {
            $return_data = $this->getUserLists($api_key);
            if (!is_object($return_data))
                $error = 'Oops!, unable to connect!';
            else if (isset($return_data->error_msg))
                $error = $return_data->error_msg;
            else if (isset($return_data->result))
            {
                $is_valid = true;
                if (!empty($return_data->result))
                    $list_data = $return_data->result;
            }
        }
        return $return_data = array(
            'isValid' => $is_valid,
            'data' => $list_data,
            'error' => $error
        );
    }
    /**
     * handle api key form
     */
    public function handleApikeyFormSubmit($api_new_key = null)
    {
        $api_new_key = strip_tags(trim($api_new_key));
        $return_data  = $this->validateAPIkey($api_new_key);
        if ($return_data['error'] != '')
            $this->mailin_error[] = __($return_data['error'], 'mailin_i18n');
        else if (!$return_data['isValid'])
            $this->mailin_error[] = __($return_data['error'], 'mailin_i18n');
        else {
            $this->mailin_success[] = __('Successfully Updated', 'mailin_i18n');
            $api_previous_key = get_option('mailin_apikey');
            update_option('mailin_apikey', $api_new_key);
            update_option('mailin_manage_subscribe', 1);
            if ($api_previous_key == null)
                $this->createFolderName();
            elseif ($api_new_key != $api_previous_key)
            {
                $this->mailinRemove();
                $this->createFolderName();
            }
            update_option('mailin_apikey_status', $_POST['mailin_api_status']);
            $this->updateUserLists($api_new_key);
        }
    }
    /**
     * Method is being called at the time of put another key the Mailin plugin.
     */
    public function mailinRemove()
    {
        update_option('mailin_list_selected', '');
        update_option('mailin_smtp', '');
        update_option('mailin_lists', '');
        update_option('mailin_manage_subscribe', '');
        update_option('mailin_apikey_status', '');
        $mailinsmtp_options = array(
            'mail_from' => '',
            'mail_from_name' => '',
            'mailer' => 'mail',
            'mail_set_return_path' => 'false',
            'mailin_smtp_host' => 'localhost',
            'mailin_smtp_port' => '25',
            'mailin_smtp_ssl' => 'none',
            'mailin_smtp_auth' => false,
            'mailin_smtp_user' => '',
            'mailin_smtp_pass' => ''
        );
        // Create the required options...
        foreach ($mailinsmtp_options as $name => $val)
            update_option($name, $val);
    }
    /**
     * Creates a list by the name "wordpress" on user's Mailin account.
     */
    public function createNewList($response, $exist_list)
    {
        if ($exist_list != '')
        {
            $date     = date('dmY');
            $list_name = 'wordpress_'.$date;
        } else
            $list_name = 'wordpress';
        $api_key             = get_option('mailin_apikey');
        $data                = array();
        $data['key']         = $api_key;
        $data['listname']    = $list_name;
        $data['webaction']   = 'NEWLIST';
        $data['list_parent'] = $response; //folder id
        $list_response        = json_decode($this->curlRequest($data));
        $this->sendAllMailIDToMailin($list_response->result);
    }
    /**
     * functions used for create folder
     */
    public function createFolderName()
    {
        $this->createAttributesName();
        $result = $this->checkFolderList();
        if (empty($result[1]))
        {
            $data               = array();
            $data['key']        = get_option('mailin_apikey');
            $data['webaction']  = 'ADDFOLDER';
            $data['foldername'] = 'wordpress';
            $res                = $this->curlRequest($data);
            $res                = json_decode($res);
            $folder_id           = $res->folder_id;
            $exist_list          = '';
        } else
        {
            $folder_id  = $result[0];
            $exist_list = $result[2];
        }
        $this->createNewList($folder_id, $exist_list); // create list in mailin
        $this->partnerWordpress(); // create partner name
    }
    /**
     * Method is used to add the partner's name in Mailin.
     * In this case its "WORDPRESS".
     */
    public function partnerWordpress()
    {
		$data['key']       = get_option('mailin_apikey');
        $data['webaction'] = 'MAILIN-PARTNER';
        $data['partner']   = 'WORDPRESS';
        $list_response      = $this->curlRequest($data);
    }
    /**
     * Fetches all folders and all list within each folder of the user's Mailin 
     * account and displays them to the user. 
     */
	public function checkFolderList()
	{
		$data = array();
		$data['key']       = get_option('mailin_apikey');
		$data['webaction'] = 'DISPLAY-FOLDERS-LISTS';
		$data['ids']  = ''; //folder id
		$s_array       = array();
		$list_response = $this->curlRequest($data);
		$res = json_decode($list_response, true);
		if (isset($res) && !empty($res))
		{
			foreach ($res as $key => $value)
			{
				if (strtolower($value['name']) == 'wordpress')
				{
					$s_array[] = $key;
					$s_array[] = $value['name'];
				}
				if (!empty($value['lists']))
				{
					foreach ($value['lists'] as $val)
					{
						if (strtolower($val['name']) == 'wordpress')
							$s_array[] = $val['name'];
					}
				}
			}
		}
		return $s_array;
	}
    /**
     * Checks if a folder 'Wordpress' and a list "Wordpress" exits in the Mailin account.
     * If they do not exits, this method creates them.
     */
    public function createFolderCaseTwo()
    {
        $result   = $this->checkFolderList();
        $list_name = 'wordpress';
        $param = array();
        $data  = array();
        $folder_id  = $result[0];
        $exist_list = $result[2];
        if (empty($result[1]))
        {
            $data['key']        = get_option('mailin_apikey');
            $data['webaction']  = 'ADDFOLDER';
            $data['foldername'] = 'wordpress';
            $res                = $this->curlRequest($data);
            $res                = json_decode($res);
            $folder_id           = $res->folder_id;
            $param['key']         = get_option('mailin_apikey');
            $param['listname']    = $list_name;
            $param['webaction']   = 'NEWLIST';
            $param['list_parent'] = $folder_id; //folder id
            $list_response         = $this->curlRequest($param);
            $res                  = json_decode($list_response);
            $list_id               = $res->result;
            // import old user to mailin
            $this->sendAllMailIDToMailin($list_id);
        } elseif (empty($exist_list))
        {
            // create list
            $param['key']         = get_option('mailin_apikey');
            $param['listname']    = $list_name;
            $param['webaction']   = 'NEWLIST';
            $param['list_parent'] = $folder_id; //folder id
            $list_response         = $this->curlRequest($param);
            $res                  = json_decode($list_response);
            $list_id               = $res->result;
            $this->sendAllMailIDToMailin($list_id);
        }
    }
    /**
     * Method is used to send all the subscribers from Wordpress to
     * Mailin for adding / updating purpose.
     */
    public function sendAllMailIDToMailin($list)
    {
        $lang = get_bloginfo('language');
        update_option('mailin_list_selected', trim($list));
        if ($lang == 'en-US')
            $l = 'en';
        else
            $l = 'fr';
        $api_key  = get_option('mailin_apikey');
        $allemail = $this->autoSubscribeAfterInstallation();
        if ($allemail != false)
        {
            $data               = array();
            $data['webaction']  = 'MULTI-USERCREADIT';
            $data['key']        = $api_key;
            $data['attributes'] = $allemail;
            $data['lang']       = $l;
            $data['listid']     = $list; // List id should be optional
            $response = $this->curlRequest($data);
        } else
            return false;
    }
    /**
     * Fetches the SMTP and order tracking details
     */
    public function trackingSmtp()
    {
        $api_key = get_option('mailin_apikey');
        $data    = array();
        $data['key'] = $api_key;
        $data['webaction'] = 'TRACKINGDATA';
        return json_decode($this->curlRequest($data));
    }
    /**
     * Create Normal attributes and their values
     * on Mailin platform. This is necessary for the Wordpress to add subscriber's details.
     */
    public function createAttributesName()
    {
        $data                      = array();
        $api_key                   = get_option('mailin_apikey');
        $data['key']               = $api_key;
        $data['webaction']         = 'ATTRIBUTES_CREATION';
        $data['normal_attributes'] = 'PRENOM,text|NOM,text|CLIENT,number';
        return $this->curlRequest($data);
    }
    /**
     * This method is used to check the subscriber's newsletter subscription status in Mailin
     */
    public function checkusermainStatus($result)
    {
        $data       = array();
        $userstatus = array();
        if (isset($result))
            foreach ($result as $value)
                $userstatus[] = $value->email;
        $email = implode(',', $userstatus);
        $api_key           = get_option('mailin_apikey');
        $data['key']       = $api_key;
        $data['webaction'] = 'USERS-STATUS';
        $data['email']     = $email;
        return json_decode($this->curlRequest($data), true);
    }
    /**
     * Fetches all the subscribers of Wordpress and adds them to the Mailin database.
     */
    public function autoSubscribeAfterInstallation()
    {
        $users = $this->getAllSubscribers();
        $data  = array();
        if (!empty($users))
            foreach ($users as $subs)
                $data[] = array(
                    'email' => $subs->email,
                    'PRENOM' => $subs->fname,
                    'NOM' => $subs->lname
                );
        if (!empty($data))
            return json_encode($data);
        else
            return false;
    }
    /**
     * update list form submit
     */
    public function handleUpdateListFormSubmit($list_id)
    {
        if (!empty($list_id))
        {
            $this->updateUserLists();
            $list_id = implode('|', $list_id);
            update_option('mailin_list_selected', $list_id);
            update_option('mailin_unsubscribe', $_POST['unsubscription']);
            $this->mailin_success[] = __('Successfully Updated', 'mailin_i18n');
        } else
            $this->mailin_error[] = __('Please select a list', 'mailin_i18n');
    }
    /**
     * validate manage subscribe
     */
    public function manageSubscribe()
    {
        update_option('mailin_manage_subscribe', $_POST['managesubscribe']);
    }
    /**
     * validate newsletter form
     */
    public function validateNewsletterForm($email, $fname, $lname)
    {
        if (!preg_match('/^([a-z0-9]+([_\.\-]{1}[a-z0-9]+)*){1}([@]){1}([a-z0-9]+([_\-]{1}[a-z0-9]+)*)+(([\.]{1}[a-z]{2,6}){0,3}){1}$/i', $email))
            $this->mailin_error['email'] = __('Please enter valid e-mail', 'mailin_i18n');
        if ($fname == '' && isset($_POST['fname']))
            $this->mailin_error['fname'] = __('Please enter your first name', 'mailin_i18n');
        if ($lname == '' && isset($_POST['lname']))
            $this->mailin_error['lname'] = __('Please enter your last name', 'mailin_i18n');
        return $this->mailin_error;
    }
    /**
     * handle newsletter subscribe form
     */
    public function handleNewsletterSubscribeSubmit()
    {
        if (!$this->syncronizeSetting())
            return false;
        $email  = trim(strip_tags($_POST['mailin_email']));
        $email  = strtolower($email);
        $fname  = isset($_POST['fname']) ? trim(strip_tags($_POST['fname'])) : '';
        $lname  = isset($_POST['lname']) ? trim(strip_tags($_POST['lname'])) : '';
        $action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : 5;
        if ($action == 2)
            $action = 0;
        if ($action == 5)
            $action = 1;
        $this->validateNewsletterForm($email, $fname, $lname);
        if (empty($this->mailin_error))
        {
            $api_key       = get_option('mailin_apikey');
            $selected_list = get_option('mailin_list_selected');
            if ($action == 1)
                $return_data = $this->createUser($api_key, $email, $selected_list, $fname, $lname);
            else
            {
                $return_data = $this->unSubUser($api_key, $email);
                global $wpdb;
                $myrows = $wpdb->get_results('SELECT id FROM '.MAILIN_SUBSCRIBERS.' WHERE email = "'.$email.'" LIMIT 1');
                if ($action == 0 && empty($myrows))
                {
                    $this->mailin_error['doesnotexist'] = __('Email filled does not exist in our database', 'mailin_i18n');
                    return false;
                }
            }
            if (!is_object($return_data))
                $error = 'Oops!, unable to connect!';
            else if ((isset($return_data->msg_ty) && $return_data->msg_ty == 'success') || isset($return_data->result))
            {
                $this->updateUserLists($api_key);
                $this->updateSubscribers($email, $selected_list, $fname, $lname, $action, 0);
                if ($action == 0)
                    $this->mailin_success[] = __('Unsubscription successful', 'mailin_i18n');
                else
                    $this->mailin_success[] = __('Subscription successful', 'mailin_i18n');
            } else if (isset($return_data->msg_ty) && $return_data->msg_ty == 'error')
                $this->mailin_error[] = __($return_data->msg, 'mailin_i18n');
        }
    }
    /**
     * make curl request
     */
   public function curlRequest($data)
   {
        $url   = 'http://ws.mailin.fr/'; //WS URL
        $ch    = curl_init();
        // prepate data for curl post
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
   }
    /**
     * Subscribe a subscriber from Mailin. 
     */
    public function createUser($api_key = null, $email, $list_id, $fname, $lname)
    {
        $client                   = 0;
        $attribute                = $fname.'|'.$lname.'|'.$client;
        $data['key']              = $api_key;
        $data['webaction']        = 'USERCREADITM';
        $data['email']            = $email;
        $data['id']               = '';
        $data['blacklisted']      = '';
        $data['attributes_name']  = 'PRENOM|NOM';
        $data['attributes_value'] = $attribute;
        $data['listid']           = $list_id;
        return json_decode($this->curlRequest($data));
    }
    /**
     * Subscribe a register user from Mailin. 
     */
    public function createRegistrationUser($api_key = null, $email, $list_id, $fname, $lname)
    {
        $client                   = 1;
        $attribute                = $fname.'|'.$lname.'|'.$client;
        $data['key']              = $api_key;
        $data['webaction']        = 'USERCREADITM';
        $data['email']            = $email;
        $data['id']               = '';
        $data['blacklisted']      = '';
        $data['attributes_name']  = 'PRENOM|NOM|CLIENT';
        $data['attributes_value'] = $attribute;
        $data['listid']           = $list_id;
        return json_decode($this->curlRequest($data));
    }
    /**
     * Unsubscribe a subscriber from Mailin. 
     */
    public function unSubUser($api_key = null, $email)
    {
        $data['key']         = $api_key;
        $data['webaction']   = 'EMAILBLACKLIST';
        $data['blacklisted'] = '0';
        $data['email']       = $email;
        return json_decode($this->curlRequest($data));
    }
    /**
     * Fetches all the list of the user from the Mailin platform.
     */
    public function getUserLists($api_key = null, $list_id = null)
    {
       if ($api_key == '')
            return;
        $data              = array();
        $data['key']       = $api_key;
        $data['webaction'] = 'DISPLAYLISTDATA';
        if ($list_id != null)
            $data['listids'] = $list_id;
        return json_decode($this->curlRequest($data));
    }
    /**
     * This is an automated version of the usersStatusTimeStamp method but is called using a CRON.
     */
    public function usersStatus($api_key)
    {
        if ($api_key == '')
            return;
        $timezone = get_option('timezone_string');
        $users    = $this->getAllUsers();
        $userstatus = array();
        $data       = array();
        if (!empty($users))
        {
            foreach ($users as $subs)
                $userstatus[] = $subs->email.','.$subs->subscribed.','.$subs->create_date;
        }
        $userstatus = implode('|', $userstatus);
        $data['key']         = $api_key;
        $data['webaction']   = 'UPDATE-USER-SUBSCRIPTION-STATUS';
        $data['timezone']    = $timezone;
        $data['user_status'] = $userstatus;
        return json_decode($this->curlRequest($data), true);
    }
    /**
     * validate user
     */
    public function validateUser($api_key, $email)
    {
        if ($api_key == '')
            return;
        $data              = array();
        $data['key']       = $api_key;
        $data['webaction'] = 'DISPLAYUSERDETAIL';
        $data['email']     = $email;
        return json_decode($this->curlRequest($data));
    }
    /**
     * Checks whether the Mailin API key and the Mailin subscription form is enabled
     * and returns the true|false accordingly.
     */
    public function syncronizeSetting()
    {
        $api_key = get_option('mailin_apikey');
        $mailin_apikey_status = get_option('mailin_apikey_status');
        $mailin_manage_subscribe = get_option('mailin_manage_subscribe');
        if ($api_key == false || $mailin_apikey_status == 0 || $mailin_manage_subscribe == 0)
            return false;
        else
            return true;
    }
    /**
     * Update subscriber
     */
    public function updateSubscribers($email, $selected_list, $fname, $lname, $action, $type)
    {
        $selected_list = explode('|', $selected_list);
        $selected_list = implode(',', $selected_list);
        if ($type == 1)
            $client = 1;
        else
            $client = 0;
        global $wpdb;
        $myrows = $wpdb->get_results('SELECT id FROM '.MAILIN_SUBSCRIBERS.' WHERE email = "'.$email.'" LIMIT 1');
        if ($action == 0 && empty($myrows))
        {
            $this->mailin_error['doesnotexist'] = __('Email filled does not exist in our database', 'mailin_i18n');
            return false;
        }
        $timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
        $timezone_format = date_i18n($timezone_format);
        // end local time code
        if (empty($myrows))
        {
            $sql = 'INSERT INTO '.MAILIN_SUBSCRIBERS.'
			SET email= "'.$email.'",subscribed = "'.$action.'" , fname = "'.$fname.'" ,
			lname = "'.$lname.'" , list = "'.$selected_list.'" ,  create_date= "'.$timezone_format.'" , client = "'.$client.'" ';
            $wpdb->query($sql);
        } else
        {
            $sql = 'UPDATE '.MAILIN_SUBSCRIBERS.'  SET list = "'.$selected_list.'" ,  fname = "'.$fname.'" ,
			lname = "'.$lname.'" , subscribed = "'.$action.'", create_date= "'.$timezone_format.'", client = "'.$client.'" WHERE id = '.$myrows[0]->id;
            $wpdb->query($sql);
        }
    }
    public function smtpUpdateConfiguration()
    {
        update_option('mailin_smtp', $_POST['mailin_smtp_action']);
        if ($_POST['mailin_smtp_action'] == 0)
        {
            $mailinsmtp_options = array(
                'mail_from' => '',
                'mail_from_name' => '',
                'mailer' => 'mail',
                'mail_set_return_path' => 'false',
                'mailin_smtp_host' => 'localhost',
                'mailin_smtp_port' => '25',
                'mailin_smtp_ssl' => 'none',
                'mailin_smtp_auth' => false,
                'mailin_smtp_user' => '',
                'mailin_smtp_pass' => ''
            );
            // Create the required options...
            foreach ($mailinsmtp_options as $name => $val)
                update_option($name, $val);
        } else
        {
            $value = $this->trackingSmtp();
            if ($value->result->relay_data->status == 'enabled')
            {
                $mailinsmtp_options = array(
                    'mail_from' => '',
                    'mail_from_name' => '',
                    'mailer' => 'smtp',
                    'mail_set_return_path' => 'false',
                    'mailin_smtp_host' => $value->result->relay_data->data->relay,
                    'mailin_smtp_port' => $value->result->relay_data->data->port,
                    'mailin_smtp_ssl' => 'true',
                    'mailin_smtp_auth' => 'true',
                    'mailin_smtp_user' => $value->result->relay_data->data->username,
                    'mailin_smtp_pass' => $value->result->relay_data->data->password
                );
                // Create the required options...
                foreach ($mailinsmtp_options as $name => $val)
                    update_option($name, $val);
                update_option('mailin_smtp', 1);
            } else
                update_option('mailin_smtp', 0);
        }
    }
    /**
     * get all subscribers users
     */
    public function getAllSubscribers($subs = 1)
    {
        global $wpdb;
        $sql = 'SELECT * FROM '.MAILIN_SUBSCRIBERS.' WHERE 1=1 ';
        if ($subs == 1)
            $sql .= ' AND subscribed = "1" ';
        return $wpdb->get_results($sql);
    }
    /**
     *  Returns the list of active  user details 
     * 
     */
    public function getAllUsers()
    {
        global $wpdb;
        $sql = 'SELECT * FROM '.MAILIN_SUBSCRIBERS.' WHERE 1=1 ';
        return $wpdb->get_results($sql);
    }
    /**
     *  Returns the list of active  user details for pagination
     * 
     */
    public function getAllUsersPagination($start, $page)
    {
        global $wpdb;
        $sql = 'SELECT * FROM '.MAILIN_SUBSCRIBERS.' LIMIT '.$start.','.$page;
        return $wpdb->get_results($sql);
    }
    /**
     *  Returns the list of active  user details for pagination
     * 
     */
    public function getTotalUsers()
    {
        global $wpdb;
        return $wpdb->get_results('SELECT COUNT(*) as total FROM '.MAILIN_SUBSCRIBERS.' WHERE 1=1 ');
    }
    /**
     *  Get the total count of the  unsubscribed
     *
     */
    public function getTotalUnsubUsers()
    {
        global $wpdb;
        return $wpdb->get_results('SELECT COUNT(*) as total FROM '.MAILIN_SUBSCRIBERS.' WHERE subscribed=0 ');
    }
    /**
     *  Get the total count of the  subscribed
     *
     */
    public function getTotalsubUsers()
    {
        global $wpdb;
        return $wpdb->get_results('SELECT COUNT(*) as total FROM '.MAILIN_SUBSCRIBERS.' WHERE subscribed=1 ');
    }
    /**
     * syncronize user with mailin
     */
    public function syncUsers()
    {
        if (get_option('mailin_list_selected') != '')
        {
            global $wpdb;
            $api_key = get_option('mailin_apikey');
            $dove = $this->usersStatus($api_key);
            $timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
            $timezone_format = date_i18n($timezone_format);
            if (empty($dove['errorMsg']))
            {
                foreach ($dove as $valuearray)
                {
                    foreach ($valuearray as $key => $value)
                    {
                        $sql    = 'UPDATE '.MAILIN_SUBSCRIBERS.' SET create_date = '.$timezone_format.', subscribed = '.$status.' WHERE email = "'.strtolower(trim($key)).'" ';
                        $myrows = $wpdb->query($sql);
                    } // end foreach
                } // end foreach
                echo 'done';
            }
            return true;
        } else
            return false;
    }
}
