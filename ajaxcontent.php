<?php
$_SERVER['HTTP_HOST'] = 'wp_trunk';
$wp_load = '../../../wp-load.php';
if (!is_file($wp_load))
    exit;
require_once($wp_load);
require_once(ABSPATH.'wp-admin/includes/admin.php');
if (!class_exists('MailinApi'))
    require_once('mailinapi.class.php');
$m_obj = new MailinApi();

if (isset($_POST['page']))
{
    if ($_POST['token'] != md5(get_option('mailin_apikey')))
        die('Invalid token');
    if ($_POST['language'] == 'fr-FR')
    {
        $title1 = 'Inscrire le contact';
        $title2 = 'Désinscrire le contact';
        $first = 'Première page';
        $last = 'Dernière page';
        $previous = 'Précédente';
        $next = 'Suivante';
        $y = 'Oui';
        $n = 'Non';
    } else
    {
        $title1 = 'Unsubscribe the contact';
        $title2 = 'Subscribe the contact';
        $first = 'First';
        $last = 'Last';
        $previous = 'Previous';
        $next = 'Next';
        $y = 'Yes';
        $n = 'No';
    }
    $page = (int)$_POST['page'];
    $cur_page = $page;
    $page -= 1;
    $per_page = 20;
    $previous_btn = true;
    $next_btn = true;
    $first_btn = true;
    $last_btn = true;
    $start = $page * $per_page;
    $count = $m_obj->getTotalUsers();
    $count = $count[0]->total;
    $no_of_paginations = ceil($count / $per_page);
    if ($cur_page >= 7)
    {
        $start_loop = $cur_page - 3;
        if ($no_of_paginations > $cur_page + 3)
            $end_loop = $cur_page + 3;
        else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6)
        {
            $start_loop = $no_of_paginations - 6;
            $end_loop   = $no_of_paginations;
        } else
            $end_loop = $no_of_paginations;
    } else
    {
        $start_loop = 1;
        if ($no_of_paginations > 7)
            $end_loop = 7;
        else
            $end_loop = $no_of_paginations;
    }
    $users = $m_obj->getAllUsersPagination($start, $per_page);
    $data = $m_obj->checkusermainStatus($users);
    $result = $data['result'];
    $msg = '';
    $i   = 1;
    foreach ($users as $subs)
    {
        if ($result[$subs->email] === 1 || $result[$subs->email] === null)
				$pstatus = 1;
        elseif ($result[$subs->email] === 0)
				$pstatus = 0;
		if ($subs->client == 1)
			$client = $y;
		elseif ($subs->client == 0)
			$client = $n;
        if ($subs->subscribed)
            $img = '<img src="'.plugins_url().'/mailin/img/enabled.gif" >';
        else
            $img = '<img src="'.plugins_url().'/mailin/img/disabled.gif" >';
        if (!$pstatus)
            $img1 = '<img src="'.plugins_url().'/mailin/img/enabled.gif" 
            id="ajax_contact_status_'.$i.'" title="'.$title1.'" 
            class="toolTip1 imgstatus">';
        else
            $img1 = '<img src="'.plugins_url().'/mailin/img/disabled.gif" 
					id="ajax_contact_status_'.$i.'" title="'.$title2.'" 
					class="toolTip1 imgstatus">';
        $msg .= '<tr><td>'.$i.'</td><td>'.$subs->fname.'</td><td>'.$subs->lname.'</td>
				<td>'.$subs->email.'</td><td>'.$client.'</td><td>'.$img.'</td>
				<td><a status="'.$pstatus.'" email="'.$subs->email.'" class="ajax_contacts_href" href="javascript:void(0)">
                    '.$img1.'
                    </a></td><td>'.date('d M Y H:i', strtotime($subs->create_date)).'</td></tr>';
        $i++;
    }
    $msg_paging = '';
    $msg_paging .= '<tr><td colspan="7"><div class="pagination"><ul class="pull-left">';
    if ($first_btn && $cur_page > 1)
        $msg_paging .= '<li p="1" class="active">'.$first.'</li>';
    else if ($first_btn)
        $msg_paging .= '<li p="1" class="inactive">'.$first.'</li>';
    if ($previous_btn && $cur_page > 1)
    {
        $pre = $cur_page - 1;
        $msg_paging .= '<li p="'.$pre.'" class="active">'.$previous.'</li>';
    } else if ($previous_btn)
        $msg_paging .= '<li class="inactive">'.$previous.'</li>';
    for ($i = $start_loop; $i <= $end_loop; $i++)
    {
        if ($cur_page == $i)
            $msg_paging .= '<li p="'.$i.'" style="color:#fff;background-color:#000000;" class="active">'.$i.'</li>';
        else
            $msg_paging .= '<li p="'.$i.'"  class="active">'.$i.'</li>';
    }
    if ($next_btn && $cur_page < $no_of_paginations)
    {
        $nex = $cur_page + 1;
        $msg_paging .= '<li p="'.$nex.'" class="active">'.$next.'</li>';
    } else if ($next_btn)
        $msg_paging .= '<li class="inactive">'.$next.'</li>';
    if ($last_btn && $cur_page < $no_of_paginations)
         $msg_paging .= '<li p="'.$no_of_paginations.'" class="active">'.$last.'</li>';
    else if ($last_btn)
        $msg_paging .= '<li p="'.$no_of_paginations.'" class="inactive">'.$last.'</li>';
    if ($count != 0)
        echo $msg.$msg_paging.'</td></tr>';
}
