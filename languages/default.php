<?php

$plugin_tx['newsletter']['menu_main'] = "Info / Publish";

$plugin_tx['newsletter']['admin_gplv3_1'] = "can be used in compliance with";
$plugin_tx['newsletter']['admin_gplv3_2'] = ".";

$plugin_tx['newsletter']['material_icons_1'] = "This plugin uses";
$plugin_tx['newsletter']['material_icons_2'] = "This icon fonts are available under Apache-Lizenzversion 2.0.";

$plugin_tx['newsletter']['mailform_url'] = "mailform";

$plugin_tx['newsletter']['publish']="Publish";
$plugin_tx['newsletter']['admin_subscribers']="Receiver";
$plugin_tx['newsletter']['admin_log']="Log";
$plugin_tx['newsletter']['mailing_list']="Mailing list";
$plugin_tx['newsletter']['subscribe']="Subscribe";
$plugin_tx['newsletter']['subscribe_succes']="Subscription succeeded.";
$plugin_tx['newsletter']['subscribe_fail']="Your subscription to the newsletter failed. Please contact us via <a href=\"%s\">contact form</a> or try again later.";
$plugin_tx['newsletter']['unsubscribe']="Unsubscribe";
$plugin_tx['newsletter']['unsubscribe_succes']="Your subscription is stopped.";
$plugin_tx['newsletter']['unsubscribe_not_found']="not found. Please contact us via the <a href=\"%s\">contact form</a> or try again later.";
$plugin_tx['newsletter']['unsubscribe_fail']="Could not remove your subscription. Please contact us via <a href=\"%s\">contact form</a> or try again later";
$plugin_tx['newsletter']['subscribe_confirmation_mail']="Thank you for subscribing to our newsletter.<br>You will receive a confirmation e-mail shortly.";
$plugin_tx['newsletter']['subscribe_confirm_text']="Thank you for signing up. To confirm your subscription, please reply the mail you will recieve within a few hours.";
$plugin_tx['newsletter']['subscribe_confirmation_mail_error']="Confirmation e-mail was not sent because of an error:";
$plugin_tx['newsletter']['mail_not_valid']="Your e-mail could not be confirmed.<br>Try it again.";
$plugin_tx['newsletter']['send']="Send";
$plugin_tx['newsletter']['subject']="Subject:";
$plugin_tx['newsletter']['attachment']="Attach file";
$plugin_tx['newsletter']['newsletter_subject']="Newsletter from %s";
$plugin_tx['newsletter']['test_mail']="Send test mail:";
$plugin_tx['newsletter']['test_mail_sent']="Test mail sent to";
$plugin_tx['newsletter']['test_mail_nosent']="No test-mail sent to";
$plugin_tx['newsletter']['mnu_main']="Plugin Admin";
$plugin_tx['newsletter']['restart']="Restore";
$plugin_tx['newsletter']['sendinit']="<br>The sending of the newsletters will start automatically. Please do not touch any keys while the sending is on.";
$plugin_tx['newsletter']['msg_sent']="Sent";
$plugin_tx['newsletter']['msg_of']="of";
$plugin_tx['newsletter']['msg_wait']="Sending wait...";
$plugin_tx['newsletter']['msg_errorcount']="Number of errors";
$plugin_tx['newsletter']['msg_error']="Error message";
$plugin_tx['newsletter']['recivercount']="Number of subscribers";
$plugin_tx['newsletter']['log_success']="OK";
$plugin_tx['newsletter']['log_error']="Error";
$plugin_tx['newsletter']['footer_unsubscribe']="Unsubscribe";
$plugin_tx['newsletter']['alt_body']="The newsletter is not readable in your email program. But do not despair. Follow this link to read the newsletter on the website:";
$plugin_tx['newsletter']['alt_footer']="Regards\r\n\r\nYour name";
$plugin_tx['newsletter']['alt_unsubscribe']="You can unsubscribe from the newsletter here:";
$plugin_tx['newsletter']['subscribe_succes_subject']="Thank you for signing up for the newsletter.";
$plugin_tx['newsletter']['subscribe_confirm_subject']="Please confirm your subscription";
$plugin_tx['newsletter']['unsubscribe_succes_subject']="Your subscription is stopped.";
$plugin_tx['newsletter']['subscribe_confirm']="Thank you for signing up for the newsletter. To confirm your subscription please follow this link:";
$plugin_tx['newsletter']['admin_template']="Newsletter template";
$plugin_tx['newsletter']['unreadable']="Click here";
$plugin_tx['newsletter']['subscriber_fields_delimiter']=";";
$plugin_tx['newsletter']['subscriber_fields_label']="Title; First Name; Last Name";
$plugin_tx['newsletter']['subscriber_fields_mandatory']="Please complete all fields";
$plugin_tx['newsletter']['subscriber_fields_empty']="Please complete all fields";
$plugin_tx['newsletter']['subscriber_email_empty']="Please enter a valid email adress.";
$plugin_tx['newsletter']['subscriber_email_label']="E-mail adress";
$plugin_tx['newsletter']['date_format']="Y-m-d H:i:s";
$plugin_tx['newsletter']['admin_confirmation_template']="Confirmation template";
$plugin_tx['newsletter']['cf_separator']="You can have several newsletters on the same page. Only the one over the separator will be send. Default is \"&lt;hr&gt;\".";
$plugin_tx['newsletter']['cf_adminmail']="The mail address where you want to send test mail. If left empty the CMSimple contact form email is used.";
$plugin_tx['newsletter']['cf_from']="The senders mail. If left empty the CMSimple contact form email is used instead.";
$plugin_tx['newsletter']['cf_from_name']="The senders name. If left empty the CMSimple site title from your language file is used (CMSimple_XH only)";
$plugin_tx['newsletter']['cf_smtp']="Insert your internet providers smtp server name. If left blank newsletter will send mails through php mail command. The best sending method is smtp.";
$plugin_tx['newsletter']['cf_max_execution_time']="The server time in seconds before timeout. You can find it by executing phpinfo.php on your server. Look for max_execution_time. If you experience timeouts when sending newsletters reduce this value to something lower.";
$plugin_tx['newsletter']['cf_mail_confirm_subscribtion']="<p>Values: <b>mail</b>, <b>user</b> or <b>no</b>.</p><p><b>mail</b>: the mail is send to new subscriber. The content of this mails is defined in a confirmation template in Plugin Main Settings\".</p><p><b>user</b>: To activate the subscription the subscriber must reply the mail containing a link to confirmation page on your website. The content of the mail is defined in plugin language options \"subscribe confirm subject\" and \"subscribe confirm\". All subscription data is encrypted.</p>";
$plugin_tx['newsletter']['cf_mail_confirm_unsubscribtion']="<p>Values: yes or no.</p><p>If yes the mail is send to the subscriber otherwise no. The content of this mail is defined in plugin language \"unsubscribe success subject\" and \"unsubscribe success\".</p>";
$plugin_tx['newsletter']['cf_encrypt_key']="8 to 32 arbitrary characters without spaces. Used to encrypt subscription information in activation mail.";
$plugin_tx['newsletter']['cf_selected_img']="The name of an icon for selected newsletter. Only visible if you have more then one newsletter. Default is selcted.png. The icon image must be located in Newsletter images folder";
//Changes Version: 2.3.0
$plugin_tx['newsletter']["subscription_for"]="Subscription for: ";
$plugin_tx['newsletter']['select_newsletters_subscribe']="Please select newsletters you wish to subscribe/unsubscribe from the list below";
$plugin_tx['newsletter']['select_newsletters_unsubscribe']="Please select newsletters you wish to unsubscribe from the list below";
$plugin_tx['newsletter']["error_newsletter_selected"]="Error: No newsletter specified. Please check at least one newsletter subscription from the list of available newsletters.";
$plugin_tx['newsletter']['mail_subscribe_succes']="Thank you for signing up. You can cancel your subscription on our <a href=\"%s\">website</a>.";
$plugin_tx['newsletter']['mail_subscribe_thx']="Thank you for signing up.";
$plugin_tx['newsletter']['utf8_marker']="æøå";
//Version .2.3.2
$plugin_tx['newsletter']['cf_editor_relative_urls']="<p>Values true/false</p><p>Default: true</p><p>If true then Newsletter will rewrite relative urls to absolute</p><p>If links and images does not work in your newsletters then you have to change this value to false and change configuration of your editor to relative urls = false (or absolute urls=true).</p>";
$plugin_tx['newsletter']['cf_attachment_folder']="<p>The name of the attachment subfolder.</p><p>The attachment subfolder must be a subfolder of CMSimple_XH default downloads folder.</p><p>If left blank the CMSimple_XH default downloads folder is used.</p><p>You need to use ftp to copy files to attachment folder.</p>";
$plugin_tx['newsletter']['adm_newsletter_subject']="<p>Subject of your newsletter</p>";
$plugin_tx['newsletter']['adm_newsletter_attachment']="<p>Select an attachment file from the list of files</p><p>Attachment files are stored in a folder defined in plugin configuration variable \"attachment folder\".</p><p>Use FTP to move files to the attchment folder.</p>";
$plugin_tx['newsletter']['adm_newsletter_sendto']="<p>If \"Send test mail\" is checked then you will send only a test newsletter to the specified e-mail adress.</p><p>Check the content, links and images (they must have an absolute path) and if your received newsletter is as expected then uncheck the check box and push Publish button again to send newsletters to all recipients.</p>";
$plugin_tx['newsletter']['adm_newsletter_restore']="<p>In case of time out this field will contain the number of the last successful newsletter (ff the field is empty then you need to insert the number of last successful newsletter from the log file).</p><p>Click the Publish button to continue sending mails.</p>";
//version 2.3.3
$plugin_tx['newsletter']['cf_smtp_auth']="<p>Set to true if your smtp server requires authentication.</p>"; 
$plugin_tx['newsletter']['cf_smtp_auth_username']="<p>Authentification username to SMTP server.</p>";
$plugin_tx['newsletter']['cf_smtp_auth_password']="<p>Authentification password to SMTP server.</p>";
$plugin_tx['newsletter']['terms_of_use']='<p><strong>Terms of use:</strong></p><p>Newsletter may only be used to send messages to subscribers who voluntary have agreed to receive messages from your website by subscribing. All other use is to be regarded as spamming and is prohibited.</p><p> By using the Newsletter you automatically accept these conditions.</p>';
$plugin_tx['newsletter']['cf_debug']="0 = off (for production use)  1 = client messages  2 = client and server messages";
//version 2.5.1
$plugin_tx['newsletter']['field_leave_blank']="Do not fill in!";
$plugin_tx['newsletter']['info_spam_suspicious']="Spam suspicion<br>Your request will not be processed further.";
$plugin_tx['newsletter']['cf_spam_protection']="Activate spam protection";
$plugin_tx['newsletter']['cf_spam_protection_min_time']="Minimum time in seconds that must elapse before the form is sent. (default: 5)";
$plugin_tx['newsletter']['cf_spam_protection_max_time']="Maximum time in seconds that may elapse before the form is sent. (default: 1800)";

$plugin_tx['newsletter']['cf_license']="<p>Default is Linkware</p><p><strong>Do not change unless you have purchased Newslterrer Remove Link license on simplesolutions.dk website</strong></p>";    
