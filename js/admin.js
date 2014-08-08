function change_list_val(list_id)
{
    var data = {
        action:'sib_change_list',
        list_id:list_id
    }
    jQuery('#sib_select_list').attr('disabled', 'true');
    jQuery.post(ajax_object.ajax_url, data, function(respond){
        jQuery('#sib_total_contacts').html(respond);
        var base_url = jQuery('#sib_list_link').attr('data-url');
        jQuery('#sib_list_link').attr('href', base_url + list_id);
        jQuery('#sib_select_list').removeAttr('disabled');
    });
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
    return pattern.test(emailAddress);
}

function change_field_attr()
{
    var attr_val = jQuery('#sib_sel_attribute').val();

    // get all info of attr
    var attr_type = jQuery('#sib_hidden_' + attr_val).attr('data-type');
    var attr_name = jQuery('#sib_hidden_' + attr_val).attr('data-name');
    var attr_text = jQuery('#sib_hidden_' + attr_val).attr('data-text');

    // generate attribute html
    generate_attribute_html(attr_type, attr_name, attr_text);
}

function change_attribute_tag(attr_type, attr_name, attr_text)
{
    jQuery('#sib_field_add_area').show();
    jQuery('#sib_field_html_area').show();
    jQuery('#sib_field_label').attr('value', attr_text);
    jQuery('#sib_field_placeholder').attr('value', '');
    jQuery('#sib_field_initial').attr('value', '');
    jQuery('#sib_field_button_text').attr('value', attr_text);
    jQuery('#sib_field_wrap').attr('checked', 'true');
    switch(attr_type)
    {
        case 'email':
            jQuery('#sib_field_label_area').show();
            jQuery('#sib_field_placeholder_area').show();
            jQuery('#sib_field_initial_area').show();
            jQuery('#sib_field_button_text_area').hide();
            jQuery('#sib_field_wrap_area').show();
            jQuery('#sib_field_required').attr('checked', 'true');
            jQuery('#sib_field_required_area').show();
            break;
        case 'text':
            jQuery('#sib_field_label_area').show();
            jQuery('#sib_field_placeholder_area').show();
            jQuery('#sib_field_initial_area').show();
            jQuery('#sib_field_button_text_area').hide();
            jQuery('#sib_field_wrap_area').show();
            jQuery('#sib_field_required').removeAttr('checked');
            jQuery('#sib_field_required_area').show();
            break;
        case 'float':
            jQuery('#sib_field_label_area').show();
            jQuery('#sib_field_placeholder_area').show();
            jQuery('#sib_field_initial_area').show();
            jQuery('#sib_field_button_text_area').hide();
            jQuery('#sib_field_wrap_area').show();
            jQuery('#sib_field_required').removeAttr('checked');
            jQuery('#sib_field_required_area').show();
            break;
        case 'submit':
            jQuery('#sib_field_label_area').hide();
            jQuery('#sib_field_placeholder_area').hide();
            jQuery('#sib_field_initial_area').hide();
            jQuery('#sib_field_button_text_area').show();
            jQuery('#sib_field_wrap_area').show();
            jQuery('#sib_field_required').removeAttr('checked');
            jQuery('#sib_field_required_area').hide();
            break;
    }
}

// generate attribute html
function generate_attribute_html(attr_type, attr_name, attr_text)
{
    var field_label = jQuery('#sib_field_label').val();
    var field_placeholder = jQuery('#sib_field_placeholder').val();
    var field_initial = jQuery('#sib_field_initial').val();
    var field_buttontext = jQuery('#sib_field_button_text').val();
    var field_wrap = jQuery('#sib_field_wrap').is(':checked');
    var field_required = jQuery('#sib_field_required').is(':checked');

    var field_html = '';

    if(field_wrap == true) {
        if(attr_type != 'submit') {
            field_html += '<p class="sib-' + attr_name + '-area"> \n';
        }
        else {
            field_html += '<p> \n';
        }
    }

    if((field_label != '') && (attr_type != 'submit')) {
        field_html += '    <label class="sib-' + attr_name + '-area">' + field_label + '</label> \n';
    }

    switch (attr_type)
    {
        case 'email':
            field_html += '    <input type="email" class="sib-' + attr_name + '-area" name="' + attr_name + '" ';
            field_html += 'placeholder="' + field_placeholder + '" ';
            field_html += 'value="' + field_initial + '" ';
            if(field_required == true) {
                field_html += 'required="required" ';
            }
            field_html += '> \n';
            break;
        case 'text':
            field_html += '    <input type="text" class="sib-' + attr_name + '-area" name="' + attr_name + '" ';
            if(field_placeholder != '') {
                field_html += 'placeholder="' + field_placeholder + '" ';
            }
            if(field_initial != '') {
                field_html += 'value="' + field_initial + '" ';
            }
            if(field_required == true) {
                field_html += 'required="required" ';
            }
            field_html += '> \n';
            break;
        case 'float':
            field_html += '    <input type="text" class="sib-' + attr_name + '-area" name="' + attr_name + '" ';
            if(field_placeholder != '') {
                field_html += 'placeholder="' + field_placeholder + '" ';
            }
            if(field_initial != '') {
                field_html += 'value="' + field_initial + '" ';
            }
            if(field_required == true) {
                field_html += 'required="required" ';
            }
            field_html += 'pattern="[0-9]+([\\.|,][0-9]+)?" > \n';
            break;
        case 'submit':
            field_html += '    <input type="submit" name="' + attr_name + '" ';
            field_html += 'value="' + field_buttontext + '" ';
            field_html += '> \n';
            break;
    }

    if(field_wrap == true) {
        field_html += '</p> \n';
    }

    jQuery('#sib_field_html').html(field_html);
}

jQuery(document).ready(function(){
    //jQuery("[data-toggle='tooltip']").tooltip();
    jQuery('.popover-help-form').popover({
    });
    jQuery('.sib-spin').hide();
    jQuery('body').click(function(e) {
        if(!jQuery(e.target).hasClass('popover-help-form')) {
            jQuery('.popover-help-form').popover('hide');
        }
    });

    jQuery('#sib_field_label_area').hide();
    jQuery('#sib_field_placeholder_area').hide();
    jQuery('#sib_field_initial_area').hide();
    jQuery('#sib_field_button_text_area').hide();
    jQuery('#sib_field_wrap_area').hide();
    jQuery('#sib_field_required_area').hide();
    jQuery('#sib_field_add_area').hide();
    jQuery('#sib_field_html_area').hide();

    // validate button click process in welcome page
    jQuery('#sib_validate_btn').click(function(){
        var access_key = jQuery('#sib_access_key').val();
        var secret_key = jQuery('#sib_secret_key').val();

        // check validation
        var error_flag = 0;
        if(access_key == '') {
            jQuery('#sib_access_key').addClass('error');
            error_flag =1;
        }
        if(secret_key == '') {
            jQuery('#sib_secret_key').addClass('error');
            error_flag =1;
        }

        if(error_flag != 0) {
            return false;
        }

        // ajax process for validate
        var data = {
            action:'sib_validate_process',
            access_key: access_key,
            secret_key: secret_key
        }

        jQuery('#failure-alert').hide();
        jQuery('.sib-spin').show();
        jQuery('#sib_access_key').removeClass('error');
        jQuery('#sib_secret_key').removeClass('error');
        jQuery(this).attr('disabled', 'true');

        jQuery.post(ajax_object.ajax_url, data, function(respond) {
            jQuery('.sib-spin').hide();
            jQuery('#sib_validate_btn').removeAttr('disabled');
            if(respond == 'success') {
                jQuery('#success-alert').show();
                var cur_url = jQuery('#cur_refer_url').val();
                window.location.href = cur_url;
            } else {
                jQuery('#sib_access_key').addClass('error');
                jQuery('#sib_secret_key').addClass('error');
                jQuery('#failure-alert').show();
            }
        });
    });

    jQuery('#sib_access_key').keypress(function(){
        jQuery(this).removeClass('error');
    });

    jQuery('#sib_secret_key').keypress(function(){
        jQuery(this).removeClass('error');
    });

    jQuery('#activate_email_radio_yes').click(function(){
        var data = {
            action: 'sib_activate_email_change',
            option_val: 'yes'
        }

        jQuery.post(ajax_object.ajax_url, data, function(respond) {
            jQuery('#email_send_field').show();
        });

        return true;
    });

    jQuery('#activate_email_radio_no').click(function(){
        var data = {
            action: 'sib_activate_email_change',
            option_val: 'no'
        }

        jQuery.post(ajax_object.ajax_url, data, function(respond) {
            jQuery('#email_send_field').hide();
        });


        return true;
    });

    // send activate email button

    jQuery('#send_email_btn').click(function(){
        var email = jQuery('#activate_email').val();
        if(email == '' || isValidEmailAddress(email) != true) {
            jQuery('#activate_email').removeClass('has-success');
            jQuery('#activate_email').addClass('error');
            jQuery('#failure-alert').show();
            return false;
        }
        jQuery(this).attr('disabled', 'true');

        var data = {
            action:'sib_send_email',
            email:email
        }

        jQuery('#failure-alert').hide();
        jQuery('#success-alert').hide();
        jQuery('#activate_email').removeClass('error');
        jQuery('.sib-spin').show();
        jQuery.post(ajax_object.ajax_url, data,function(respond) {
            jQuery('.sib-spin').hide();
            jQuery('#send_email_btn').removeAttr('disabled');
            if(respond != 'success') {
                jQuery('#activate_email').removeClass('has-success');
                jQuery('#activate_email').addClass('error');
                jQuery('#failure-alert').show();
            } else {
                jQuery('#success-alert').show();
            }
        });
    });

    jQuery('#activate_email').keypress(function(){
        jQuery('#activate_email').removeClass('error');
        var email = jQuery('#activate_email').val();
        if(isValidEmailAddress(email) == true) {
            jQuery('#activate_email').addClass('has-success');
        }
    });

    jQuery('#sib_sel_attribute').change(function() {
        var attr_val = jQuery(this).val();

        // get all info of attr
        var attr_type = jQuery('#sib_hidden_' + attr_val).attr('data-type');
        var attr_name = jQuery('#sib_hidden_' + attr_val).attr('data-name');
        var attr_text = jQuery('#sib_hidden_' + attr_val).attr('data-text');

        // change attribute tags
        change_attribute_tag(attr_type, attr_name, attr_text);

        // generate attribute html
        generate_attribute_html(attr_type, attr_name, attr_text);
    });

    jQuery('#sib_field_wrap').change(function() {
        change_field_attr();

    });
    jQuery('#sib_field_required').change(function() {
        change_field_attr();
    });
    jQuery('#sib_field_label').change(function() {
        change_field_attr();
    });
    jQuery('#sib_field_placeholder').change(function() {
        change_field_attr();
    });
    jQuery('#sib_field_initial').change(function() {
        change_field_attr();
    });
    jQuery('#sib_field_button_text').change(function() {
        change_field_attr();
    });

    jQuery('#sib_add_to_form_btn').click(function() {
        var field_html = jQuery('#sib_field_html').val();
        var formContent = jQuery("#sibformmarkup");
        formContent.val(formContent.val() + "\n" + field_html);
        return false;
    });

    /* read more click */
    jQuery('#home-read-more-link').click(function(e) {
        jQuery('.home-read-more-content').show();
        jQuery(this).hide();
        return false;
    });

    /* when change template id, auto select sender id  */
    jQuery('#sib_template_id').change(function() {
        var temp_id = jQuery(this).val();

        if(temp_id == '-1') {
            jQuery('#sib_sender_id').val('-1');
            return;
        }

        var data = {
            action: 'sib_change_template',
            template_id: temp_id
        }
        jQuery('#sib_sender_id').attr('disabled', 'true');
        jQuery.post(ajax_object.ajax_url, data,function(respond) {
            jQuery('#sib_sender_id').val(respond);
            jQuery('#sib_sender_id').removeAttr('disabled');
        });


    });

    // check confirm email
    var is_send_confirm_email = jQuery("input[type='radio'][name='is_confirm_email']:checked").val();

    if(is_send_confirm_email == 'yes') {
        jQuery('#sib_confirm_template_area').show();
        jQuery('#sib_confirm_sender_area').show();
        jQuery('#sib_confirm_sender_area_select').html(jQuery('#sib_sender_id'));
    } else {
        jQuery('#sib_confirm_template_area').hide();
        jQuery('#sib_confirm_sender_area').hide();
    }

    // check double optin
    var is_double_optin = jQuery("input[type='radio'][name='is_double_optin']:checked").val();

    if(is_double_optin == 'yes') {
        jQuery('#is_confirm_email_no').prop("checked", true);
        jQuery('#sib_confirm_template_area').hide();
        jQuery('#sib_confirm_sender_area').hide();
        jQuery('#sib_double_sender_area_select').html(jQuery('#sib_sender_id'));
        jQuery('#sib_double_sender_area').show();
    } else {
        jQuery('#sib_double_sender_area').hide();
        jQuery('#sib_double_redirect_area').hide();
    }

    // click confirm email
    jQuery("input[type='radio'][name='is_confirm_email']").click(function() {
        var confirm_email = jQuery("input[type='radio'][name='is_confirm_email']:checked").val();

        if(confirm_email == 'yes') {
            jQuery('#is_double_optin_no').prop("checked", true);
            jQuery('#sib_confirm_sender_area_select').html(jQuery('#sib_sender_id'));
            jQuery('#sib_double_sender_area').hide();
            jQuery('#sib_double_redirect_area').hide();
            jQuery('#sib_confirm_template_area').show();
            jQuery('#sib_confirm_sender_area').show();
        } else {
            jQuery('#sib_confirm_template_area').hide();
            jQuery('#sib_confirm_sender_area').hide();
        }
    });

    // click double optin
    jQuery("input[type='radio'][name='is_double_optin']").click(function() {
        var double_optin = jQuery("input[type='radio'][name='is_double_optin']:checked").val();

        if(double_optin == 'yes') {
            jQuery('#is_confirm_email_no').prop("checked", true);
            jQuery('#sib_confirm_template_area').hide();
            jQuery('#sib_confirm_sender_area').hide();
            jQuery('#sib_double_sender_area_select').html(jQuery('#sib_sender_id'));
            jQuery('#sib_double_sender_area').show();
            jQuery('#sib_double_redirect_area').show();
        } else {
            jQuery('#sib_double_sender_area').hide();
            jQuery('#sib_double_redirect_area').hide();
        }
    });

    // click redirect url
    jQuery('#is_redirect_url_click_yes').click(function() {
        jQuery('#sib_subscrition_redirect_area').show();
    });
    jQuery('#is_redirect_url_click_no').click(function() {
        jQuery('#sib_subscrition_redirect_area').hide();
    });
});