<script src="<?php echo plugins_url(); ?>/mailin/js/jquery-1.9.1.min.js" ></script>
<script>
$(document).ready(function () {
   jQuery('.toolTip')
            .hover(function () {
                var title = jQuery(this).attr('title');
                var offset = jQuery(this).offset();

                jQuery('body').append(
                    '<div id="tipkk" style="top:' + offset.top + 'px; left:' + offset.left + 'px; ">' + title + '</div>');
                var tipContentHeight = jQuery('#tipkk')
                    .height() + 25;
                jQuery('#tipkk').css(
                    'top', (offset.top - tipContentHeight) + 'px');

            }, function () {
                jQuery('#tipkk').remove();
            });
            
            var mailin_api_status = $('input:radio[name=mailin_api_status]:checked').val();
					
					if(mailin_api_status==0)
					{ 
						$('.apikey').hide();
					}else{ 
						$('.apikey').show();
					}	
					
				$('.mailin_api_status').click(function (){

					var mailin_api_status = jQuery(this).val();

					if (mailin_api_status == 0){
					
						$('.apikey').hide();
					}else{
						$('.apikey').show();
					}
				});
  });            
</script>
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
		</ul>
		<?php esc_html_e('Monitor your transactional emails (purchase confirmation, password reset …) with a better deliverability and real-time analytics', 'mailin_i18n' ); ?><br><br>
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
	<legend><img  src="<?php echo plugins_url(); ?>/mailin/img/logo.gif"> <?php esc_html_e('Prerequisites', 'mailin_i18n' ); ?></legend>
<label"> <?php esc_html_e('- You should have a Mailinblue account. You can create a free account here:', 'mailin_i18n' ); ?> <a target="_blank" href="<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?>">&nbsp;<?php esc_html_e('http://www.mailinblue.com/', 'mailin_i18n' ); ?></a><br></label"></fieldset>
</div>


<?php  
$checked1 = '';
$checked2 = '';
	 $mailin_apikey_status = get_option('mailin_apikey_status'); 
	
	if ($mailin_apikey_status == 1)
		$checked1 = 'checked="checked"';
	else
		$checked2 = 'checked="checked"';
		
?>

<div class="mailin_row">
<fieldset class="fields">
<legend><img  src="<?php echo plugins_url(); ?>/mailin/img/logo.gif"><?php esc_html_e('Settings', 'mailin_i18n' ); ?> </legend>

   <form method="post" action="options-general.php?page=mailin_options">
        
      <table class="form-table blog_table">
		  
			<tr>
			<td style="width:250px; text-align:right;">
			<label style="word-wrap:break-word; width:244px;">
			 <?php esc_html_e('Activate Mailinblue:', 'mailin_i18n' ); ?></label>
			</td>
			<td>
				<?php esc_html_e('Yes', 'mailin_i18n' ); ?><input type="radio" <?php echo $checked1; ?> size="32" value="1" class="mailin_api_status"  name="mailin_api_status">
				<?php esc_html_e('No', 'mailin_i18n' ); ?><input type="radio" <?php echo $checked2; ?> size="32" value="0" class="mailin_api_status"  name="mailin_api_status">
			</td>
			</tr>
         
         <tr valign="top" class="apikey">
            <td scope="row" style="width:250px; text-align:right;"><?php esc_html_e('API Key', 'mailin_i18n'); ?>:</td>
            <td>
               <input name="mailin_apikey" type="text" id="mailin_apikey" class="code" value="<?php echo esc_attr($api_key); ?>" size="32" />
               <span title="<?php esc_html_e('Please enter your API key from your Mailinblue account and if you donot have it yet, please go to www.mailinblue.com and subscribe. You can then get the API key from https://my.mailinblue.com/advanced/apikey', 'mailin_i18n' ); ?>" class="toolTip">&nbsp;</span>
            </td>
         </tr>
         <tr>
            <td colspan="2">
               <input type="hidden" name="mailin_form_action" value="apikey_update"/>
               <input type="submit" style="float: left; margin-left:260px;" name="Submit" value="<?php esc_attr_e('Validate & Save' , 'mailin_i18n');?>" class="button" />
            </td>
         </tr>
      </table>
   </form>
</fieldset>
</div>
