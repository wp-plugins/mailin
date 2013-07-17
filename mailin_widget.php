<?php

/*
Description: This is mailin widget which is working with mailin wordpress plugin . from this widget you can manage title of the block displayed in front office . you can also manage to display Prenom and Nom.
Author: deshbandhu
Version: 1
Author URI: http://mailin.fr/
*/


/**
 * Adds Foo_Widget widget.
 */

function getMailinSubscriptionForm($arg)
{

$api_key = get_option('mailin_apikey');

$mailin_apikey_status = get_option('mailin_apikey_status');

$mailin_manage_subscribe = get_option('mailin_manage_subscribe');

$mailin_unsubscribe = get_option('mailin_unsubscribe');

if ($api_key == false || $mailin_apikey_status == 0 || $mailin_manage_subscribe == 0)
return false;
   if (get_option('mailin_list_selected') != '')
   {
?>
<div id="mailin_signup_box">
	<form id="mailin_signup_form" action="#mailin_signup_box" method="post">
		<input type="hidden" value="subscribe_form_submit"	name="mailin_form_action">
		<div class="mailin_signup_box_inside">
		<div class="mailin_signup_box_row">
		<span class="mailin_widget_head"> <?php	esc_html_e($arg['title'], 'mailin_i18n');?></span></div>
		<?php
		if (mailinMessages() != '')
		{
			?>
			<div id="mailin_message">
			<?php
			echo mailinMessages();
			?>
			</div>
			<?php
		}
		?>

		<?php
		if ($arg['firstname'] == 1)
		{
		?>
			<div class="mailin_signup_box_row">
			<label><?php echo esc_html_e('First name', 'mailin_i18n'); ?><span class="required">*</span></label>
			<input type="text" id="fname" name="fname"	value="<?php echo isset($_POST['fname']) ? $_POST['fname'] : ''; ?>" size="21" maxlength="55">
			</div>
		<?php
		}
		?>

		<?php
		if ($arg['lastname'] == 2)
		{
		?>
			<div class="mailin_signup_box_row">
			<label><?php echo esc_html_e('Last name', 'mailin_i18n');?><span class="required">*</span>
			</label><input type="text" id="lname" name="lname"	value="<?php echo isset($_POST['lname']) ? $_POST['lname'] : ''; ?>" size="21" maxlength="55">
			</div>
		<?php }?>

			<div class="mailin_signup_box_row">
				<label><?php  echo esc_html_e('Email', 'mailin_i18n'); ?><span	class="required">*</span> </label> 
				<input type="text"	id="mailin_email" name="mailin_email" value="<?php  echo isset($_POST['mailin_email']) ? $_POST['mailin_email'] : ''; ?>" size="21">
			</div>
<?php
        if ($mailin_unsubscribe == 1)
        {
?>
			<div class="mailin_signup_box_row">
					<select name="action">
						<option value="1" <?php  if(isset($_POST['action']) && $_POST['action'] == 1 ) echo  'selected'; ?> >
						<?php
						echo esc_html_e('Subscribe', 'mailin_i18n');
						?>
					</option>
					<option value="2"
					<?php  if(isset($_POST['action']) && $_POST['action'] == 2) echo  'selected'; ?>>
					<?php
					echo esc_html_e('Unsubscribe', 'mailin_i18n');
					?>
					</option>
					</select>
			</div>
		<?php
        }
?>	
			

			<div class="mailin_signup_box_row right">
				<input type="submit" class="button"	value="<?php  echo esc_html_e('Validate', 'mailin_i18n'); ?>" id="mailin_signup_submit" name="mailin_signup_submit"></div>
		</div>
	</form>
</div>


<?php
    }
}


class Mailin_Widget extends WP_Widget
{

	/**
	* Register widget with WordPress.
	*/
	public function __construct()
	{
		parent::__construct('mailin_widget', // Base ID
		'Mailin_Widget', // Name
		array(
		'description' => __('Mailin Widget', 'mailin_i18n')
		) // Args
		);
	}

/**
* Front-end display of widget.
*
* @see WP_Widget::widget()
*
* @param array $args     Widget arguments.
* @param array $instance Saved values from database.
*/
	public function widget($args, $instance)
	{
		extract($args);
		if (!is_array($instance))
			$instance = array();
		getMailinSubscriptionForm(array_merge($args, $instance));
	}
	public function form($instance)
	{
		if (isset($instance['title']) && !empty($instance['title']))
			$title = $instance['title'];
		else
			$title = 'Subscribe to newsletter';
		if (isset($instance['firstname']) && $instance['firstname'] == 1)
			$fchecked = 'checked';
		else if (!isset($instance['firstname']))
			$fchecked = 'checked';
		if (isset($instance['lastname']) && $instance['lastname'] == 2)
			$lchecked = 'checked';
		else if (!isset($instance['lastname']))
			$lchecked = 'checked';
		

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>" >
		<?php _e('Title:'); ?></label>
		<input id="<?php  echo $this->get_field_id('title'); ?>" 
		name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php   echo $title; ?>" /></p>

		<p><input <?php echo $fchecked; ?> 
		id="<?php echo $this->get_field_id('firstname'); ?>" 
		name="<?php echo $this->get_field_name('firstname'); ?>" type="checkbox" value="1" />
		<label for="<?php echo $this->get_field_id('firstname'); ?>">
		<?php _e('Display first name input', 'mailin_i18n'); ?></label></p>

		<p>	<input  <?php echo $lchecked; ?> 
		id="<?php echo $this->get_field_id('lastname'); ?>" 
		name="<?php  echo $this->get_field_name('lastname'); ?>" type="checkbox" value="2" /> 
		<label for="<?php  echo $this->get_field_id('lastname'); ?>">
		<?php   _e('Display last name input', 'mailin_i18n'); ?></label></p>

		<?php
	}
	public function update($new_instance, $old_instance)
	{
		$instance['title']          = strip_tags($new_instance['title']);
		$instance['firstname']      = strip_tags($new_instance['firstname']);
		$instance['lastname']       = strip_tags($new_instance['lastname']);
		
		return $instance;
	}
}
	function mailinRegisterWidgets()
	{
		register_widget('Mailin_Widget');
	}

	add_action('widgets_init', 'mailinRegisterWidgets');

?>
