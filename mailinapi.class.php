<?php

/**
 * This class has functions that supports interaction with Mailin server
*/

class mailin_API {

  /**
    error messages default set to blank
  */
  public $_mailin_error = array();

  /**
    success messages default set to blank
  */
  public $_mailin_success = array();

  /**
  *  Returns all lists from databse
    * @return Array
  */
  public function getListDetails($list_id){

    $lists = get_option('mailin_lists');
    $lists = unserialize($lists);

  }

  /**
    * Updates lists in database after fetching from mailin
    * @return Array
  */
  public function updateUserLists($api_key = null){

    if($api_key == null){
        $api_key = get_option('mailin_apikey');
    }

    if($api_key == ''){
        return ;
    }

    $returnData = $this->getUserLists($api_key);

    if(isset($returnData->result) && !empty($returnData->result)){
         $listData = $returnData->result;
         $lists = serialize($listData);
         update_option('mailin_lists', $lists);
    }

  }

  /**
    * Updates user campaigns in database after fetching from mailin
    * @return array of list
  */
  public function updateUserCampaigns($api_key = null){

    if($api_key == null){
        $api_key = get_option('mailin_apikey');
    }

    if($api_key == ''){
        return ;
    }

    $returnData = $this->getUserCampaigns($api_key);

    if(isset($returnData->result) && !empty($returnData->result)){
         $campaigns = $returnData->result;
         $campaigns = serialize($campaigns);
         update_option('mailin_campaigns', $campaigns);
    }

  }


  /**
    * Validate API key entered by user
    * @return array with error message (if invalid), user's lists (if valid)
  */
  public function validateAPIkey($api_key = null){

    $isValid = false;
    $error = '';
    $listData = array();

    if($api_key == ''){

        $error = 'Please enter API key';

    }else{

        $returnData = $this->getUserLists($api_key);

        if(!is_object($returnData)){

            $error = 'Oops!, unable to connect!';

        }else if(isset($returnData->errorMsg)){

            $error = $returnData->errorMsg;

        }else if(isset($returnData->result)){

            $isValid = true;
            if(!empty($returnData->result)){
                $listData = $returnData->result;
            }
        }
    }

    $returnData = array('isValid' => $isValid , 'data' => $listData ,  'error' => $error);
    return $returnData;

  }


  /**
    * Validate API key, saves API key in DB
    * Saves users campaigns, Saves users lists
    * @return void
  */
  public function handle_apikey_form_submit($api_key = null){

      $api_key = strip_tags(trim($api_key));

      $returnData = $this->validateAPIkey($api_key);

      if($returnData['error'] != ''){

          $this->_mailin_error[] = __( $returnData['error'] , 'mailin_i18n');

      }else if(!$returnData['isValid']){

          $this->_mailin_error[] = __( $returnData['error'], 'mailin_i18n');

      }else{

          $this->_mailin_success[] = __('Your API is valid, your lists are fetched and saved in the database.', 'mailin_i18n');

          $this->updateUserLists($api_key);
          $this->updateUserCampaigns($api_key);

          if(!empty($returnData['data'])){

              $lists = serialize($returnData['data']);
              update_option('mailin_lists', $lists);

          }
          update_option('mailin_apikey', $api_key);
          update_option('mailin_list_selected', '');

      }

  }


  /**
    * Saves selected lists (in which newsletter subscriptions will be added)
    * @return void
  */
  public function handle_updatelist_form_submit($list_id){

      if(!empty($list_id)){

          $this->updateUserLists();
          $list_id = implode('|', $list_id);
          update_option('mailin_list_selected', $list_id);
          $this->_mailin_success[] = __('List updated successfully', 'mailin_i18n');

      }else{

          $this->_mailin_error[] = __('Please select a list' , 'mailin_i18n');

      }
  }

  /**
    * Removes api key, mailin lists and selected lists from DB
    * @return void
  */
  public function handle_logout_form_submit(){

      update_option('mailin_apikey', '');
      update_option('mailin_lists', '');
      update_option('mailin_list_selected', '');

  }


  /**
    * Validate newsletter form and sets up error messages accordingly.
    * @return void
  */
  function validate_newsletter_form($email, $fname , $lname) {

    $result = true;

    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
        //$result = false;
        $this->_mailin_error['email'] = __("Please enter valid e-mail" , 'mailin_i18n');
    }

    if($fname == ''){
        $this->_mailin_error['fname'] = __("Please enter your first name" , 'mailin_i18n');
    }

    if($lname == ''){
        $this->_mailin_error['lname'] = __("Please enter your last name" , 'mailin_i18n');
    }
    return $result;
  }



  /**
    * Validate newsletter form and sets up error messages accordingly.
    * @return void
  */
  public function handle_newsletter_subscribe_submit(){

    $email  =  trim(strip_tags($_POST['mailin_email']));
    $email = strtolower($email);

    $fname  =  isset($_POST['fname']) ? trim(strip_tags($_POST['fname'])) : '';
    $lname  =  isset($_POST['lname']) ? trim(strip_tags($_POST['lname'])) : '';

    $this->validate_newsletter_form($email, $fname , $lname);

    if(empty($this->_mailin_error)){

        $api_key = get_option('mailin_apikey');
        $selected_list = get_option('mailin_list_selected');

        $returnData = $this->createUser($api_key ,$email , $selected_list);

        if(!is_object($returnData)){

            $error = 'Oops!, unable to connect!';

        }else if((isset($returnData->msgTy) && $returnData->msgTy == 'success') || isset($returnData->result)){

            $this-> updateUserLists($api_key);

            $this-> updateSubscribers($email , $selected_list, $fname , $lname);

            $this->_mailin_success[] = __('You have successfully subscribed Mailin newsletter!','mailin_i18n' );

        }else if(isset($returnData->msgTy) && $returnData->msgTy == 'error'){

            $this->_mailin_error[] = __($returnData->msg,'mailin_i18n');

        }
    }

  }

  public function add_subscription($email){

    get_option('USERCREADIT');
    //add user through ADD USER FUNCTION

  }

  /**
    * Makes curl request to Mailin server with array of vars
    * @return array returned by API
  */
  function curl_request($data) {

      $url = 'http://ws.mailin.fr/'; //WS URL
      $ch = curl_init();
      // prepate data for curl post

      $ndata='';
      if(is_array($data)){
          foreach($data AS $key=>$value){
              $ndata .=$key.'='.urlencode($value).'&';
          }
      }else {
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


  /**
    * Creates a user under the provided list id
    * @return Array
  */
  public function createUser($api_key = null, $email, $list_id){

    $data['key']= $api_key ;
    $data['webaction']='USERCREADIT';
    $data['email']= $email;
    $data['id']='';
    $data['blacklisted']='';
    $data['attributes_name']='';
    $data['attributes_value']='';
    $data['listid']= $list_id;

    $return = $this->curl_request($data);
    $return = json_decode($return);
    return $return;

  }


  /**
    * Return list data from Mailin server
    * @return Array
  */
  public function getUserLists($api_key = null , $list_id = null){

    if($api_key == '')  {
      return ;
    }

    $data = array();
    $data['key'] = $api_key;
    $data['webaction']= 'DISPLAYLISTDATA';

    if($list_id != null){
        $data['listids'] = $list_id;
    }

    $return = $this->curl_request($data);
    $return = json_decode($return);

    return $return;
  }



  /**
    * Returns user data of provided list Ids from Mailin server
    * @return Array;
  */
  function getListUsers($api_key, $list_ids){


    if($api_key == '')  {
      return ;
    }

    $data = array();
    $data['webaction']='DISPLAYLISTDATABLACK';
    $data['key']=$api_key;

    $data['listids']= $list_ids;

    $return = $this->curl_request($data);
    $return = json_decode($return);

    return $return;

  }


  /**
    * Returns user's campaigns from Mailin
    * @return Array;
  */
  public function getUserCampaigns($api_key = null){

    if($api_key == '')  {
      return ;
    }

    $data = array();
    $data['key'] = $api_key;
    $data['webaction']= 'CAMPAIGNDETAIL';
    $data['show']='ALL';

    $return = $this->curl_request($data);
    $return = json_decode($return);
    return $return;
  }



  /**
    * Returns user details of email provided from Mailin server
    * @return Array;
  */
  public function validateUser($api_key, $email){

    if($api_key == '')  {
      return ;
    }

    $data = array();
    $data['key'] = $api_key;
    $data['webaction']= 'DISPLAYUSERDETAIL';
    $data['email']= $email;

    $return = $this->curl_request($data);
    $return = json_decode($return);
    return $return;
  }



  /**
    * Inserts/updates(user status) in subscription table
    * @return void;
  */
  public function updateSubscribers($email , $selected_list, $fname , $lname){

    $selected_list = explode('|', $selected_list);
    $selected_list = implode(',', $selected_list);

    global $wpdb;
    $myrows = $wpdb->get_results( "SELECT id FROM ".MAILIN_SUBSCRIBERS." WHERE email = '".$email."' LIMIT 1");

    if(empty($myrows)){

       $sql = "INSERT INTO ".MAILIN_SUBSCRIBERS."
               SET email= '".$email."', fname = '".$fname."' ,
               lname = '".$lname."' , list = '".$selected_list."' ,  create_date= '".date('Y-m-d H:i:s')."' ";

       $wpdb->query($sql);

    }else{

          $sql = "UPDATE ".MAILIN_SUBSCRIBERS."  SET list = '".$selected_list."' ,  fname = '".$fname."' ,
               lname = '".$lname."' , create_date= '".date('Y-m-d H:i:s')."' WHERE id = ".$myrows[0]->id." ";
          $wpdb->query($sql);
     }
  }


  /**
    * Returns subscribers from subscription table
    * @return void;
  */
  public function getAllSubscribers($subs = 1){

      global $wpdb;

      $sql = "SELECT * FROM ".MAILIN_SUBSCRIBERS." WHERE 1=1 ";

      if($subs == 1){
          $sql .= " AND subscribed = '1' ";
      }

      $myrows = $wpdb->get_results($sql);
      return $myrows;
  }


  /**
    * Synchronize the user's status in subscription table with user's status on Mailin
    * @return bool;
  */
  public function syncUsers(){

      global $wpdb;
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

      $list_users = $this->getListUsers($api_key , $list_ids);


      if(!empty($list_users->result)){

          foreach($list_users->result as $key=>$lists){

              if(!empty($lists)){
                  foreach($lists as $users){

                      if(isset($users->blacklisted)){

                          if($users->blacklisted == '1'){
                              $sql = "UPDATE ".MAILIN_SUBSCRIBERS." SET subscribed = '0' WHERE email = '".strtolower(trim($users->email))."' " ;
                          }else{
                              $sql = "UPDATE ".MAILIN_SUBSCRIBERS." SET subscribed = '1' WHERE email = '".strtolower(trim($users->email))."' " ;

                          }
                          $myrows = $wpdb->query($sql);
                      }
                  }
              }
          }
      }
      return true;
  }

}
?>
