<div class="mailin_row">
    <table class="mailin_admin_head">
        <tbody>
            <tr>
                <td>
                    <h3 style="margin:0">
                        <?php echo esc_html_e('You are logged in', 'mailin_i18n'); ?>
                    </h3>
                </td>
                <td align="right">
                    <form action="options-general.php?page=mailin_options" method="post">
                        <input type="hidden" value="logout" name="mailin_form_action">
                        <input type="submit" class="button" value="<?php echo esc_html_e('Logout', 'mailin_i18n'); ?>"
                        name="Submit">
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mailin_row">
    <h3>
        <?php esc_html_e('Your Lists', 'mailin_i18n'); ?>
    </h3>
</div>
<form action="options-general.php?page=mailin_options" method="post">
    <div class="mailin_row">
        <p>
            <?php echo esc_html_e("Please check lists in which users will be added", 'mailin_i18n'). '&nbsp;'; ?>
        </p>
    </div>
    <div class="mailin_row">
        <table class="mailin_row">
            <tbody>
                <tr>
                    <td>
                        <input type="hidden" value="update_list" name="mailin_form_action">
                        <table class="widefat" width="600">
                            <thead style="float:left;width:100%">
                                <tr>
                                    <th width="20">&nbsp;</th>
                                    <th width="400">
                                        <?php echo esc_html_e( 'List Name', 'mailin_i18n'); ?>
                                    </th>
                                    <th width="94">
                                        <?php echo esc_html_e( 'Users', 'mailin_i18n'); ?>
                                    </th>
                                    <th width="90">
                                        <?php echo esc_html_e( 'Blacklisted', 'mailin_i18n'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody style="height:200px;overflow-y:scroll;float:left;width:100%">
                                <?php
                                $mailin_lists=get_option('mailin_lists');

                                if(!is_array($mailin_lists)){
                                    $mailin_lists= unserialize($mailin_lists);
                                } if(!empty($mailin_lists)){
                                    $selected_list= get_option( 'mailin_list_selected');
                                    $selected_list= explode('|', $selected_list);

                                    $i=1 ;

                                    foreach($mailin_lists as $item){
                                       $selected='' ;
                                       if($selected_list !='' && in_array($item->id, $selected_list)){
                                         $selected = 'checked="checked"';
                                         } ?>

                                          <tr id="mailin_list_row_<?php echo $item->id ?>">
                                            <td width="20">
                                                <input type="checkbox" <?php echo $selected ?>name="mailin_list[]" id="mailin_list" value="
                                                <?php echo $item->id ?>" ></td>
                                            <td width="400">
                                                <?php echo $item->name?></td>
                                            <td width="90">
                                                <?php echo $item->count?></td>
                                            <td width="90">
                                                <?php echo $item->blacklisted?></td>
                                        </tr>
                                        <?php $i++;
                                    } ?>
                                <?php
                                } ?>

                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mailin_row" style="float:left;padding-top:5px">
        <div style="float:left;">
            (<?php echo esc_html_e("If you want to keep the users in selected list please click on the button Update List", 'mailin_i18n'); ?>)</div>
        <div style="float:right;">
            <input type="submit" class="button" value="<?php esc_html_e('Update List', 'mailin_i18n'); ?>"
            name="Submit">
        </div>
    </div>
</form>

<div class="mailin_row">
    <h3>
        <?php esc_html_e( 'Your Campaigns', 'mailin_i18n'); ?>
    </h3>
</div>
<form action="options-general.php?page=mailin_options" method="post">
    <div class="mailin_row">
        <div style="float:left;width:auto;">
            <p>
                <?php echo esc_html_e( "Following are your campaigns on Mailin",
                'mailin_i18n'). '&nbsp;'; ?>
            </p>
        </div>
    </div>
    <?php
    $mailin_campaigns=get_option( 'mailin_campaigns'); if(!is_array($mailin_campaigns)){
    $mailin_campaigns=unserialize($mailin_campaigns); } if(!empty($mailin_campaigns)){
    ?>
        <div class="mailin_row">
            <table class="mailin_row">
                <tbody>
                    <tr>
                        <td>
                            <input type="hidden" value="update_campaigns" name="mailin_form_action">
                            <table class="widefat" width="600">
                                <thead style="float:left;width:100%">
                                    <tr>
                                        <th width="50">
                                            <?php echo esc_html_e( 'No.', 'mailin_i18n'); ?>
                                        </th>
                                        <th width="350">
                                            <?php echo esc_html_e( 'Campaign Name', 'mailin_i18n'); ?>
                                        </th>
                                        <th width="70">
                                            <?php echo esc_html_e( 'Sent', 'mailin_i18n'); ?>
                                        </th>
                                        <th width="70">
                                            <?php echo esc_html_e( 'Delivered', 'mailin_i18n'); ?>
                                        </th>
                                        <th width="70">
                                            <?php echo esc_html_e( 'Remaining', 'mailin_i18n'); ?>
                                        </th>
                                        <th width="90">
                                            <?php echo esc_html_e( 'No of lists', 'mailin_i18n'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody style="height:200px;overflow-y:scroll;float:left;width:100%">
                                    <?php $i=1 ; foreach($mailin_campaigns as $item){ ?>
                                        <tr id="mailin_list_row_<?php echo $item->id ?>">
                                            <td width="50">
                                                <?php echo $i ?>
                                            </td>
                                            <td width="350">
                                                <?php echo $item->name != '' ? $item->name : ''?></td>
                                            <th width="70">
                                                <?php echo $item->sent?></th>
                                            <th width="70">
                                                <?php echo $item->delivered?></th>
                                            <th width="70">
                                                <?php echo $item->remaining?></th>
                                            <td width="95">
                                                <?php echo count($item->listid)?></td>
                                        </tr>
                                        <?php $i++; } ?>
                                            <?php ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="mailin_row">
                <?php }else{ ?>
                    <div style="float: left; width: 655px; padding: 5px; background-color:#FFF0FF; border: 1px solid #FF6860;">
                        <?php esc_html_e(
                        'You have no campaigns on Mailin.fr, please update campaigns if you have added any campaign recently.', 'mailin_i18n'); ?>
                    </div>
                    <?php } ?>
</form>
<div class="mailin_row">
    <h3>
        <?php esc_html_e( 'Subscribers', 'mailin_i18n'); ?>
    </h3>
</div>
<div class="mailin_row">
    <p>
        <?php echo esc_html_e("Following users have subscribed to Mailin newsletter", 'mailin_i18n'). '&nbsp;'; ?>
    </p>
</div>
<?php $mObj=new mailin_API; $users=$mObj->getAllSubscribers(); if(!empty($users)) { ?>
    <div class="mailin_row">
        <table class="widefat">
            <thead style="float:left;width:100%">
                <tr>
                    <th width="40">
                        <?php echo esc_html_e( 'No.', 'mailin_i18n'); ?>
                    </th>
                    <th width="150">
                        <?php echo esc_html_e( 'Username', 'mailin_i18n'); ?>
                    </th>
                    <th width="300">
                        <?php echo esc_html_e( 'Email', 'mailin_i18n'); ?>
                    </th>
                    <th width="122">
                        <?php echo esc_html_e( 'Subscription date', 'mailin_i18n'); ?>
                    </th>
                </tr>
            </thead>
            <tbody style="height:200px;overflow-y:scroll;float:left;width:100%">
                <?php if(!empty($users)){ $i=1 ; foreach($users as $subs){ ?>
                    <tr>
                        <td width="40">
                            <?php echo $i?>
                        </td>
                        <td width="150">
                            <?php echo $subs->fname.' '.$subs->lname?></td>
                        <td width="300">
                            <?php echo $subs->email?></td>
                        <td width="122">
                            <?php echo date( 'd M Y H:i' , strtotime($subs->create_date))?></td>
                    </tr>
                    <?php $i++; } }?>
            </tbody>
        </table>
        <?php }else{ ?>
            <div style="float: left; width: 655px; padding: 5px; background-color:#FFF0FF; border: 1px solid #FF6860;">
                <?php esc_html_e( 'No users have subscribed mailin newsletter yet.',
                'mailin_i18n'); ?>
            </div>
            <?php } ?>
                <div style="float:left;padding-top:5px" class="mailin_row">
                    <div style="float:left;">(<?php

                        $cron_link = MAILIN_URL.'cron.php';

                        $printable_link = "<a target='_blank' href ='".$cron_link."'>".$cron_link."</a>";

                        $text =  translate('To synchronize the emails id of your  subscribers from Mailin plateform to your e-commerce website, you should add this link {cron_link} as a cron job (using for example: cpanel or crontab) and this link should be executed each day. You can also synchronize the subscribers manually by clicking on the button Synchronize Users', 'mailin_i18n');

                        echo str_replace('{cron_link}', $printable_link , $text);

                        ?>)</div>
                    <div style="float:right;">
                        <form name="" action="" method="post">
                            <input type="submit" name="Submit" value="<?php echo esc_html_e('Synchronize Users', 'mailin_i18n');?>"
                            class="button">
                            <input type="hidden" name="mailin_form_action" value="sync_users">
                        </form>
                    </div>
                </div>
    </div>
