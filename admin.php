<?php

//https://test.simplesolutions.dk/?Start/my-newsletter
//https://test.simplesolutions.dk/?Start/my_newsletter


/*
============================================================
    Newsletter for CMSimple
============================================================
Copyright:  © simpleSolutions 2010-2012
Website:    simplesolutions.dk
Released under Remove Link License and Commercial License. Please read file TermsOfUse.txt included in distribution package. 
Visit simplesolutions.dk for user manual and to buy a licence.
*/

if (isset($_GET['newsletter'])) {
  global $hjs, $tx, $sl, $plugin_tx;
  
  if (!isset($_SESSION['NEWSLETTER']['MailSession'])) {
    //session_start();
    $_SESSION['NEWSLETTER']['MailSession'] = "ok";
  }
  
  $admin = isset($_POST['admin']) ? $_POST['admin'] : (isset($_GET['admin'])?$_GET['admin']:"");
  $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action'])?$_GET['action']:"");
  $plugin = basename(dirname(__file__), "/");
  $o='<h1>Newsletter</h1>Newsletter plugin &copy; <a href="http://simplesolutions.dk">simpleSolutions</a>';
  if($admin == '') {
	   $o.='<p><strong>License:</strong> This CMSimple XH plugin was developed in hope that you find it useful. You can use it as it is or with any code changes to suit your needs. However, you may not (re)distribute neither original or modified source code and remove the link to simplesolutions.dk\'s website.</a></p><p>To remove the link you must buy Linkware License on <a href="https://simplesolutions.dk">simplesolutions.dk\'s</a> website.</p>';	
	}     
  $newsletter_adminmail = isset($_POST['adminmail']) ? $_POST['adminmail'] : (isset($_GET['adminmail'])?$_GET['adminmail']:"");//$_GET['adminmail'];
  $newsletter_subject = isset($_POST['subject']) ? $_POST['subject'] : (isset($_GET['subject'])?$_GET['subject']:"");//$_GET['subject'];
  $newspage = isset($_POST['nlp']) ? $_POST['nlp'] : (isset($_GET['nlp'])?$_GET['nlp']:"");//$_GET['nlp'];
  $newsletter_submit = isset($_POST['submit']) ? $_POST['submit'] : (isset($_GET['submit'])?$_GET['submit']:"");//$_GET['submit'];
  $newsletter_test = isset($_POST['test']) ? $_POST['test'] : (isset($_GET['test'])?$_GET['test']:"");//$_GET['test'];
  $newsletter_template = isset($_POST['template']) ? $_POST['template'] :  (isset($_GET['template'])?$_GET['template']:"");//$_GET['template'];
  $newsletter_attachment = isset($_POST['attachment']) ? $_POST['attachment'] : (isset($_GET['attachment'])?$_GET['attachment']:"");//$_GET['attachment'];
  $restart = (isset($_POST['restart']) ? $_POST['restart'] : (isset($_SESSION['NEWSLETTER']['MailCount'])? $_SESSION['NEWSLETTER']['MailCount']: null));
    
  
    
  // admin menu
  $newsletter_t = print_plugin_admin('on');
  if ($admin <> 'plugin_main') {
    $newsletter_t .= plugin_admin_common($action, $admin, $plugin);
    //$newsletter_t .= '<p>CMSimple plugin: Newsletter &copy; <a href="https://simplesolutions.dk">simpleSolutions</a></p>';
  	if ($admin==null) {
  		if(!is_writable($pth['folder']['plugins'].$plugin."/config/config.php")) {
  			$newsletter_t .="<br>Warning : config.php not writable, please change permissions<br>";
  		}		
  		if(!is_writable($pth['folder']['plugins'].$plugin."/css/stylesheet.css")) {
  			$newsletter_t .="<br>Warning : stylesheet.css not writable, please change permissions<br>";
  		}
  		if(!is_writable($pth['folder']['plugins'].$plugin."/languages/".$sl.".php")) {
  			$newsletter_t .="<br>Warning : ".$sl.".php not writable, please change permissions<br>";
  		}
  		$newsletter_t .= $plugin_tx['newsletter']['terms_of_use'];
  	}
  }
  
  if ($newspage=="") {
  	newsletter_mailinglist($c, $h, $l, $newspage, ""); //preset $newspage (activ mailing list)
  }
  else {
  	$newspage=str_replace("_"," ",$newspage);
  }
  if ($admin == 'plugin_main') {
    $newsletter_t.='<table class="edit" width="100%" cellpadding="1" cellspacing="0" border="1"><tr>';
    $newsletter_t.='<td><a href="'.$sn.'?'.$plugin.'&amp;admin=plugin_main'.'&amp;action=publish'.'&amp;nlp='.rswu($newspage).'">';
  	$newsletter_t .= $plugin_tx['newsletter']['publish'].'</a></td>';
  	$newsletter_t .= '<td><a href="'.$sn.'?'.$plugin.'&amp;admin=plugin_main'.'&amp;action=subscribers'.'&amp;nlp=';
    $newsletter_t .= rswu($newspage) . '">' . $plugin_tx['newsletter']['admin_subscribers'];
    $newsletter_t.='</a></td><td>  <a href="'.$sn.'?'.$plugin.'&amp;admin=plugin_main'.'&amp;action=template'.'&amp;nlp=';
    $newsletter_t .= rswu($newspage) . '">' . $plugin_tx['newsletter']['admin_template'];
    if ($plugin_cf['newsletter']['mail_confirm_subscribtion']!="no" || 	$plugin_cf['newsletter']['mail_confirm_unsubscribtion']!="no"){
    	$newsletter_t.='</a></td><td>  <a href="'.$sn.'?'.$plugin.'&amp;admin=plugin_main'.'&amp;action=confirm'.'&amp;nlp=';
      $newsletter_t .= rswu($newspage) . '">' . $plugin_tx['newsletter']['admin_confirmation_template'];
  	}    
    $newsletter_t.='</a></td><td>  <a href="'.$sn.'?'.$plugin.'&amp;admin=plugin_main'.'&amp;action=log'.'&amp;nlp=';
    $newsletter_t .= rswu($newspage) . '">' . $plugin_tx['newsletter']['admin_log'];
    $newsletter_t .= '</a></td></tr></table>'.'Newsletter for CMSimple &copy; <a href="https://simplesolutions.dk">simpleSolutions</a>';
    $newsletter_t.=newsletter_template_file($pth,$plugin,$sl,$newspage,$newsletter_template_file);
    $template=file_get_contents($newsletter_template_file);
    $template=preg_replace("/<.*\/.*body.*>/i",newsletter_license("html")."</body>",$template,1);
  	$newsletter_t .= '<h4>' . ucfirst(str_replace("_", " ", $plugin)) . '</h4>';		
    if ($action == 'publish') {
      if (trim($newsletter_adminmail) == "")
        $newsletter_adminmail = ($plugin_cf['newsletter']['adminmail'] == "") ? $cf['mailform']['email'] :
          $plugin_cf['newsletter']['adminmail'];
      if (trim($newsletter_subject) == "")
        $newsletter_subject = $plugin_tx['newsletter']['newsletter_subject'];
  
      $newsletter_t .= newsletter_mailinglist($c, $h, $l, $newspage, $action); // list of mailing list
      $newsletter_s = array_search($newspage, $h);
      //$newsletter_body = explode($plugin_cf['newsletter']['separator'], preg_replace("/" .
      //  chr(35) . "CMSimple.*" . chr(35) . "/Uis", "", preg_replace("/<h[1-5]>(.+?)<\/h[1-5]>/i",
      //  "", $c[$newsletter_s])));
      
       $newsletter_body = explode($plugin_cf['newsletter']['separator'], preg_replace("/" .
        chr(35) . "CMSimple.*" . chr(35) . "/Uis", "", preg_replace("/<h[1-".$cf['menu']['levels']."]>(.+?)<\/h[1-".$cf['menu']['levels']."]>/i", "", $c[$newsletter_s])));
  
  		$newsletter_unreadable="";
  		if (trim($plugin_tx['newsletter']['unreadable'])!="") {
  			$newsletter_unreadable='<a href="'.newsletter_headpath($h, $l, $newspage).'">'.$plugin_tx['newsletter']['unreadable'].'</a>';
  		}
  		if (stristr($plugin_cf['newsletter']['editor_relative_urls'],"true")) {
  			$newsletter_body[0]=preg_replace('#(href|src)="([^:"]*)("|(?:(\?)[^"]*"))#','$1="'.(isset($_SERVER['HTTPS']) ? "https" : "http").'://'.$_SERVER['HTTP_HOST'].'/$2$3',$newsletter_body[0]);	//replace relative urls with absolute (absolute urls stay unchanged version 2.3.1)	
  		}
  		$template=preg_replace('/\{NOT_READABLE\}/',$newsletter_unreadable,$template);
  		$template=preg_replace('/\{CONTENT\}/',$newsletter_body[0],$template);
  		$template=preg_replace('/\{UNSUBSCRIBE\}/','<a href="'.$_SESSION['subscribe_page'].'">'.$plugin_tx['newsletter']['footer_unsubscribe'].'</a>',$template);
  		preg_match('/<body[^>]*>(.*?)<\/body>/si', $template, $regs);
  		$template_preview = $regs[1];
  		
  		$template_preview_send = $template;
  		if (trim($plugin_tx['newsletter']['subscriber_fields_label'])!="") {
  			$labels=explode(";",$plugin_tx['newsletter']['subscriber_fields_label']);
  			for ($i=0; $i<sizeof($labels); $i++) {
     			$template_preview=preg_replace('/\{TEXTFIELD_'.($i+1).'\}/','['.trim($labels[$i]).']',$template_preview);
     			$template_preview_send=preg_replace('/\{TEXTFIELD_'.($i+1).'\}/','['.trim($labels[$i]).']',$template_preview_send);
  			}
  		}
  		// preview or test submitted
      if ($newsletter_submit == '' || $newsletter_test != '') {
      	$checked=' checked="checked"';
      	if ($restart>0) $checked=""; 
        $newsletter_t .= 
  			'<form name="newsmail" action="'.$sn.'?'.$plugin.'&amp;admin=plugin_main&amp;action=publish&amp;submit=sendmail&amp;nlp='.rswu($newspage).'" method="post">' 
  			.'<table border="0">'
  			.'<tr><td width="200px">'
  			.'<a href="#" class="pl_tooltip"><img src = "'.(isset($pth['folder']['corestyle'])?$pth['folder']['corestyle']:$pth['folder']['plugins'].'pluginloader/css/').'help_icon.png" alt="" class="helpicon" /> &nbsp;<span>'.$plugin_tx[$plugin]['adm_newsletter_subject'].'</span></a>'
  			.$plugin_tx['newsletter']['subject'].'</td>'
  			.'<td>'.tag('input name="subject" type="text" size="50%" value="' . $newsletter_subject .'"').'</td></tr>' 
  			.'<tr><td>'
  			.'<a href="#" class="pl_tooltip"><img src = "'.(isset($pth['folder']['corestyle'])?$pth['folder']['corestyle']:$pth['folder']['plugins'].'pluginloader/css/').'help_icon.png" alt="" class="helpicon" /> &nbsp;<span>'.$plugin_tx[$plugin]['adm_newsletter_attachment'].'</span></a>'
  			.$plugin_tx['newsletter']['attachment'].':&nbsp;</td>'
  			.'<td>'.newsletter_attachmentslist($newsletter_attachment).'</td></tr>';
  			$newsletter_t .='<tr><td>'
  				.'<a href="#" class="pl_tooltip"><img src = "'.(isset($pth['folder']['corestyle'])?$pth['folder']['corestyle']:$pth['folder']['plugins'].'pluginloader/css/').'help_icon.png" alt="" class="helpicon" /> &nbsp;<span>'.$plugin_tx[$plugin]['adm_newsletter_restore'].'</span></a>'
  				.$plugin_tx['newsletter']['restart'].': </td><td>'.tag('input name="restart" type="text" size="5" value="'.$restart.'" onchange="if (document.newsmail.restart.value.search(/^\d*$/)==-1) { document.newsmail.restart.style.backgroundColor='.'\'#FFE9E8\''.'; alert('.'\'Number only, please.\''.'); } else {document.newsmail.restart.style.backgroundColor='.'\'#FFFFFF\''.';{if (document.newsmail.restart.value>0) {document.newsmail.test.checked=false;} else {document.newsmail.test.checked=true;}}}"')
  			 .'</td></tr>';
  			$newsletter_t .='<tr><td>'
  			.'<a href="#" class="pl_tooltip"><img src = "'.(isset($pth['folder']['corestyle'])?$pth['folder']['corestyle']:$pth['folder']['plugins'].'pluginloader/css/').'help_icon.png" alt="" class="helpicon" /> &nbsp;<span>'.$plugin_tx[$plugin]['adm_newsletter_sendto'].'</span></a>'
  			.tag('input type="checkbox" name="test"'.$checked).'&nbsp;'. $plugin_tx['newsletter']['test_mail'].'</td>'
  				.'<td>'.tag('input name="adminmail" type="text" size="50%" value="'.$newsletter_adminmail.'"').'</td></tr>';
  			$newsletter_t .='<tr><td colspan="2">'.tag("br").tag('input type="submit" class="submit" value="'.$plugin_tx['newsletter']['publish'].'"').'</td></tr>'
  			.'<tr><td colspan="2">'.tag("br").'</td></tr>'
  			.'</table>'.'</form>';
      }
      $_SESSION['NEWSLETTER']['MailFrom'] = (trim($plugin_cf['newsletter']['from']) == "") ? $cf['mailform']['email'] : $plugin_cf['newsletter']['from'];
      
      $_SESSION['NEWSLETTER']['MailFromName'] = (trim($plugin_cf['newsletter']['from_name']) == "")? $tx['site']['title']:$plugin_cf['newsletter']['from_name'];
     
      $_SESSION['NEWSLETTER']['MailAltBody'] = $plugin_tx['newsletter']['alt_body'].newsletter_headpath($h, $l, $newspage)."\r\n".$plugin_tx['newsletter']['alt_footer']."\r\n\r\n".$plugin_tx['newsletter']['alt_unsubscribe'].$_SESSION['subscribe_page']."\r\n".newsletter_license("text");
      $_SESSION['NEWSLETTER']['MailHTMLBody'] = $template;
      if ($newsletter_submit != '') { // send email to all subscribers or test email to administrator
        // Init phpmailer
        include_once ('phpmailer/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->SMTPDebug = $plugin_cf['newsletter']['debug'];
        $mail->SetLanguage($cf['language']['default'],'./plugins/newsletter/phpmailer/language/');
        if (trim($plugin_cf['newsletter']['smtp']) != "") {
          $mail->IsSMTP(); // telling the class to use SMTP
          $mail->Host = $plugin_cf['newsletter']['smtp']; // SMTP server
          if (stristr($plugin_cf['newsletter']['smtp_auth'],"true")) {
            $mail->SMTPAuth=true;
            $mail->Username=$plugin_cf['newsletter']['smtp_auth_username'];
            $mail->Password=$plugin_cf['newsletter']['smtp_auth_password'];
          }
        } 
  			else {
          $mail->IsMail(); // telling the class to use mail
    		}
        $mail->IsHTML(true);
        $mail->SingleTo=true;
        if (trim($plugin_tx['newsletter']['charset'])!="") {
        	$mail->CharSet=$plugin_tx['newsletter']['charset'];
       	}
     		else {
  				if (isset($tx['meta']['codepage'])) 
  					$mail->CharSet=$tx['meta']['codepage'];
  			}
        $mail->From = $_SESSION['NEWSLETTER']['MailFrom'];
        $mail->FromName = $_SESSION['NEWSLETTER']['MailFromName'];
        $mail->AltBody = str_replace("uns=1","uns=".newsleter_convert($newsletter_adminmail,1), $_SESSION['NEWSLETTER']['MailAltBody']);
        
        // END Init phpmailer
        $reciver_list = tag("br");
        if ($newsletter_test) { // send test mail to admin
        	
        	$mail->MsgHTML(str_replace("uns=1","uns=".newsleter_convert($newsletter_adminmail,1), $template_preview_send));
          $mail->AddAddress($newsletter_adminmail, $newsletter_adminmail);
          $mail->Subject=$newsletter_subject;
          if ($newsletter_attachment != "no") 
            $mail->AddAttachment($pth['folder']['downloads'].($plugin_cf['newsletter']['attachment_folder']==""?"":$plugin_cf['newsletter']['attachment_folder']."/").$newsletter_attachment);
  
          $mail->SMTPKeepAlive = false;
          if (!$mail->Send())
            $newsletter_t .= $plugin_tx['newsletter']['test_mail_nosent']."&nbsp;" . $newsletter_adminmail . " (" .
              date($plugin_tx['newsletter']['date_format']) . ")".$plugin_tx['newsletter']['msg_errorcount'].": " . $mail->error_count . "&nbsp ".$plugin_tx['newsletter']['msg_error'].": (" .
              $mail->ErrorInfo . ")";
          else
            $newsletter_t .= $plugin_tx['newsletter']['test_mail_sent']."&nbsp;" . $newsletter_adminmail . " (" . date($plugin_tx['newsletter']['date_format']) .
              ")";
  				//$newsletter_sbp=$_SESSION['subscribe_page'];   
  				//session_destroy();
  				unset($_SESSION['NEWSLETTER']);
  				//$_SESSION['subscribe_page']=$newsletter_sbp;   		// restore 
        } // end test mail
  			else { // send all
          $location_str = (isset($_SERVER['HTTPS']) ? "https" : "http")."://".$_SERVER['HTTP_HOST'].$sn."?newsletter&amp;submit=sendmail&amp;admin=plugin_main&amp;action=publish&amp;nlp=".rswu($newspage); 
          if (!isset($_SESSION['NEWSLETTER']['MailCount'])) {
            $hjs .= tag('meta http-equiv="refresh" content="2;url='.$location_str.'"');
          	$_SESSION['NEWSLETTER']['MailCount'] = 0;
            $_SESSION['NEWSLETTER']['MailingList'] = $pth['folder']['plugins']."newsletter/data/subscribe_".rswu($newspage).".txt";
            newsletter_copy_logfile($pth['folder']['plugins']."newsletter/data/log_",rswu($newspage));
            $_SESSION['NEWSLETTER']['LogFile'] = $pth['folder']['plugins']."newsletter/data/log_".rswu($newspage).".txt";
            $_SESSION['NEWSLETTER']['MailSendResult'] = "";
            $_SESSION['NEWSLETTER']['MailSendErrors'] = 0;
            $_SESSION['NEWSLETTER']['MailSubject'] = $newsletter_subject;
  					$_SESSION['NEWSLETTER']['MailAttachment']=$newsletter_attachment;  
            $fh = fopen($_SESSION['NEWSLETTER']['LogFile'],"w");
            fwrite($fh, "***********************************************\nNewsletter (".$newspage.") ".date($plugin_tx['newsletter']['date_format'])."\n***********************************************\n" );
            fclose($fh);
  					if ($restart != "") { $_SESSION['NEWSLETTER']['MailCount']=(int)$restart; } 
  					$newsletter_t .= $plugin_tx['newsletter']['sendinit'].'&nbsp;'.tag('img src="'.$pth['folder']['plugins'].$plugin.'/images/loadingAnimation.gif"');
          } 
  				else {
  					$mail->Subject = $_SESSION['NEWSLETTER']['MailSubject'];
        		if ($_SESSION['NEWSLETTER']['MailAttachment'] != "no") 
          		$mail->AddAttachment($pth['folder']['downloads'].($plugin_cf['newsletter']['attachment_folder']==""?"":$plugin_cf['newsletter']['attachment_folder']."/").$_SESSION['NEWSLETTER']['MailAttachment']);
            $mail->SMTPKeepAlive = true;
            $newsletter_fc = file($_SESSION['NEWSLETTER']['MailingList']);
  
            $start_time = time(); 
            $fh = fopen($_SESSION['NEWSLETTER']['LogFile'],"a");            
            for ($i = (int)$_SESSION['NEWSLETTER']['MailCount']; $i < count($newsletter_fc); $i++) {
              if (trim($newsletter_fc[$i]) != '') {
  //$newsletter_t .= "[$i]  ". $newsletter_fc[$i].tag("br");                  	
            		$tmp=explode("@@@",$newsletter_fc[$i]);
            		$mail->AddAddress($tmp[0], $tmp[2]);
            		$template=$_SESSION['NEWSLETTER']['MailHTMLBody'];
            		for ($ri=1;$ri<sizeof($tmp);$ri++) { 
            			$template=preg_replace('/\{TEXTFIELD_'.($ri).'\}/',$tmp[$ri],$template);
           			}
           			$template=preg_replace('/\{TEXTFIELD_.*\}/',"",$template);  // remove unused text fields
                $mail->AltBody = str_replace("uns=1","uns=".newsleter_convert($tmp[0],1), $_SESSION['NEWSLETTER']['MailAltBody']);
        				$mail->MsgHTML(str_replace("uns=1","uns=".newsleter_convert($tmp[0],1), $template)); //insert unsubscribe link
        				
                if (!$mail->Send()) {
                  $mailsendresult=$plugin_tx['newsletter']['log_error']."&nbsp;".rmanl($newsletter_fc[$i])." (".date($plugin_tx['newsletter']['date_format']).") Error: ".$mail->error_count."&nbsp;".$plugin_tx['newsletter']['msg_error'].":&nbsp;(" .$mail->ErrorInfo.")";
                  ++$_SESSION['NEWSLETTER']['MailSendErrors'];
                } 
  			 				else {
                  $mailsendresult=$plugin_tx['newsletter']['log_success']." ".rmanl($newsletter_fc[$i])." (".date($plugin_tx['newsletter']['date_format']).")";
                  //$mail->ClearAddresses(); 
                }
                ++$_SESSION['NEWSLETTER']['MailCount'];
                $mailsendresult="[".$_SESSION['NEWSLETTER']['MailCount']."] ".$mailsendresult;
                fwrite($fh,$mailsendresult."\n");
                $_SESSION['NEWSLETTER']['MailSendResult'].=$mailsendresult.tag("br"); 
                //sleep(1);
                if ($_SESSION['NEWSLETTER']['MailCount'] == count($newsletter_fc)) {
                  $mail->SmtpClose();
                  fclose($fh);
                  $newsletter_t .= $plugin_tx['newsletter']['msg_sent'].":&nbsp;" . ($_SESSION['NEWSLETTER']['MailCount']) ."&nbsp;". $plugin_tx['newsletter']['msg_errorcount'].":&nbsp;" . $_SESSION['NEWSLETTER']['MailSendErrors'] .
                    tag("br");
                  //log
                  $newsletter_t .= $_SESSION['NEWSLETTER']['MailSendResult'];
  								unset($_SESSION['NEWSLETTER']); 
                } 
  							else if ((time() - $start_time) > ((int)$plugin_cf['newsletter']['max_execution_time']-1)) {
                  $hjs .= tag('meta http-equiv="refresh" content="'.$plugin_cf['newsletter']['max_execution_time'].';url='. $location_str.'"');
                  $mail->SmtpClose();
                  fclose($fh);
                  $i = count($newsletter_fc); //stop loop
                  $newsletter_t .= $plugin_tx['newsletter']['msg_sent'].":&nbsp;".($_SESSION['NEWSLETTER']['MailCount'])."&nbsp;".$plugin_tx['newsletter']['msg_of']."&nbsp;".
  									count($newsletter_fc).tag('br').$plugin_tx['newsletter']['msg_wait'].'&nbsp;'.tag('img src="'.$pth['folder']['plugins'].$plugin.'/images/loadingAnimation.gif"');
                }
  							$mail->ClearAddresses(); 
              }
              else
              	++$_SESSION['NEWSLETTER']['MailCount']; // do not send to empty mail adress, just update counter 	
            }
          }
        }
      } // END - SUBMIT
      if ($newsletter_submit == '' || $newsletter_test != '') {
        $newsletter_t .= 
  			'<table border="0">'
  			.'<tr><td colspan="2" class="newsletter_code">'.str_replace("uns=1",("uns=".newsleter_convert($newsletter_adminmail,1)), $template_preview /*$newsletter_body[0]*/).'</td></tr>'
  			.'</table>';
      }
    } // END - ACTION: publish
    else {
  		switch ($action) {
  			case 'subscribers': 
  				$newsletter_t .= newsletter_adminSubscribers($c, $h, $l, $newsletter_submit, $newspage, $action);
  				break;
  			case 'log': 
  				$newsletter_t .= newsletter_adminLog($c, $h, $l, $newsletter_submit, $newspage, $action);
  				break;
  			case 'template': 
  				$newsletter_t .= newsletter_adminTemplate($c, $h, $l, $newsletter_submit, $newspage, $newsletter_template_file, $action);
  				break;
  			case 'confirm': 
  				$newsletter_t .= confirmatinon_adminTemplate($c, $h, $l, $newsletter_submit, $newspage, $action);
  				break;
  			default:
  				$newsletter_t .= "";
  				break;
  		}
  	} 
  } // END - ADMIN
  $o .= $newsletter_t;
  $o.="\n<!--Newsletter plugin end-->\n";
}
// END - newsletter

function newsletter_mailinglist($c, $h, $l, &$newspage, $action)
{
  global $plugin, $n, $pth, $cf, $plugin_tx, $plugin_cf, $l, $cl, $pd_router, $sn;
 
 	$t="";
	if (!isset($_SESSION['NEWSLETTER']['NewsletterPath']) || (trim($_SESSION['NEWSLETTER']['NewsletterPath'])=="")) {
  	$ph=explode("?",$_SERVER["HTTP_REFERER"]);
		$_SESSION['NEWSLETTER']['NewsletterPath']=$ph[0];
	}
	$subscribe_pages = array();
	$count=0;
  foreach ($c as $i) {
  	$page[$l[$count]]=$h[$count];
		preg_match('/#cmsimple\s+.*newsletter\(["|\'].*["|\']\).*#/i',$i,$mres);
		preg_match('/newsletter\(["|\'].*["|\']\)/',$mres[0],$mres);
		if (trim($mres[0]=="")) { /// try {{{PLUGIN:....}}} notification
			preg_match('/{{{[PLUGIN:]*newsletter\(["|\'].*["|\']\).*}}}/i',$i,$mres);
			preg_match('/newsletter\(["|\'].*["|\']\)/',$mres[0],$mres);
      /*
			if (trim($mres[0]=="")) { //not found on the page, search hi_pd_scripting for newsletter calls
				if (isset($plugin_cf['hi_pd_scripting']['scripts_max_on_page']) && isset($pd_router) ) { // only if pd_scripting and page_param are installed search through pagedata.php            
					$page_pd = $pd_router -> find_page($count); // get page
					// search for newsletter in hi_pd_scripting fields in pageparam
					for ($pd_i = 1; ($pd_i <= $plugin_cf['hi_pd_scripting']['scripts_max_on_page']); $pd_i++) {
						if ($page_pd['hi_pd_scripting_active'.$pd_i] == '1') {  // activated
							preg_match('/newsletter\(["|\'].*["|\']\)/',trim($page_pd['hi_pd_scripting_code'.$pd_i]),$mres);
							if ($mres[0]!="")
								$pd_i=$plugin_cf['hi_pd_scripting']['scripts_max_on_page']; // found, destroy loop	
						}	
					}
				}
			}
      */
		}
		preg_match('/(["|\'].*["|\'])/',$mres[0],$mres);
		$temp=preg_replace('/["|\']/',"",$mres[0]);
		$newspage_arr= explode(",",$temp);
		foreach ($newspage_arr as $np) {
			if ((trim($np))!="") {
	    	$t.=trim($np).'@@@';
				array_push($subscribe_pages, newsletter_headpath($h, $l, $h[$count]));
			}
		}
    ++$count;
  }
  if (!isset($_SESSION['subscribe_page']))
  	$_SESSION['subscribe_page']=$subscribe_pages[0]."&uns=1";  //& -> text based e-mail can't handle &amp;

  $t = explode("@@@",$t);
  $newsletter_array = array();
  foreach ($t as $i) {
    if (strlen($i) > 0) {
      array_push($newsletter_array, $i);
    }
  }
  $newsletter_array = array_unique($newsletter_array);
  sort($newsletter_array);
  if ($newspage == '')
    $newspage = $newsletter_array[0];

  if (($action != "") && (count($newsletter_array)>1) ) {
    $newsletter_t = '<fieldset><legend>' . $plugin_tx['newsletter']['mailing_list'] .
      ': </legend>';
    $count=0;
    foreach ($newsletter_array as $i) {
      if ($i == $newspage) {
        $newsletter_t .= tag('img src="'.$pth['folder']['plugins'].$plugin.'/images/'.$plugin_cf['newsletter']['selected_img'].'" alt="selected" title="selected"').$i.tag("br");
        $_SESSION['subscribe_page']=$subscribe_pages[$count]."&uns=1"; //& -> text based e-mail can't handle &amp;
      }
      else {
        $newsletter_t .= tag('img src="'.$pth['folder']['plugins'].$plugin.
          '/images/transparent.png" alt=""').'<a href="'.$sn.'?'.$plugin.
          '&amp;admin=plugin_main&amp;action='.$action .'&amp;nlp='.rswu($i).'">'.$i.'</a>'.
          tag("br");
      }
    	++$count;    
    }
    $newsletter_t .= '</fieldset>';
    //$newsletter_t .= tag("hr");
    return $newsletter_t;
  } 
	else
    return "";
}

function newsletter_attachmentslist($attachment)
{ 
	global $plugin_cf, $pth, $plugin;
	
	//create attachment subfolder if necessary	
	if (!trim($plugin_cf['newsletter']['attachment_folder'])=="") {
		if (!file_exists($pth['folder']['downloads'].$plugin_cf['newsletter']['attachment_folder'])) {
			mkdir($pth['folder']['downloads'].$plugin_cf['newsletter']['attachment_folder']);	
		}
	}
	
	$att_list='<select name="attachment">'.
						'<option value="no">No attachment</option>';
	if ($handle = opendir($pth['folder']['downloads'].($plugin_cf['newsletter']['attachment_folder']==""?"":$plugin_cf['newsletter']['attachment_folder']."/"))) {
  	while (false !== ($file = readdir($handle))) {
  		if (!is_dir($pth['folder']['downloads'].($plugin_cf['newsletter']['attachment_folder']==""?"":$plugin_cf['newsletter']['attachment_folder']."/").$file))  
  			if ($attachment==$file) $att_list.='<option selected value="'.$file.'">'.$file.'</option>';
 				else $att_list.='<option value="'.$file.'">'.$file.'</option>';
  	}
	 	closedir($handle);
	}	
	$att_list.='</select>';
	return $att_list;
}				

function newsletter_adminSubscribers($c, $h, $l, $newsletter_submit, $newspage, $action)
{
  global $pth, $plugin, $plugin_cf, $plugin_tx, $tx, $sn;

  $newsletter_t = newsletter_mailinglist($c, $h, $l, $newspage, $action).tag('br');

  if ($newsletter_submit != '' and $fh = @fopen($pth['folder']['plugins']."newsletter/data/subscribe_".rswu($newspage).".txt", "w"))
  {
  	$subscribers=isset($_POST['subscribers']) ? $_POST['subscribers'] : $_GET['subscribers'];
    if (fwrite($fh, $subscribers) || $subscribers=="")
			$newsletter_t .= "<strong>File saved: ".$pth['folder']['plugins']."newsletter/data/subscribe_".rswu($newspage)."</strong><br />";
		else	
			$newsletter_t .= "Error, not saved: ";		
    fclose($fh);
  }
  $newsletter_t .= $plugin_tx['newsletter']['recivercount'].":&nbsp;".newsletter_subscribersCount($pth['folder']['plugins'] . "newsletter/data/subscribe_".rswu($newspage).".txt").tag("hr").'<form action="'.$sn.'?'.$plugin.'&amp;admin=plugin_main&amp;action=subscribers&amp;submit=save&amp;nlp='.rswu($newspage).'" method="post">' . tag('input type="submit" class="newsletter_submit" value="'.$tx['action']['save'].'"').
	'<textarea class="newsletter_textarea" name="subscribers">'.rmnl(file_get_contents($pth['folder']['plugins'] . "newsletter/data/subscribe_".rswu($newspage).".txt")) .
    '</textarea>&nbsp;'.tag('br') . tag('input type="hidden" name="news" value="'.$newspage.'"').'</form>';

  return $newsletter_t;
} // END - adminSubscribers

function newsletter_template_file($pth,$plugin,$sl,$newspage,&$newsletter_template_file){
		
	$errmsg = "";
	$newsletter_template_file=$pth['folder']['plugins'].$plugin."/templates/".$sl."_".rswu($newspage)."_template.htm";
	if (!file_exists($newsletter_template_file)) {
		$master=$pth['folder']['plugins'].$plugin."/templates/".$sl."_template.htm";
		if (!file_exists($master))
			$master=$master=$pth['folder']['plugins'].$plugin."/templates/template.htm";
		if (!copy($master,$newsletter_template_file)) {
			$newsletter_template_file=$pth['folder']['plugins'].$plugin."/templates/template.htm";
			$errmsg=tag('br')."Couldn't create template file, using default template instead.";
		}
		else 
			$errmsg=tag('br')."template: ".$newsletter_template_file." created.";
	}
	return $errmsg;
}

function confirmatinon_adminTemplate($c, $h, $l, $newsletter_submit, $newspage, $action)
{
  global $pth, $tx, $sl, $sn, $plugin;
  
	$newsletter_t = newsletter_mailinglist($c, $h, $l, $newspage, $action).tag('br'); 
  $msg = "<strong>File: </strong>";
	$confirmation_template_file=$pth['folder']['plugins'].$plugin."/templates/".$sl."_template_confirmation.htm";
	if (!file_exists($confirmation_template_file)) {
		$master=$pth['folder']['plugins'].$plugin."/templates/template_confirmation.htm";
		if (!copy($master,$confirmation_template_file)) {
			$confirmation_template_file=$master;
			$msg=tag('br')."<strong>Error: </strong>couldn't create confirmation template file. Using default template instead: ";
		}
		else 
			$msg=tag('br')."<strong>File created: </strong>";
	}
  if ($newsletter_submit != '' and $fh = @fopen($confirmation_template_file, "w")) {
		$template=isset($_POST['template']) ? $_POST['template'] : $_GET['template'];
		if (get_magic_quotes_gpc()) 
			$template = stripslashes($template);	
    if (fwrite($fh, $template))
			$msg = "<strong>File saved: </strong>";
		else	
			$msg = "<strong>Error: </strong> File not saved: ";		
    fclose($fh);
  }
  
  $newsletter_t .= $msg. $confirmation_template_file;
	$template=file_get_contents($confirmation_template_file);
  $newsletter_t .= tag("br").'<form action="'.$sn.'?'.$plugin.
	'&amp;admin=plugin_main&amp;action=confirm&amp;submit=save&amp;nlp='.
	rswu($newspage).'" method="post">' . tag('input type="submit" class="newsletter_submit" value="'.
	$tx['action']['save'].'"');
	if (function_exists('XH_hsc')) {
		$newsletter_t.='<textarea class="newsletter_textarea" name="template">'.XH_hsc($template).'</textarea>';
	}
	else {
		$newsletter_t.='<textarea class="newsletter_textarea" name="template">'.$template.'</textarea>';
	}
	$newsletter_t.=	tag('input type="hidden" name="news" value="'.$newspage.'"').'</form>';

  return $newsletter_t;
} // END - confirmatinon_adminTemplate

function newsletter_adminTemplate($c, $h, $l, $newsletter_submit, $newspage, $template_file, $action)
{
  global $pth, $tx, $sl, $sn, $plugin;
  
  $newsletter_t = newsletter_mailinglist($c, $h, $l, $newspage, $action).tag('br');
  $msg = "<strong>File: </strong>";
  if ($newsletter_submit != '' and $fh = @fopen($template_file, "w")) {
		$template=isset($_POST['template']) ? $_POST['template'] : $_GET['template'];
		if (get_magic_quotes_gpc()) 
			$template = stripslashes($template);	
    if (fwrite($fh, $template))
			$msg = "<strong>File saved: </strong>";
		else	
			$msg .= "<strong>Error: </strong> File not saved: ";		
    fclose($fh);
  }
  $newsletter_t .= $msg. $template_file;
	$template=file_get_contents($template_file);
  $newsletter_t .= tag("br").'<form action="'.$sn.'?'.$plugin.'&amp;admin=plugin_main&amp;action=template&amp;submit=save&amp;nlp='.
	rswu($newspage).'" method="post">' . tag('input type="submit" class="newsletter_submit" value="'.$tx['action']['save'].'"');
	if (function_exists('XH_hsc')) {
		$newsletter_t .='<textarea class="newsletter_textarea" name="template">'.XH_hsc($template).	'</textarea>';
	}
	else {
		$newsletter_t .='<textarea class="newsletter_textarea" name="template">'.$template.	'</textarea>';
	}
	$newsletter_t .=tag('input type="hidden" name="news" value="'.$newspage.'"').'</form></p>';

  return $newsletter_t;
} // END - adminTemplate

function newsletter_adminLog($c, $h, $l, $newsletter_submit, $newspage, $action)
{
  global $pth, $plugin, $plugin_cf, $plugin_tx, $tx, $sn;
  
  $newsletter_t = newsletter_mailinglist($c, $h, $l, $newspage, $action).tag('br');
	
	$fn=$pth['folder']['plugins']."newsletter/data/log_".rswu($newspage).".txt";
  
  if ($newsletter_submit != '' and $fh = @fopen($fn, "w"))
  {
  	$log=isset($_POST['log']) ? $_POST['log'] : $_GET['log'];
    if (fputs($fh, $log) || ($log==""))
    		$newsletter_t .= "<strong>File saved: ".$fn. " </strong>";
		else	
			$newsletter_t .= "error, not saved: ";		
    fclose($fh);
  }
  $newsletter_t .= tag("hr").'<form action="'.$sn.'?'.$plugin.'&amp;admin=plugin_main&amp;action=log&amp;submit=save&amp;nlp='.rswu($newspage).'" method="post">' . 
		tag('input type="submit" class="newsletter_submit" value="'.$tx['action']['save'].'"');
	if (function_exists('XH_hsc')) {
		$newsletter_t .= '<textarea class="newsletter_textarea" name="log">'.XH_hsc(rmnl(file_get_contents($fn))).'</textarea>';
	}
	else {
		$newsletter_t .= '<textarea class="newsletter_textarea" name="log">'.rmnl(file_get_contents($fn)).'</textarea>';
	}
	$newsletter_t .= tag('input type="hidden" name="news" value="'.$newspage.'"').'</form>';
  return $newsletter_t;
} // END - adminLog

function newsletter_subscribersCount($f)
{
	$count=0;	
	$fc=file($f);
	foreach($fc as $line) {
		++$count;
	}
	return $count;
}

function newsletter_headpath($h, $l, $page)
{
	global $cf;
	
 	$pagearray=array();
	$count=0;
	$tmp="";
  foreach ($h as $i) {
  	$pagearray[$l[$count]]=$h[$count];
		if ($i==$page) {
			for($n=1; $n<$l[$count]; $n++) {
				$tmp.=urlencode(preg_replace('/ /','_',$pagearray[$n])).$cf['uri']['seperator'];
			}
			return $_SESSION['NEWSLETTER']['NewsletterPath']."?".$tmp.urlencode(preg_replace('/ /',$cf['uri']['word_separator'],$page));
		}    
    ++$count;
  }
}

/*
function rewriteRelative($html, $base) {
// generate server-only replacement for root-relative URLs
	$server = preg_replace('@^([^\:]*)://([^/*]*)(/|$).*@', '\1://\2/', $base);
	// replace root-relative URLs
	$html = preg_replace('@\<([^>]*) (href|src)="/([^"]*)"@i', '<\1 \2="' . $server . '\3"', $html);
	// replace base-relative URLs (kludgy, but I couldn't get ! to work)
	$html = preg_replace('@\<([^>]*) (href|src)="(([^\:"])*|([^"]*:[^/"].*))"@i', '<\1 \2="' . $base . '\3"', $html);

	return $html;
}
*/
function newsletter_copy_logfile($logfile, $nln)
{
	$numofcopies = 3;
	$res=rename($logfile.$nln.".txt", $logfile.$nln."_".date('YmdHi').".txt");
	foreach (glob($logfile.$nln."_*.txt") as $filename) {
    $arr[]= $filename;
	}
	sort($arr);
	// delete excess copies
	if (count($arr) > $numofcopies) {
		for ($i=0; $i<count($arr)-$numofcopies; $i++) {
			unlink($arr[$i]);			
		}	
	}
}
if (function_exists('XH_registerStandardPluginMenuItems')) {
  XH_registerStandardPluginMenuItems(true);
}
?>