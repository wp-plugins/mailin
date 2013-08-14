<script src="<?php echo plugins_url(); ?>/mailin/js/jquery-1.9.1.min.js" ></script>
<script src="<?php echo plugins_url(); ?>/mailin/js/jquery-ui.min.js" ></script>
<script>
	var base_url="<?php echo plugins_url(); ?>";
	var selectoption = "<?php esc_html_e('Select option', 'mailin_i18n' ); ?>";
	var selected = "<?php esc_html_e('Selected', 'mailin_i18n' ); ?>";
</script>
<script src="<?php echo plugins_url(); ?>/mailin/js/jquery.multiselect.min.js" ></script>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
<link type="text/css" rel="stylesheet" media="screen" href="<?php echo plugins_url(); ?>/mailin/css/jquery.multiselect.css" />


<script>
	var base_url="<?php echo plugins_url(); ?>";
	var selectoption = "<?php esc_html_e('Select option', 'mailin_i18n' ); ?>";
	var selected = "<?php esc_html_e('Selected', 'mailin_i18n' ); ?>";
</script>
<input type="hidden" name="token" id="token" value="<?php echo md5(get_option('mailin_apikey')); ?>" />
<input type="hidden" name="language" id="language" value="<?php echo get_bloginfo('language'); ?>" />
<script src="<?php echo plugins_url(); ?>/mailin/js/mailin.js" ></script>
<div class="">
<img src="<?php echo plugins_url(); ?>/mailin/img/
<?php esc_html_e('mailinblue.jpg', 'mailin_i18n' ); ?>" class="mailin-logo">
<div style="float:left;font-weight:bold; padding:25px 0px 0px 0px; color:#268CCD;">
	<?php esc_html_e('Mailinblue : THE all-in-one plugin for your marketing and transactional emails.', 'mailin_i18n' ); ?></div><div class="clear"></div>
</div>
<div class="mailin_row">
<fieldset class="fields">
	<legend class="lgend">
	<img  src="<?php echo plugins_url(); ?>/mailin/img/logo.gif">
	<?php esc_html_e('Mailinblue', 'mailin_i18n' ); ?>
	</legend>
		<div class="contact-details">
		<h2 style="color:#268CCD;"><?php esc_html_e('Contact Mailinblue team', 'mailin_i18n' ); ?> </h2>
		<div style="clear: both;"></div>
		<p> <?php esc_html_e('Contact us:', 'mailin_i18n' ); ?> 
		<br><br><?php esc_html_e('Email:', 'mailin_i18n' ); ?>
		<a style="color:#268CCD;" href="mailto:<?php esc_html_e('contact@mailinblue.com', 'mailin_i18n' ); ?>">
		<?php esc_html_e('contact@mailinblue.com', 'mailin_i18n' ); ?>
		</a><br>
		<?php esc_html_e('Phone : 0899 25 30 61', 'mailin_i18n' ); ?></p>
		<p style="padding-top:20px;"><b><?php esc_html_e('For further informations, please visit our website:', 'mailin_i18n' ); ?>
		</b><br><a style="color:#268CCD;" target="_blank" href="<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?>">
		<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?></a></p>
		</div>
		<div><?php esc_html_e('With the Mailinblue plugin, you can find everything you need to easily and efficiently send your emailing campains to your prospects and customers. ', 'mailin_i18n' ); ?>
		<br><br>
		<ul class="listt">
		<li><?php esc_html_e('Synchronize your subscribers with Mailinblue (subscribed and unsubscribed contacts)', 'mailin_i18n' ); ?></li>
		<li><?php esc_html_e('Easily create good looking emailings', 'mailin_i18n' ); ?></li>
		<li><?php esc_html_e('Schedule your campaigns', 'mailin_i18n' ); ?></li>
		<li><?php esc_html_e('Track your results and optimize', 'mailin_i18n' ); ?></li>
		
		<li><?php esc_html_e('Monitor your transactional emails (purchase confirmation, password reset …) with a better deliverability and real-time analytics', 'mailin_i18n' ); ?></li>
		</ul>
		<b><?php esc_html_e('Why should you use Mailinblue ?', 'mailin_i18n' ); ?></b>
		<ul class="listt">
		<li><?php esc_html_e('Optimized deliverability', 'mailin_i18n' ); ?></li>
		<li><?php esc_html_e('Unbeatable pricing &ndash; best value in the industry', 'mailin_i18n' ); ?></li>
		<li><?php esc_html_e('Technical support, by phone or by email', 'mailin_i18n' ); ?></li><br>
		</ul>
		</div><div style="clear:both;">&nbsp;</div>
		</fieldset>
</div>

<div class="mailin_row">
<fieldset class="fields">
	<legend class="lgend"><img  src="<?php echo plugins_url(); ?>/mailin/img/logo.gif">
	 <?php esc_html_e('Prerequisites', 'mailin_i18n' ); ?></legend>
<span> <?php esc_html_e('- You should have a Mailinblue account. You can create a free account here:', 'mailin_i18n' ); ?> 
<a target="_blank" href="<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?>">
&nbsp;<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?></a><br></span></fieldset>
</div>
<?php

$checked1 = '';
$checked2 = '';
$checked3 = '';
$checked4 = '';
$checked5 = '';
$checked6 = '';
$style = '';

 $mailin_apikey_status = get_option('mailin_apikey_status');
	if ($mailin_apikey_status == 1)
		$checked1 = 'checked="checked"';
	else
		$checked2 = 'checked="checked"';

	 $mailin_unsubscribe = get_option('mailin_unsubscribe');
	if ($mailin_unsubscribe == 1)
		$checked5 = 'checked="checked"';
	else
		$checked6 = 'checked="checked"';
?>

<div class = "mailin_row">
<fieldset class = "fields">
<legend class = "lgend"><img  src = "<?php echo plugins_url(); ?>/mailin/img/logo.gif">
<?php esc_html_e('Settings', 'mailin_i18n' ); ?></legend>

   <form method = "post" action = "options-general.php?page=mailin_options">
        
      <table class = "form-table blog_table">
		  
			<tr>
			<td style = "width:250px; text-align:right;">
			<label style = "word-wrap:break-word; width:244px;">
			 <?php esc_html_e('Activate Mailinblue:', 'mailin_i18n' ); ?></label>
			</td>
			<td>
				<?php esc_html_e('Yes', 'mailin_i18n' ); ?>
				<input type = "radio" <?php echo $checked1; ?> size = "32" value = "1" class = "mailin_api_status"  name = "mailin_api_status">
				<?php esc_html_e('No', 'mailin_i18n' ); ?>
				<input type = "radio" <?php echo $checked2; ?> size = "32" value = "0" class = "mailin_api_status"  name = "mailin_api_status">
			</td>
			</tr>
         
         <tr valign = "top" class = "apikey">
            <td scope = "row" style = "width:250px; text-align:right;"><?php esc_html_e('API Key', 'mailin_i18n'); ?>:</td>
            <td>
               <input name = "mailin_apikey" type = "text" id = "mailin_apikey" class = "code" value = "<?php echo esc_attr($api_key); ?>" size = "32" />
               <span title = "<?php esc_html_e('Please enter your API key from your Mailinblue account and if you donot have it yet, please go to www.mailinblue.com and subscribe. You can then get the API key from https://my.mailinblue.com/advanced/apikey', 'mailin_i18n' ); ?>" class = "toolTip">&nbsp;</span>
            </td>
         </tr>
         <tr>
            <td colspan = "2">
               <input type = "hidden" name = "mailin_form_action" value = "apikey_update"/>
               <input type = "submit" style = "float: left; margin-left:260px;" name = "Submit" value = "<?php esc_attr_e('Validate & Save', 'mailin_i18n');?>" class = "button" />
            </td>
         </tr>
      </table>
   </form>
</fieldset>
</div>
<?php  

	 $mailin_manage_subscribe = get_option('mailin_manage_subscribe');
	if ($mailin_manage_subscribe == 1)
		$checked3 = 'checked = "checked"';
	else
		$checked4 = 'checked = "checked"';
?>

<div class = "mailin_row blog_form">
<fieldset class  =  "fields">
<legend class = "lgend"><img  src = "<?php echo plugins_url(); ?>/mailin/img/logo.gif" >
 <?php esc_html_e('Your Lists', 'mailin_i18n'); ?></legend>


    
      <table class = "blog_table" style = "width:100%;">
		  <form action = "options-general.php?page=mailin_options" method = "post">
         <tbody>
            <tr>
               <td>
				   
                  <input type = "hidden" value = "update_list" name = "mailin_form_action">
					<table class = "optiontable form-table">
						<tr>
						<td style = "width:259px; text-align:right;">
						<label style = "word-wrap:break-word; width:244px;">
						<?php esc_html_e('Activate Mailinblue to manage subscribers:', 'mailin_i18n' ); ?></label>
						</td>
						<td>
						<?php esc_html_e('Yes', 'mailin_i18n' ); ?>
						<input type = "radio" <?php echo $checked3; ?> size = "32" value = "1" class = "managesubscribe" id = "managesubscribe" name = "managesubscribe">
						<?php esc_html_e('No', 'mailin_i18n' ); ?>
						<input type = "radio"  <?php echo $checked4; ?> size = "32" value = "0" class = "managesubscribe" id = "managesubscribe" name = "managesubscribe"> 
						</td>
						</tr>
					</table>
					
					<table class = "optiontable form-table">
						<tr>
						<td style = "width:259px; text-align:right;">
						<label style = "word-wrap:break-word; width:244px;">
						<?php esc_html_e('Manage unsubscription from Front-Office', 'mailin_i18n' ); ?></label>
						</td>
						<td>
						<?php esc_html_e('Yes', 'mailin_i18n' ); ?>
						<input type = "radio" <?php echo $checked5; ?> size = "32" value = "1" class = "unsubscription" id = "unsubscription" name = "unsubscription">
						<?php esc_html_e('No', 'mailin_i18n' ); ?>
						<input type = "radio"  <?php echo $checked6; ?> size = "32" value = "0" class = "unsubscription" id = "unsubscription" name = "unsubscription"> 
						<span  class = "toolTip" title = "<?php esc_html_e('If you activate this option, you will let your customers the possiblity to unsubscribe from your newsletter using the newsletter block displayed in the home page.', 'mailin_i18n'); ?>">&nbsp;</span>
						</td>
						
						</tr>
					</table>
					
					<table class = "optiontable form-table subscribe">		
						<tr>
						 <th scope = "row" style = "width:250px; text-align:right;"><label for = "to"><?php esc_html_e('Your Lists', 'mailin_i18n' ); ?>
                        </label>
						</th>
                        <td >
					
						<form action = "options-general.php?page = mailin_options" method = "post">
                         <select id = "mySelectBox" style = "height:150px;width:250px;" name = 'mailin_list[]' multiple = 'multiple' >

                        <?php
                           $m_obj = new MailinApi;
                           $m_obj->createFolderCaseTwo();
                           $mailin_apikey = get_option('mailin_apikey');
                           if ($mailin_apikey)
                           	$m_obj->updateUserLists($mailin_apikey);
                           $mailin_lists = get_option('mailin_lists');
                           if (!is_array($mailin_lists))
                           	$mailin_lists = unserialize($mailin_lists);
                           	if (!empty($mailin_lists))
                           	{
								$selected_list = get_option( 'mailin_list_selected');
								$selected_list = explode('|', $selected_list);
								foreach ($mailin_lists as $item)
								{
									$selected = '';
									if ($selected_list != '' && in_array($item->id, $selected_list))
										$selected = 'selected = "selected"';
								?>
						 
								<option value = '<?php echo $item->id ?>'
								 <?php echo $selected; ?> >
								 <?php echo $item->name?></option>

							
								<?php 
								}
								?>
								 </select>
								 <span  class = "toolTip" title = "<?php esc_html_e('Select the contact list where you want to save the contacts of your site WordPress. By default, we have created a list WordPress in your Mailinblue account and we have selected it', 'mailin_i18n'); ?>">&nbsp;</span>
								</td>
							</tr>
							  
							<?php
							}
							 ?>
                   <tr>
                  <td colspan = "2">
					<input type = "submit" style = "float: left; margin-left:260px;" name = "submit"
					id = "submit" class = "button"
					value = "<?php _e('Validate & Save', 'mailin_i18n'); ?>" />
					</div>
                  </td>
                  </tr>
                  <tr><td colspan = "2">
					<div style = "float:left;">
					
					<?php	
						$cron_link = MAILIN_URL.'cron.php';
						echo esc_html_e('To synchronize the emails of your customers from Mailinblue platform to your WordPress website, you should run ', 'mailin_i18n').'<a target="_blank" href ="'.$cron_link.'?token='.md5(get_option('mailin_apikey')).'" >';
						echo esc_html_e(' this link', 'mailin_i18n').'</a>';
						echo esc_html_e(' each day.', 'mailin_i18n');
					?>
					
					</div>
                  
                  </td></tr>
				 
                  </table>
                 
               </td>
            </tr>
         </tbody>
        </form>
      </table>
            
    	
</fieldset>
</div>

<?php  $options = get_option('mailin_smtp');   ?>
<div class = "mailin_row blog_form">
<fieldset class = "fields">
<legend class = "lgend"><img  src = "<?php echo plugins_url(); ?>/mailin/img/logo.gif">
  <?php esc_html_e('Activate Mailinblue SMTP for your transactional emails', 'mailin_i18n'); ?></legend>

 

   <table style = "width: 100%;" class = "blog_table">
      <tbody>
		<tr>
		<td colspan = "2">
		<a href = "<?php esc_html_e('http://www.mailinblue.com/mailin-smtp', 'mailin_i18n' ); ?>"  target = "_blank" >
		<?php esc_html_e('Mailinblue SMTP', 'mailin_i18n' ); ?>
		</a>
		<?php esc_html_e('  is a product of Mailinblue and it allows you to manage your transactional emails', 'mailin_i18n'); ?>
		<span class = "toolTip" title = "<?php esc_html_e('Transactional email is an expected email because it has been triggered automatically after a transaction or a specific event. Common examples of transactional email is : account opening and welcome message, order shipment confirmation, shipment tracking and purchase order status, registration via a contact form, account termination, payment confirmation, invoice...', 'mailin_i18n'); ?>">&nbsp;</span>

		</td>
		</tr>
		
         <tr>
            <td style = "width:250px; text-align:right;">
               <span style = "word-wrap:break-word; width:244px;">
                  <?php echo esc_html_e('Activate Mailinblue SMTP:', 'mailin_i18n'); ?>
               </span>
            </td>
            <td>
				
				<?php echo esc_html_e('Yes', 'mailin_i18n'); ?>
				<input name = "mailin_smtp" type = "radio" id = "mailin_smtp"
				class = "mailin_smtp_action" value = "1" size = "32"
				<?php if ($options == 1)echo 'checked'; ?> />
				<?php echo esc_html_e('No', 'mailin_i18n'); ?>
				<input name = "mailin_smtp" type = "radio" id = "mailin_smtp"
				class = "mailin_smtp_action" <?php if ($options == 0)echo 'checked';?> value = "0" size="32" /> 
				
            </td>
         </tr>
       
		  <?php 
			  if (!get_option('mailin_smtp'))
					$style = 'style="display:none;"';
		  ?>
      <tr id="smtptest" <?php echo $style; ?> >
         <td colspan="2">
            <form action="options-general.php?page=mailin_options" method="post">
               <table class="optiontable form-table">
                  <tr valign="top">
                     <th scope="row" style="width:250px; text-align:right;">
						 <span for="to"><?php esc_html_e('Send email test From / To', 'mailin_i18n' ); ?>
                        </span>
                     </th>
                     <td><input name="to" type="text" id="to" value="<?php echo get_option('admin_email'); ?>"
                        class="regular-text" />
                     </td>
                  </tr>
                  <tr>
                  <td colspan="2">
					<input type="submit" style="float: left; margin-left:260px;" name="smtp_mailin"
					id="smtp_mailin" class="button"
					value="<?php _e('Send', 'mailin_i18n'); ?>" />
					</div>
                  </td>
                  </tr>
               </table>
               </form>
         </td>
      </tr>
      </tbody>
   </table>		
</fieldset>
</div>
  


<?php 

$unsub = $m_obj->getTotalUnsubUsers();

$sub = $m_obj->getTotalsubUsers();

?>
 
<div class="mailin_row subscribe">
<input type="hidden" name="pagenumber" id="pagenumber" value="" />
<fieldset class="fields">
	<legend class="lgend"><img  src="<?php echo plugins_url(); ?>/mailin/img/logo.gif">
	<?php esc_html_e('Contacts list', 'mailin_i18n' ); ?> </legend>

<?php esc_html_e('You have ', 'mailin_i18n' ); ?>
<?php echo $sub[0]->total; ?>
<?php esc_html_e(' contacts subscribed and ', 'mailin_i18n' ); ?>
<?php echo $unsub[0]->total; ?>
<?php esc_html_e(' contacts unsubscribed from WordPress. ', 'mailin_i18n' ); ?>
<?php $total = $sub[0]->total + $unsub[0]->total; 
if($total != 0)
{
?>
<span id="Spantextmore"><?php esc_html_e('For more details,', 'mailin_i18n' ); ?>
</span><span style="display:none;" id="Spantextless">
	<?php esc_html_e('For less details,', 'mailin_i18n' ); ?></span>
	<a id="showUserlist" href="javascript:void(0);"><?php esc_html_e('click here', 'mailin_i18n' ); ?> </a>
<br /><br />
<?php } ?>
<table class="widefat" style="display: none;">
	
   <thead>
      <tr>
         <th><?php echo esc_html_e( 'No.', 'mailin_i18n'); ?></th>
         <th><?php echo esc_html_e( 'First name', 'mailin_i18n'); ?>
         </th>
         <th><?php echo esc_html_e( 'Last name', 'mailin_i18n'); ?>
         </th>
         <th><?php echo esc_html_e( 'Email', 'mailin_i18n'); ?></th>
         
         <th><?php echo esc_html_e( 'Client', 'mailin_i18n'); ?></th>
         
         <th><?php echo esc_html_e( 'Newsletter WordPress status', 'mailin_i18n'); ?>
         </th>
         <th><?php echo esc_html_e( 'Newsletter Mailin Status', 'mailin_i18n'); ?>
         <span title="<?php esc_html_e('Click on the icon to subscribe / unsubscribe the contact from Mailinblue and WordPress.', 'mailin_i18n' ); ?>" class="toolTip">&nbsp;</span>
         </th>
         <th><?php echo esc_html_e( 'Date of last update', 'mailin_i18n'); ?>
         </th>
      </tr>
   </thead>
   
   <tbody style="height: 200px; overflow-y: scroll;" class="midleft">
 
   </tbody>
</table>

</div>



