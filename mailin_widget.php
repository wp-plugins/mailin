<?php

/*
Plugin Name: Foo Widget
Plugin URI: http://jamesbruce.me/
Description: Random Post Widget grabs a random post and the associated thumbnail to display on your sidebar
Author: sachin
Version: 1
Author URI: http://jamesbruce.me/
*/


/**
 * Adds Foo_Widget widget.
*/

function getMailinSubscriptionForm(){

  if(get_option('mailin_list_selected') != '') {
  ?>
  <div id="mailin_signup_box">
    <form id="mailin_signup_form" action="#mailin_signup_box" method="post">
      <input type="hidden" value="subscribe_form_submit" name="mailin_form_action">
      <div class="mailin_signup_box_inside">

          <div class="mailin_signup_box_row">
            <span class="mailin_widget_head"><?php esc_html_e('Subscribe to newsletter', 'mailin_i18n'); ?></span>
          </div>

          <?php if(mailin_messages() != ''){ ?>
          <div id="mailin_message" >
                <?php echo mailin_messages(); ?>
          </div>
          <?php } ?>

          <div class="mailin_signup_box_row">
              <label ><?php echo esc_html_e('First name' , 'mailin_i18n')?><span class="required">*</span></label>
              <input type="text" id="fname" name="fname" value="<?php echo isset($_POST['fname']) ? $_POST['fname'] : '' ?>" size="21" maxlength="55">
          </div>

          <div class="mailin_signup_box_row">
              <label ><?php echo esc_html_e('Last name' , 'mailin_i18n')?><span class="required">*</span></label>
              <input type="text" id="lname" name="lname" value="<?php echo isset($_POST['lname']) ? $_POST['lname'] : '' ?>" size="21" maxlength="55">
          </div>

          <div class="mailin_signup_box_row">
              <label ><?php echo esc_html_e('Email' , 'mailin_i18n')?><span class="required">*</span></label>
              <input type="text" id="mailin_email" name="mailin_email" value="<?php echo isset($_POST['mailin_email']) ? $_POST['mailin_email'] : '' ?>" size="21">
          </div>

          <div class="mailin_signup_box_row right">
              <input type="submit" class="button" value="<?php echo esc_html_e('Subscribe' , 'mailin_i18n')?>" id="mailin_signup_submit" name="mailin_signup_submit">
          </div>
      </div>
    </form>
  </div>
  <?php
  }
}


class Mailin_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'mailin_widget', // Base ID
      'Mailin_Widget', // Name
      array('description' => __( 'Mailin Widget', 'mailin_i18n' ), ) // Args
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
  public function widget( $args, $instance ) {

    extract( $args );

    if (!is_array($instance)) {
      $instance = array();
    }
    getMailinSubscriptionForm(array_merge($args, $instance));
  }


} // class Foo_Widget

// register Foo_Widget widget

add_action( 'widgets_init',  create_function( '', 'register_widget( "Mailin_Widget" );' ));

?>
