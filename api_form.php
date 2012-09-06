<div class="mailin_row">
    <form method="post" action="options-general.php?page=mailin_options">
        <h3>
            <?php esc_html_e( 'Please provide your login information' ,
            'mailin_i18n');?>
        </h3>
        <?php esc_html_e('To start using the Mailin plugin, please enter your Mailin API Key below.', 'mailin_i18n'); ?>
            <br/>
            <?php
               echo esc_html_e( "Don't have a Mailin account?", "mailin_i18n").'&nbsp;';
               echo '<a href="http://www.mailin.fr/#subscriptionform" target="_blank">'.__( "Sign up", "mailin_i18n"). '</a>';
            ?>
                <br/>
                <table class="form-table">
                    <tr valign="top">
                        <td scope="row" width="50">
                            <?php esc_html_e( 'API Key', 'mailin_i18n'); ?>:</td>
                        <td>
                            <input name="mailin_apikey" type="text" id="mailin_apikey" class="code"
                            value="<?php echo esc_attr($api_key); ?>" size="32" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="mailin_form_action" value="apikey_update" />
                            <input type="submit" name="Submit" value="<?php esc_attr_e('Validate & Save' , 'mailin_i18n');?>"
                            class="button" />
                        </td>
                    </tr>
                </table>
    </form>
</div>
