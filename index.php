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

    Requirements: The plugin loader must be installed.
    Disclaimer: No warranties.
    Acknowledgement:
    Newsletter idea is partly based on plugin GenizNewsletter
    
    History:
    version 1.1 january 2010
    some corrections of absolute path in unsubscribe and text mails and urencoding of path.
    version 1.2 january 2010
    more corrections of absolute path in unsubscribe and text mails.
    version 1.3 february 2010
    added charset in config.php (if empty then deafult=iso-8859-1
    It's possible to use headers whith <h4> and above
    added style for legend. 
    version 1.4 february 2010
    bug fix   
    version 1.5 february 2010
    bug fix (utf-8 correction of confirmation mail and encoding of unsubscribe link by Frank Z.)
    version 1.6 february 2010
    added user confirmation of subscription
    version 1.7 february 2010
    minor bug fixed. Some html line breaks moved to language files.  
    version 1.8  may 2010
    added urlencoding of subscription string when "mail confirm subscription" is set to "user".
    version 1.9  may 2010
    added user email adress to unsubscription link in newsletter
    version 2.0 may 2010
    fixed compatilbility issues with pd_scripting plugin. Calls to newsletter can be inserted through 
    pd_scripting

    Version 2.1.0
    added link to newsleter if the mail is not redable in users mail client. Text defined in $plugin_tx['newsletter']['unreadable'] is converted to a link to newsletter page. To deactivate leave $plugin_tx['newsletter']['unreadable'] empty or remove {NOT_READABLE} from the template.
    Optimized to use with pd_scripting
    changed if charset is empty then cmsimple default codepage is used.

    added user defined text fields in subscription record  (subscriber_fields_label).
    Newsletter attacment are stored in downloads directory
    
    version 2.1.1 
    fixed problems on some servers with magic quotes. eregi replaced by stristr
    
    version    2.1.2
    bug fix eregi fix overwrited
    
    version 2.1.3  February 2011
    eregi replaced by stristr again
    
    version 2.2.0 
    Added template for subscription and unsubscription confirmation mail. Released under Linkware and Commercial License
    version 2.2.1
    IE bug fix in admin php
    version 2.3.0 October 2012
    Added subscription to severall newsletters in one call.
    PhpMailer updated to version 5.2.1 , no changes applied to file class.phpmailer.php
    Updated subscribe messages. 
    version 2.3.1 October 2012
        Minor bug fix in file admin.php, $mail->ClearAddresses() moved to end of the loop
    version 2.3.2 
        added recognition of relative urls and conversion to absolute urls
        changed logging. Each shipment of newsletter is loged in a separate file. Old logs are copied to files with a time stamps. Until 5 copies are stored. The latest log file can be accessed trough plugin admin. Copies are only accessible trough ftp. 
        compatiblity with CMSimple_XH ver. 1.6.
    
  version 2.3.3
   added possibility for authentification on smtp servers that requires it.
   enabled for CMSimple 1.6.x plugin calls {{{newsletter('....');}}}
   addet server authentication variables in config.php
   
 version 2.4.0
  php 7 ready
  
 version 2.4.1
  minor bugs fix
  
 version 2.4.2
   updated to CMSimple XH version 1.7
   
 version 2.5.0
   updated to CMSimple XH version 1.8
*/

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_GET['newsletterconfirm'])) {
    $f='newsletterconfirm';
    $o.= confirm_subscription();
}

$mail_confirm_subscribtion = $plugin_cf['newsletter']['mail_confirm_subscribtion'];
$subscribe_confirmation_mail = $plugin_tx['newsletter']['subscribe_confirmation_mail'];

function newsletter($newspage_list)
{
    global $plugin_tx, $plugin_cf, $cf, $sn, $su, $pth, $mail_confirm_subscribtion;
    //error_reporting (E_ALL ^ E_NOTICE);
    
    $adddel=isset($_POST['adddel']) ? $_POST['adddel'] : (isset($_GET['adddel'])?$_GET['adddel']:"");
    $subscribermail=isset($_POST['subscribermail']) ? $_POST['subscribermail'] : (isset($_GET['subscribermail'])?$_GET['subscribermail']:"");//$_GET['subscribermail'];
    if (trim($plugin_tx['newsletter']['subscriber_fields_label'])!="") {
        $labels=explode(trim($plugin_tx['newsletter']['subscriber_fields_delimiter']),$plugin_tx['newsletter']['subscriber_fields_label']);
        for ($i=0; $i<sizeof($labels); $i++) {    
      $subscriberfield[$i]=isset($_POST[rswu(trim($labels[$i]))]) ? $_POST[rswu(trim($labels[$i]))] : (isset($_GET[rswu(trim($labels[$i]))])?$_GET[rswu(trim($labels[$i]))]:"");
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
                    $o.= newsletter_confirmation($newspages, $newspage_list, $subscribermail, $subscriberfield);
                }
                else {
                    $o.=newsletter_AddSubscriberToList($newspages, $subscribermail, $subscriberfield);
                    $o.=newsletter_create_form($newspage_list, $subscribermail, $subscriberfield, $newspages);
                }
            }    // end - else if
            else if ($adddel==$plugin_tx['newsletter']['unsubscribe']) {    // remove mail from subscriber list
                    $o.=newsletter_RemoveSubscriber($newspages, $subscribermail, $subscriberfield);
                    $o.=newsletter_create_form($newspage_list, $subscribermail, $subscriberfield, $newspages);
            }   
        }
        else {
            $o.='<p class="newsletter_subscribe_errmsg">'.$plugin_tx['newsletter']["error_newsletter_selected"].'</p>';
            $o.=newsletter_create_form($newspage_list, $subscribermail, $subscriberfield, $newspages);
    }
    }    // end - if ($adddel!='')
    else {        // make subscribe/unsubscribe form
        $o.=newsletter_create_form($newspage_list, $subscribermail, $subscriberfield, $newspages);
    }
    $o.='</fieldset><!--Newsletter-->';
    return $o;
}

function confirm_subscription()
{
    global $mail_confirm_subscribtion, $plugin_cf, $plugin_tx;

    //decrypt;
    $ky = $plugin_cf['newsletter']['encrypt_key'];
    if ($ky != '') {
        $tmp = newsleter_convert($_GET['cnf'],0);
    } else {
        $tmp = $_GET['cnf'];
    }
    $tmp = explode('¤¤¤', $tmp);
    $o = '<h1>' . $plugin_tx['newsletter']['subscribe'] . '</h1>';
    $newspage[] = $tmp[1];
    $tmp = explode('@@@', $tmp[0]);
    $subscribermail = $tmp[0];
    unset($tmp[0]);
    $tmp = array_values($tmp);
    // overwrite
    $mail_confirm_subscribtion = 'thx';
    $o .= newsletter_AddSubscriberToList($newspage, $subscribermail, $tmp);

    return $o;
}

function newsletter_confirmation($newspages, $newspage_list, $subscribermail, $subscriberfield) {

    global $plugin_tx, $subscribe_confirmation_mail;

    //echo "newsletter_confirmation";
    $crform = '';
    $subscribe = '';
    if (newsletter_verify_email($subscribermail)
    && newsletter_verify_fields($subscriberfield)) {
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
            $confirm_str = newsleter_convert($confirm_str,1);
            $subscribe .= newsletter_subscription_mail($subscribermail,
                                                       $plugin_tx['newsletter']['subscribe_confirm_subject'],
                                                       $plugin_tx['newsletter']['subscribe_confirm'],
                                                       CMSIMPLE_URL . '?newsletterconfirm&cnf=' . $confirm_str);
                                                       //& -> text based e-mail can't handle &amp; // lck/Holger
        }
    } else {
        if (!newsletter_verify_email($subscribermail))
            $subscribe = '<p>' . $plugin_tx['newsletter']['mail_not_valid'] . '</p>';
        if (!newsletter_verify_fields($subscriberfield)) {
            $subscribe.= '<p>'.$plugin_tx['newsletter']['subscriber_fields_mandatory'].'</p>';
        }
        $subscriber_class = 'newsletter_subscribe_errmsg';
        $crform .= newsletter_create_form($newspage_list,
                                          $subscribermail,
                                          $subscriberfield,
                                          $newspages);
    }
    $subscribe = '<div class="' . $subscriber_class . '">' . $subscribe . '</div>' . $crform;

    return $subscribe;
}


function newsletter_create_form($newspage_list, $subscribermail, $subscriberfield, $newspages) {

    global $plugin_tx, $plugin_cf, $sn, $su, $hjs, $onload;

    $o = '';
    $onload .= 'getFocus()';
    $hjs .= '<script type="text/javascript">
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
            document.getElementById(\'userinput\').style.visibility=\'hidden\';
        } 
        else {
            document.getElementById(\'userinput\').style.visibility=\'visible\';
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
            document.getElementById("err").innerHTML=\''.$plugin_tx['newsletter']['subscriber_fields_empty'].'\'; 
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
           document.getElementById("err").innerHTML="'.$plugin_tx['newsletter']['subscriber_email_empty'].'";
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
        $o .= '<select name="adddel" id="addel" style="visibility:hidden">'
            . "\n"
            . '<option>'
            . $plugin_tx['newsletter']['subscribe']
            . '</option>'
            . "\n";
        $o .= '<option selected>'
            . $plugin_tx['newsletter']['unsubscribe']
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
            . $plugin_tx['newsletter']['subscribe']
            .'</option>'
            . "\n";
        $o .= '<option>'
            . "\n"
            . $plugin_tx['newsletter']['unsubscribe']
            . '</option>'
            . "\n"
            . '</select>'
            . "\n";
    }

    $o .= '<br>'
        . '<span class="newsletter_label">'
        . $plugin_tx['newsletter']['subscriber_email_label']
        . ':&nbsp;</span>'
        . '<input name="subscribermail" id="subscribermail" type="text" class="newsletter_inputfield" value="'
        . (isset($_GET['uns'])
            ? newsleter_convert($_GET['uns'], 0)
            : $subscribermail)
        . '" onblur="newsletter_ValidEmail(document.subscribe);">'
        . '<br>'
        . "\n";
    $mandatory = (trim($plugin_tx['newsletter']['subscriber_fields_mandatory']) != '');
    if (!isset($_GET['uns'])
    && trim($plugin_tx['newsletter']['subscriber_fields_label']) != '') {
        $labels = explode(trim($plugin_tx['newsletter']['subscriber_fields_delimiter']),
                          $plugin_tx['newsletter']['subscriber_fields_label']);
        $user_input = '';
        for ($i = 0; $i < sizeof($labels); $i++) {
            $user_input .= '<span class="newsletter_label">'
                         . trim($labels[$i])
                         . ':&nbsp;</span><input name="'
                         . rswu(trim($labels[$i]))
                         . '" id="'
                         . rswu(trim($labels[$i]))
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
                ? $plugin_tx['newsletter']['select_newsletters_unsubscribe']
                : $plugin_tx['newsletter']['select_newsletters_subscribe'])
            . '<br>';
        foreach ($newspage_arr as $np) {
            $checked = false;
            for ($i = 0; $i < sizeof($newspages); $i++) {
                if (rswu(trim($np)) == trim($newspages[$i])) {
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
                . rswu(trim($np))
                . '">'
                . "\n";
        }
        $o .='<br>';
    } else {
        // singel newsletter subscribe
        $o .= '<input name="single_newspage" type="hidden" value="'
            . rswu($newspage_list)
            . '">'
            . '&nbsp;';
    }
    $o .= '<br>'
        . '<span class="newsletter_label"></span>'
        . "\n"
        . '<input type="submit" class="submit" value="'
        . $plugin_tx['newsletter']['send']
        . '">'
        . "\n"
        . '</form>';

    return $o;
}

function rswu($fname)
{
    if ($fname) {
        return str_replace(' ', '_', $fname);
    }
    return false;
}

function newsletter_AddSubscriberToList($newspages, $subscribermail, $subscriberfield) {

    global $plugin_tx, $pth, $mail_confirm_subscribtion;

    $subscribe_msg = '';
    if (newsletter_verify_email($subscribermail)
    &&  newsletter_verify_fields($subscriberfield)) {
        $subscriber_class = 'newsletter_subscribe_ok';
        foreach ($newspages as $np) {
            $fhandle = $pth['folder']['plugins']
                     . 'newsletter/data/subscribe_'
                     . rswu($np)
                     . '.txt';
            if (is_readable($fhandle)) {
                $fc = file($fhandle);
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
                case 'yes':
                case 'mail':
                    $subscribe_msg .= newsletter_subscription_mail($subscribermail,
                                                                   $plugin_tx['newsletter']['subscribe_succes_subject'],
                                                                   $plugin_tx['newsletter']['mail_subscribe_succes'],
                                                                   '');
                    break;
                case 'user':
                    $subscribe_msg .= $plugin_tx['newsletter']['subscribe_confirm_text'];
                    break;
                case 'thx':
                    $subscribe_msg .= $plugin_tx['newsletter']["mail_subscribe_thx"];
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
        if (!newsletter_verify_email($subscribermail)) {
            $subscribe_msg = $plugin_tx['newsletter']['mail_not_valid']
                           . '&nbsp;';
        }
        if (!newsletter_verify_fields ($subscriberfield)) {
            $subscribe_msg .= $plugin_tx['newsletter']['subscriber_fields_mandatory'];
        }
        $subscriber_class = 'newsletter_subscribe_errmsg';
    }

    return $subscribe_msg;
}

function newsletter_RemoveSubscriber($newspages, $subscribermail, $subscriberfield) {

    global $plugin_tx, $plugin_cf, $pth;

    $removed_msg = '<p><span class="newsletter_abo_for">'
                 . $plugin_tx['newsletter']["subscription_for"]
                 . '</span>'
                 . $subscribermail
                 . '</p>';
    if (newsletter_verify_email($subscribermail)) {
        foreach ($newspages as $np) {
            $removed = '<p><span class="newsletter_name">'
                     . $np
                     . ':</span></p><p>'
                     . $subscribermail
                     . ' '
                     . $plugin_tx['newsletter']['unsubscribe_not_found']
                     . '</p>';
            $subscriber_class = 'newsletter_subscribe_errmsg';
            $fc = file($pth['folder']['plugins']
                . 'newsletter/data/subscribe_'
                . rswu($np)
                . '.txt');
            if ($fh = fopen($pth['folder']['plugins']
                         . 'newsletter/data/subscribe_'
                         . rswu($np)
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
                            newsletter_subscription_mail($subscribermail,
                                                         $plugin_tx['newsletter']['unsubscribe_succes_subject'],
                                                         $np . ': ' . $plugin_tx['newsletter']['unsubscribe_succes'],
                                                         '');
                        }
                    }
                }    
                fclose($fh);
            }
            else {
                $removed .= '<p>' . $plugin_tx['newsletter']['unsubscribe_fail'] . '</p>';
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
function extern_AddSubscriberToList($newspage, $subscribermail, $fieldarray) {

    global $mail_confirm_subscribtion, $o;

    if (stristr($mail_confirm_subscribtion, 'user')) {
        $o .= newsletter_confirmation($newspage, $subscribermail, $fieldarray);
        return true;
    } else {
        $o .= newsletter_AddSubscriberToList($newspage, $subscribermail, $fieldarray);
        return true;
    }
    return false;
}

function newsletter_subscription_mail($subscribermail, $subject, $msg, $link)
{
    global $plugin_cf, $plugin_tx, $cf, $tx, $pth, $sl, $subscribe_confirmation_mail;

    // PHPMailer
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        include_once __DIR__ . '/phpmailer/PHPMailer.php';
    }
    if (!class_exists('PHPMailer\PHPMailer\Exception')) {
        include_once __DIR__ . '/phpmailer/Exception.php';
    }
    if (!class_exists('PHPMailer\PHPMailer\SMTP')) {
        include_once __DIR__ . '/phpmailer/SMTP.php';
    }

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
    if (trim($plugin_tx['newsletter']['charset'])!="") {
       $mail->CharSet=$plugin_tx['newsletter']['charset'];
    }
    else {
        if (isset($tx['meta']['codepage'])) //available from cmsimple_XH v 1.2
            $mail->CharSet=$tx['meta']['codepage'];
    }
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
    $link_href="";
    if ($link!="")
        $link_href='<a href="'.$link.'">'.$link.'</a>'.'<br>';
//var_dump ($confirmation_template_file);
    $template=preg_replace('/\{CONTENT\}/',$msg.'<br>'.$link_href,$template);
//var_dump($template);
    
    $mail->MsgHTML($template);
    if(!$mail->Send())
        return " ".$plugin_tx['newsletter']['subscribe_confirmation_mail_error']." (".$mail->ErrorInfo.")"."<br>";
    else
        return " ".$subscribe_confirmation_mail;    
}

function newsletter_verify_email($strEmailAddress){
    global $plugin_cf;

    $confirmed=preg_match("/^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i",$strEmailAddress);
    if ($confirmed) {
        if (function_exists('checkdnsrr')) {        
             $confirmed = checkdnsrr(substr($strEmailAddress, strrpos($strEmailAddress, '@') + 1),'MX');
        }
    }
    return $confirmed;
}

function newsletter_verify_fields($inputs) {
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

// String EnCrypt + DeCrypt function
// Author: halojoy, July 2006
// encrypt = 1, decrypt = 0
function newsleter_convert($str, $encrypt){
    global $plugin_cf; 

    if (!$encrypt)
        // decrypt
        $str = base64_decode(rawurldecode($str));
    $ky = $plugin_cf['newsletter']['encrypt_key'];
    if ($ky == '') return $str;

    $ky = str_replace(chr(32), '', $ky);
    if (strlen($ky) < 8) exit('key error');
    $kl = strlen($ky) < 32 ? strlen($ky) : 32;
    $k = array();

    for ($i = 0; $i < $kl; $i++) {
        $k[$i] = ord($ky[$i])&0x1F;
    }
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $e = ord($str[$i]); 
        $str[$i] = $e&0xE0 ? chr($e^$k[$j]) : chr($e);
        $j++;
        $j = $j == $kl ? 0 : $j;
    }
    if ($encrypt)
        // encrypt
        $str = rawurlencode(base64_encode($str));
    return $str;
}
