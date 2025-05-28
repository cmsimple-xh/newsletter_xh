<?php
/*
============================================================
    Newsletter for CMSimple
============================================================
 * @copyright  © simpleSolutions 2010-2012
 * @author    simplesolutions.dk
 * @license Released under Remove Link License and Commercial License. Please read file TermsOfUse.txt included in distribution package. 
 * @license Visit simplesolutions.dk for user manual and to buy a licence.
 *
 * Message from Jerry Jakobsfeld (2025-01-09)
 * I stoped development of CMSimple plug-ins som time ago. Your welcome to use my development as you wish, it's free for use, changes and further development.
 *
 * @copyright 2025 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @license    GNU GPLv3 - http://www.gnu.org/licenses/gpl-3.0.en.html
*/

require_once($pth['folder']['plugins'] . 'newsletter/includes/nlfuncs.php');
//Spam protection
if ($plugin_cf['newsletter']['spam_protection'] == 'true') {
    require($pth['folder']['plugins'] . 'newsletter/includes/nlspamfuncs.php');
}

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_GET['newsletterconfirm'])) {
    $f = 'newsletterconfirm';
    $o .= newsletterConfirmSubscription();
}

$mail_confirm_subscribtion = $plugin_cf['newsletter']['mail_confirm_subscribtion'];
$subscribe_confirmation_mail = $plugin_tx['newsletter']['subscribe_confirmation_mail'];

function newsletter($newspage_list)
{
    global $plugin_tx, $plugin_cf, $cf, $sn, $su, $pth, $mail_confirm_subscribtion;
    //error_reporting (E_ALL ^ E_NOTICE);

    //Spam protection
    if (($plugin_cf['newsletter']['spam_protection'] == 'true')
    && $_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($_POST['subscribermail'])) {
        //Spam protection, Honeypot
        $honeypotCheck = newsletterFieldHoneypotCheck();
        if ($honeypotCheck) {
            return $honeypotCheck;
        }
        //Spam protection, Time
        $timeCheck = newsletterFieldSpamTimeCheck();
        if ($timeCheck) {
            return $timeCheck;
        }
    }

    $adddel=isset($_POST['adddel']) ? $_POST['adddel'] : (isset($_GET['adddel'])?$_GET['adddel']:"");
    $subscribermail=isset($_POST['subscribermail']) ? $_POST['subscribermail'] : (isset($_GET['subscribermail'])?$_GET['subscribermail']:"");//$_GET['subscribermail'];
    if (trim($plugin_tx['newsletter']['subscriber_fields_label'])!="") {
        $labels=explode(trim($plugin_tx['newsletter']['subscriber_fields_delimiter']),$plugin_tx['newsletter']['subscriber_fields_label']);
        for ($i=0; $i<sizeof($labels); $i++) {    
      $subscriberfield[$i]=isset($_POST[newsletterUnderline4Spaces(trim($labels[$i]))]) ? $_POST[newsletterUnderline4Spaces(trim($labels[$i]))] : (isset($_GET[newsletterUnderline4Spaces(trim($labels[$i]))])?$_GET[newsletterUnderline4Spaces(trim($labels[$i]))]:"");
        }
    }
    $newspages=array();
    $o='<!-- CMSimple_XH plugin Newsletter_XH --><fieldset class="newsleter_fieldset"><legend class="newsleter_legend">'.( (isset($_GET['uns']))?'&nbsp;':$plugin_tx['newsletter']['subscribe'].'/').$plugin_tx['newsletter']['unsubscribe'].'&nbsp;</legend>';
    if ($adddel!='') {
        if (isset($_POST['single_newspage'])) {
            $newspages[]=$_POST['single_newspage'];
        }
        else {
            if(isset($_POST['news_page']) && !empty($_POST['news_page'])) {
                foreach($_POST['news_page'] as $np) {
                    $newspages[]=$np;
                }
            }
        }
        if (count($newspages)>0) {
        //$plugin_cf['newsletter']['from_name']=(trim($plugin_cf['newsletter']['from_name'])=="")?$cf['site']['title']:$plugin_cf['newsletter']['from_name'];
            //$plugin_cf['newsletter']['from']=(trim($plugin_cf['newsletter']['from'])=="")?$cf['mailform']['email']:$plugin_cf['newsletter']['from'];
            if ($adddel==$plugin_tx['newsletter']['subscribe']) {    // add mail to subscriber list
                if (stristr($mail_confirm_subscribtion,"user")) {
                    $o.= newsletterConfirmation($newspages, $newspage_list, $subscribermail, $subscriberfield);
                }
                else {
                    $o.=newsletterAddSubscriberToList($newspages, $subscribermail, $subscriberfield);
                    $o.=newsletterCreateForm($newspage_list, $subscribermail, $subscriberfield, $newspages);
                }
            }    // end - else if
            else if ($adddel==$plugin_tx['newsletter']['unsubscribe']) {    // remove mail from subscriber list
                    $o.=newsletterRemoveSubscriber($newspages, $subscribermail, $subscriberfield);
                    $o.=newsletterCreateForm($newspage_list, $subscribermail, $subscriberfield, $newspages);
            }   
        }
        else {
            $o.='<p class="newsletter_subscribe_errmsg">'.$plugin_tx['newsletter']["error_newsletter_selected"].'</p>';
            $o.=newsletterCreateForm($newspage_list, $subscribermail, $subscriberfield, $newspages);
    }
    }    // end - if ($adddel!='')
    else {        // make subscribe/unsubscribe form
        $o.=newsletterCreateForm($newspage_list, $subscribermail, $subscriberfield, $newspages);
    }
    $o.='</fieldset><!--Newsletter-->';
    return $o;
}

function newsletterConfirmation($newspages, $newspage_list, $subscribermail, $subscriberfield) {

    global $plugin_tx, $subscribe_confirmation_mail;

    //echo "newsletter_confirmation";
    $crform = '';
    $subscribe = '';
    if (newsletterVerifyMail($subscribermail)
    && newsletterVerifyFields($subscriberfield)) {
        foreach($newspages as $np) {
            $subscriber_class = 'newsletter_subscribe_ok';
            //disable confirmation
            $subscribe_confirmation_mail = '';
            $subscribe .= '<p><span class="newsletter_name">'
                        . $np
                        . ':</span> </p><p>'
                        . $plugin_tx['newsletter']['subscribe_confirm_text']
                        . '</p>';
            $confirm_str = $subscribermail;
            for ($i = 0; $i < sizeof($subscriberfield); $i++) {
                $confirm_str .= "@@@" . ($subscriberfield[$i] == ''
                                            ? ' '
                                            : $subscriberfield[$i]);
            }
            $confirm_str .= '¤¤¤' . $np;
            //encrypt
            $confirm_str = newsletterConvert($confirm_str,1);
            $subscribe .= newsletterSubscriptionMail($subscribermail,
                                                     $plugin_tx['newsletter']['subscribe_confirm_subject'],
                                                     sprintf($plugin_tx['newsletter']['subscribe_confirm'], $np),
                                                     CMSIMPLE_URL . '?newsletterconfirm&cnf=' . $confirm_str);
                                                     //& -> text based e-mail can't handle &amp; // lck/Holger
        }
    } else {
        if (!newsletterVerifyMail($subscribermail))
            $subscribe = '<p>' . $plugin_tx['newsletter']['mail_not_valid'] . '</p>';
        if (!newsletterVerifyFields($subscriberfield)) {
            $subscribe.= '<p>'.$plugin_tx['newsletter']['subscriber_fields_mandatory'].'</p>';
        }
        $subscriber_class = 'newsletter_subscribe_errmsg';
        $crform .= newsletterCreateForm($newspage_list,
                                        $subscribermail,
                                        $subscriberfield,
                                        $newspages);
    }
    $subscribe = '<div class="' . $subscriber_class . '">' . $subscribe . '</div>' . $crform;

    return $subscribe;
}


function newsletterCreateForm($newspage_list, $subscribermail, $subscriberfield, $newspages) {

    global $plugin_cf, $plugin_tx, $sn, $su, $hjs, $onload;

    $ptx = $plugin_tx['newsletter'];

    $o = '';
    $onload .= 'getFocus()';
    $hjs .= '<script>
    /* <![CDATA[ */
    function getFocus() { 
      document.getElementById("subscribermail").focus();
    }
    function addLoadEvent(func) { // for version before CMSimple_XH 1.2
      var oldonload = window.onload;
      if (typeof window.onload != "function") {
        window.onload = func;
      } else {
        window.onload = function() {
          if (oldonload) {
            oldonload();
          }
          func();
        }
      }
    }
    addLoadEvent(function() {
        getFocus();
    });
    
        function hideFields(vfield) {
        if (vfield.selectedIndex == 1) { 
            document.getElementById(\'userinput\').style.display=\'none\';
        } 
        else {
            document.getElementById(\'userinput\').style.display=\'block\';
        }
    }

    function trim(stringToTrim) {
        return stringToTrim.replace(/^\s+|\s+$/g,"");
    }
    
    var busy=0;
    function newsletter_EmptyField(field) {
    if (busy) return;
         busy=1;
        if (trim(field.value)=="") {
             field.style.backgroundColor ="#FFAEAE";
            document.getElementById("err").innerHTML=\'' . $ptx['subscriber_fields_empty'].'\'; 
          field.focus();
          setTimeout("busy=0", 1);
          return true;
         }
         else {
             field.style.backgroundColor ="#FFFFFF";
            document.getElementById("err").innerHTML="&nbsp;";
          busy=0;
          return false;
        }  
    }

    function newsletter_ValidEmail(form){
        if (busy) return;
         busy=1;
      var validRegExp = /^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i;
      if (form.subscribermail.value.search(validRegExp) == -1 ) {
           document.getElementById("err").innerHTML="' . $ptx['subscriber_email_empty'].'";
             form.subscribermail.style.backgroundColor ="#FFAEAE";
             form.subscribermail.focus();
             form.subscribermail.select();
             setTimeout("busy=0", 1);
        return false;
      }
        form.subscribermail.style.backgroundColor ="#FFFFFF"; 
        document.getElementById(\'err\').innerHTML=\'&nbsp;\'; 
        busy=0;
    return true; 
    }
    /* ]]> */
</script>';
    $o .= "\n"
        . '<form name="subscribe" id="subscribe" method="post" action="'
        . $sn
        . '?'
        . $su
        . '" onsubmit="">'
        . "\n"
        . '<div id="err">&nbsp;</div>';
    if (isset($_GET['uns'])) {
        $o .= '<select name="adddel" id="addel" style="display: none;">'
            . "\n"
            . '<option>'
            . $ptx['subscribe']
            . '</option>'
            . "\n";
        $o .= '<option selected>'
            . $ptx['unsubscribe']
            . '</option>'
            . "\n"
            . '</select>'
            . "\n";
    } else {
        $o .= '<span class="newsletter_label">&nbsp;</span>'
            //. "\n"
            . '<select name="adddel" id="addel" onchange="hideFields(this);">'
            . "\n"
            . '<option>'
            . $ptx['subscribe']
            .'</option>'
            . "\n";
        $o .= '<option>'
            . "\n"
            . $ptx['unsubscribe']
            . '</option>'
            . "\n"
            . '</select>'
            . "\n";
    }

    $o .= '<br>'
        . '<span class="newsletter_label">'
        . $ptx['subscriber_email_label']
        . ':&nbsp;</span>'
        . '<input name="subscribermail" id="subscribermail" type="text" class="newsletter_inputfield" value="'
        . (isset($_GET['uns'])
            ? newsletterConvert($_GET['uns'], 0)
            : $subscribermail)
        . '" onblur="newsletter_ValidEmail(document.subscribe);">'
        . '<br>'
        . "\n";
    $mandatory = (trim($ptx['subscriber_fields_mandatory']) != '');
    if (!isset($_GET['uns'])
    && trim($ptx['subscriber_fields_label']) != '') {
        $labels = explode(trim($ptx['subscriber_fields_delimiter']),
                          $ptx['subscriber_fields_label']);
        $user_input = '';
        for ($i = 0; $i < sizeof($labels); $i++) {
            $user_input .= '<span class="newsletter_label">'
                         . trim($labels[$i])
                         . ':&nbsp;</span><input name="'
                         . newsletterUnderline4Spaces(trim($labels[$i]))
                         . '" id="'
                         . newsletterUnderline4Spaces(trim($labels[$i]))
                         . '" type="text" class="newsletter_inputfield" value="'
                         . $subscriberfield[$i]
                         . ($mandatory
                            ? '" onblur="newsletter_EmptyField(this);"'
                            : '"')
                         . '><br>'
                         . "\n";
        }
    }

    if (!isset($_GET['uns'])) {
        $o .= '<div id="userinput">';
        $o .= $user_input;
        $o .= '</div>';
    }    
    $o .= '<br>' . "\n";

    $newspage_arr = explode(',', $newspage_list);
    if (count($newspage_arr) > 1) {
        // multiple newsletter subscribe, create checkboxes
        $o .= ((isset($_GET['uns']))
                ? $ptx['select_newsletters_unsubscribe']
                : $ptx['select_newsletters_subscribe'])
            . '<br>';
        foreach ($newspage_arr as $np) {
            $checked = false;
            for ($i = 0; $i < sizeof($newspages); $i++) {
                if (newsletterUnderline4Spaces(trim($np)) == trim($newspages[$i])) {
                    $checked = true;
                }
            }
            $o .= '<br>'
                . "\n"
                . '<span class="newsletter_label">'
                . trim($np)
                . ':&nbsp;</span>'
                . '<input name="news_page[]" type="checkbox"'
                . ($checked
                    ? ' checked="yes"'
                    : '')
                . ' value="'
                . newsletterUnderline4Spaces(trim($np))
                . '">'
                . "\n";
        }
        $o .='<br>';
    } else {
        // singel newsletter subscribe
        $o .= '<input name="single_newspage" type="hidden" value="'
            . newsletterUnderline4Spaces($newspage_list)
            . '">'
            . '&nbsp;';
    }
    //Spam protection
    if ($plugin_cf['newsletter']['spam_protection'] == 'true') {
        //Spam protection, Honeypot
        $o .= newsletterFieldHoneypotDisplay();
        //Spam protection, Timecheck
        $o .= newsletterFieldSpamTimeDisplay();
    }
    // Submit button
    $o .= '<br>'
        . '<span class="newsletter_label"></span>'
        . "\n"
        . '<input type="submit" class="submit" value="'
        . $ptx['send']
        . '">'
        . "\n"
        . '</form>';

    return $o;
}

function newsletterAddSubscriberToList($newspages, $subscribermail, $subscriberfield) {

    global $plugin_tx, $pth, $mail_confirm_subscribtion, $su;

    $subscribe_msg = '';
    if (newsletterVerifyMail($subscribermail)
    &&  newsletterVerifyFields($subscriberfield)) {
        $subscriber_class = 'newsletter_subscribe_ok';
        foreach ($newspages as $np) {
            $fhandle = $pth['folder']['plugins']
                     . 'newsletter/data/subscribe_'
                     . newsletterUnderline4Spaces($np)
                     . '.txt';
            if (is_readable($fhandle)) {
                $fc = file($fhandle);
            } else {
                $fc = array();
            }
            if ($fh = fopen($fhandle, 'w')) {
                //$fc=array_unique($fc);
                foreach($fc as $line) {
                    if (stripos($line, $subscribermail) === false) {
                        // rewrite subscribers list except all instances of the subscriber mail
                        fwrite($fh, $line);
                    }
                }
                $subscriber_str = $subscribermail;
                if (trim($plugin_tx['newsletter']['subscriber_fields_label']) != '') {
                    for ($i = 0; $i < sizeof($subscriberfield); $i++) {
                        $subscriber_str .= '@@@'
                                         . ($subscriberfield[$i] == ''
                                            ? ' '
                                            : trim($subscriberfield[$i]));
                    }
                }
                if (fwrite($fh,$subscriber_str . "\r\n")) {
                    // write new subscriber
                    $subscribe_msg .= '<p><span class="newsletter_name">'
                                    . $np
                                    . ':</span></p><p>'
                                    . $plugin_tx['newsletter']['subscribe_succes']
                                    . '</p>';
                }  
                else {
                    $subscriber_class = 'newsletter_subscribe_errmsg';
                    $subscribe_msg .= '<p><span class="newsletter_name">'
                                    . $np
                                    . ':</span></p><p>'
                                    . $plugin_tx['newsletter']['subscribe_fail']
                                    . '</p>';
                    //destroy $mail_confirm_subscribtion
                    $mail_confirm_subscribtion = '';
                }
                fclose($fh);
            } else {
                $subscribe_msg .= '<p><span class="newsletter_name">'
                                . $np
                                . ':</span></p><p>'
                                . $plugin_tx['newsletter']['subscribe_fail']
                                . '</p>';
                $subscriber_class = 'newsletter_subscribe_errmsg';
            }
        }
            $mail_confirm_subscribtion = strtolower(trim($mail_confirm_subscribtion));
            switch ($mail_confirm_subscribtion) {
                //case 'yes':
                case 'mail':
                    $subscribe_msg .= newsletterSubscriptionMail($subscribermail,
                                                                 $plugin_tx['newsletter']['subscribe_succes_subject'],
                                                                 sprintf($plugin_tx['newsletter']['mail_subscribe_succes'],
                                                                         CMSIMPLE_URL . '?' . $su),
                                                                 '');
                    break;
                case 'user':
                    $subscribe_msg .= $plugin_tx['newsletter']['subscribe_confirm_text'];
                    break;
                case 'thx':
                    $subscribe_msg .= $plugin_tx['newsletter']['mail_subscribe_thx'];
                    break;
                default:
                    break;
            }
            $subscribe_msg = '<div class="'
                           . $subscriber_class
                           . '"><p><span class="newsletter_abo_for">'
                           . $plugin_tx['newsletter']["subscription_for"]
                           . '</span>'
                           . $subscribermail.
                           ' </p><p>'
                           . $subscribe_msg
                           . '</p></div>';
    } else { // user data error
        if (!newsletterVerifyMail($subscribermail)) {
            $subscribe_msg = $plugin_tx['newsletter']['mail_not_valid']
                           . '&nbsp;';
        }
        if (!newsletterVerifyFields($subscriberfield)) {
            $subscribe_msg .= $plugin_tx['newsletter']['subscriber_fields_mandatory'];
        }
        $subscriber_class = 'newsletter_subscribe_errmsg';
    }

    return $subscribe_msg;
}

function newsletterRemoveSubscriber($newspages, $subscribermail, $subscriberfield) {

    global $plugin_tx, $plugin_cf, $pth;

    $removed_msg = '<p><span class="newsletter_abo_for">'
                 . $plugin_tx['newsletter']["subscription_for"]
                 . '</span>'
                 . $subscribermail
                 . '</p>';
    if (newsletterVerifyMail($subscribermail)) {
        foreach ($newspages as $np) {
            $removed = '<p><span class="newsletter_name">'
                     . $np
                     . ':</span></p><p>'
                     . $subscribermail
                     . ' '
                     . sprintf($plugin_tx['newsletter']['unsubscribe_not_found'],
                               CMSIMPLE_URL
                               . ($plugin_tx['newsletter']['mailform_url'] != ''
                                   ? '?' . $plugin_tx['newsletter']['mailform_url']
                                   : '')
                               )
                     . '</p>';
            $subscriber_class = 'newsletter_subscribe_errmsg';
            $fc = file($pth['folder']['plugins']
                . 'newsletter/data/subscribe_'
                . newsletterUnderline4Spaces($np)
                . '.txt');
            if ($fh = fopen($pth['folder']['plugins']
                         . 'newsletter/data/subscribe_'
                         . newsletterUnderline4Spaces($np)
                         . '.txt',
                      'w')) {
                foreach($fc as $line) {
                    if (stripos($line, $subscribermail) === false) {
                        fwrite($fh, $line);
                    } else {
                        $removed = '<p><span class="newsletter_name">'
                                 . $np
                                 . ':</span></p><p>'
                                 . $plugin_tx['newsletter']['unsubscribe_succes']
                                 . '</p>';
                        $subscriber_class = 'newsletter_subscribe_ok';
                        if (stristr($plugin_cf['newsletter']['mail_confirm_unsubscribtion'], 'yes')) {
                            newsletterSubscriptionMail($subscribermail,
                                                       $plugin_tx['newsletter']['unsubscribe_succes_subject'],
                                                       $np . ': ' . $plugin_tx['newsletter']['unsubscribe_succes'],
                                                       '');
                        }
                    }
                }    
                fclose($fh);
            } else {
                $removed .= '<p>'
                          . sprintf($plugin_tx['newsletter']['unsubscribe_fail'],
                                    CMSIMPLE_URL
                                    . ($plugin_tx['newsletter']['mailform_url'] != ''
                                        ? '?' . $plugin_tx['newsletter']['mailform_url']
                                        : '')
                                    )
                          . '</p>';
            }
            $removed_msg .= $removed;
        }
    }
    else {
        $removed_msg .= $plugin_tx['newsletter']['mail_not_valid'];
        $subscriber_class = 'newsletter_subscribe_errmsg';
    }

    $removed_msg = '<div class="'
                 . $subscriber_class
                 . '">'
                 . $removed_msg
                 . '</div>';

    return $removed_msg;
}

// Is this function still used?
/*function extern_AddSubscriberToList($newspage, $subscribermail, $fieldarray) {

    global $mail_confirm_subscribtion, $o;

    if (stristr($mail_confirm_subscribtion, 'user')) {
        $o .= newsletterConfirmation($newspage, $subscribermail, $fieldarray);
        return true;
    } else {
        $o .= newsletterAddSubscriberToList($newspage, $subscribermail, $fieldarray);
        return true;
    }
    return false;
}*/

function newsletterSubscriptionMail($subscribermail, $subject, $msg, $link)
{
    global $plugin_cf, $plugin_tx, $cf, $tx, $pth, $sl, $subscribe_confirmation_mail;

    $plugin = basename(dirname(__file__), "/");
    $mail = new PHPMailer();
  if (trim($plugin_cf['newsletter']['smtp'])!="") {
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->Host = $plugin_cf['newsletter']['smtp']; // SMTP server
    if (stristr($plugin_cf['newsletter']['smtp_auth'],"true")) {
      // this section does not work and values must be inserted directly in class.phpmailer.php code
      $mail->SMTPAuth=true;
      $mail->Username=$plugin_cf['newsletter']['smtp_auth_username'];
      $mail->Password=$plugin_cf['newsletter']['smtp_auth_password'];
    }
      $mail->SMTPKeepAlive=false;  //close connection after this mail
    }
    else $mail->IsMail(); // telling the class to use mail
  
    //$mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->From = (trim($plugin_cf['newsletter']['from'])=="")?$cf['mailform']['email']:$plugin_cf['newsletter']['from'];
    $mail->FromName = (trim($plugin_cf['newsletter']['from_name'])=="")?$cf['site']['title']:$plugin_cf['newsletter']['from_name'];
    $mail->Subject = $subject;
    $mail->AddAddress(trim($subscribermail), "");    
    $mail->AltBody = $msg."\n\r".$link."\n\r".$plugin_tx['newsletter']['alt_footer'];

    $confirmation_template_file=$pth['folder']['plugins'].$plugin."/templates/".$sl."_template_confirmation.htm";
    if (!file_exists($confirmation_template_file)) {
        $confirmation_template_file=$pth['folder']['plugins'].$plugin."/templates/template_confirmation.htm";
    }
    $template=file_get_contents($confirmation_template_file);
    $link_href = '';
    if ($link != '')
        $link_href = '<a href="'
                   . $link
                   . '">'
                   . $plugin_tx['newsletter']['subscribe']
                   . '</a>';
//var_dump ($confirmation_template_file);
    $template = preg_replace('/\{CONTENT\}/', $msg . '<br><br>' . $link_href, $template);
//var_dump($template);
    
    $mail->MsgHTML($template);
    if(!$mail->Send())
        return " ".$plugin_tx['newsletter']['subscribe_confirmation_mail_error']." (".$mail->ErrorInfo.")"."<br>";
    else
        return " ".$subscribe_confirmation_mail;    
}

function newsletterVerifyMail($strEmailAddress){

    global $plugin_cf;

    $confirmed=preg_match("/^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i",$strEmailAddress);
    if ($confirmed) {
        if (function_exists('checkdnsrr')) {        
             $confirmed = checkdnsrr(substr($strEmailAddress, strrpos($strEmailAddress, '@') + 1),'MX');
        }
    }
    return $confirmed;
}

function newsletterVerifyFields($inputs) {

    global $plugin_tx;
        
    if (trim($plugin_tx['newsletter']['subscriber_fields_mandatory'])!="") {
        for ($i=0; $i<sizeof($inputs); $i++) {
            if (trim($inputs[$i])=="") {
                return false;
            }
        }
    }
    return true;
}
