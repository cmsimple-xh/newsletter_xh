<?php

$plugin_tx['newsletter']['publish']="Opsturen";
$plugin_tx['newsletter']['admin_subscribers']="Mailing-lijsten";
$plugin_tx['newsletter']['admin_log']="Log";
$plugin_tx['newsletter']['mailing_list']="Mailing-lijst";
$plugin_tx['newsletter']['subscribe']="Abonneren";
$plugin_tx['newsletter']['subscribe_succes']="Bedankt.<br>U bent nu geabonneerd op onze nieuwsbrief.";
$plugin_tx['newsletter']['subscribe_fail']="Iets met uw newsletter-bestelling heeft niet gefunctioneerd. Stuur ons a.u.b. via het <a href=\"./?mailform\">kontaktformulier</a> uw newsletter-bestelling.";
$plugin_tx['newsletter']['unsubscribe']="Afmelden";
$plugin_tx['newsletter']['unsubscribe_succes']="Uw nieuwsbrief-abonnement is bij deze stopgezet.";
$plugin_tx['newsletter']['unsubscribe_not_found']="niet gevonden. Stuur ons a.u.b. via het <a href=\"./?mailform\">kontaktformulier</a> uw newsletter-afmelding.";
$plugin_tx['newsletter']['unsubscribe_fail']="De afmelding heeft niet functioneerd. Stuur ons a.u.b. via het <a href=\"./?mailform\">kontaktformulier</a> uw newsletter-afmelding";
$plugin_tx['newsletter']['confirmation_mail']="Een bevestigings-mailtje is aan uw e-mail adres verstuurd.";
$plugin_tx['newsletter']['confirmation_mail_error']="Een bevestigings-mailtje kon niet worden verstuurd omdat er een fout is gebeurd.";
$plugin_tx['newsletter']['mail_not_valid']="Uw hebt een vals e-mail adres opgegeven.<br> Geef een correct e-mail adres op en probeer het nog een keer.";
$plugin_tx['newsletter']['send']="Opsturen";
$plugin_tx['newsletter']['subject']="Onderwerp: ";
$plugin_tx['newsletter']['attachment']="File toevoegen";
$plugin_tx['newsletter']['newsletter_subject']="Newsbrief van -------";
$plugin_tx['newsletter']['test_mail']="Testzending:";
$plugin_tx['newsletter']['test_mail_sent']="Test-newsletter verstuurd aan";
$plugin_tx['newsletter']['test_mail_nosent']="Geen test-newsletter verstuurd aan";
$plugin_tx['newsletter']['mnu_main']="Plugin Admin";
$plugin_tx['newsletter']['restart']="Herstarten";
$plugin_tx['newsletter']['sendinit']="<br>Het versturen van de newsletters start automatisch. A.u.b. tijdens het versturen geen toetsen aanraken:";
$plugin_tx['newsletter']['msg_sent']="Verstuurd";
$plugin_tx['newsletter']['msg_of']="van";
$plugin_tx['newsletter']['msg_wait']="Zendpauze...";
$plugin_tx['newsletter']['msg_errorcount']="Aantal van fouten";
$plugin_tx['newsletter']['msg_error']="Foutmelding";
$plugin_tx['newsletter']['recivercount']="Aantal van abonnees";
$plugin_tx['newsletter']['log_success']="OK";
$plugin_tx['newsletter']['log_error']="Fout";
//	$plugin_tx['newsletter']['newsletter_logo']="<h1>-Newsletter kopje-</h1>";
//	$plugin_tx['newsletter']['newsletter_greetings']="<p>Vriendelijke groet<br>-uw naam-</p>";
//	$plugin_tx['newsletter']['footer_newsletter']="<span><font size=\"1\" face=\"Verdana\">A.u.b. stuur geen antwoord-mailtje aan het newsletter e-mail adres.</font></span>";
$plugin_tx['newsletter']['footer_unsubscribe']="Opzeggen";
$plugin_tx['newsletter']['alt_body']="Als de newsletter hier niet verschijnt, kom je via deze link direct op onze website terecht: ";
$plugin_tx['newsletter']['alt_footer']="Met vriendelijke groet\r\n\r\n-uw naam-";
//version 1.6
$plugin_tx['newsletter']['alt_unsubscribe']="Hier kan je je newsletter-abonnement stopzetten: "; //dynamic link to unsubscribe page is added her
$plugin_tx['newsletter']['subscribe_succes_subject']="Bedankt voor het abonneren van onze nieuwsbrief.";
$plugin_tx['newsletter']['subscribe_confirm_subject']="A.u.b. bevestig hier uw nieuwsbrief-abonnement";
$plugin_tx['newsletter']['unsubscribe_succes_subject']="Uw nieuwsbrief-abonnement is stopgezet.";
$plugin_tx['newsletter']['subscribe_confirm_text']="Bedankt vor het abonneren van onze nieuwsbrief. U dient het abonnement te <strong>bevestigen</strong> door te klikken op een link in het mailtje dat u van ons zult ontvangen.";
$plugin_tx['newsletter']['subscribe_confirm']="U heeft zich zo juist geabonneerd op onze nieuwsbrief. A.u.b klik op deze link om uw abonnement te bevestigen:<br>(Als u niet besteld hebt, is er een fout gebeurd. Onze excuses daarvoor.) "; //link to confirmation page is added her
//version 2.0.1	
$plugin_tx['newsletter']['admin_template']="Template";
$plugin_tx['newsletter']['unreadable']="Klik hier";
$plugin_tx['newsletter']['subscriber_fields_delimiter']=";";
$plugin_tx['newsletter']['subscriber_fields_label']="Aanspreekvorm; Voornaam; Achternaam";
$plugin_tx['newsletter']['subscriber_fields_mandatory']=""; // blank if fields are not mandatory  
$plugin_tx['newsletter']['subscriber_fields_empty']="A.u.b. alle vakjes invullen.";
$plugin_tx['newsletter']['subscriber_email_empty']="A.u.b. een correct email adres invullen.";
$plugin_tx['newsletter']['subscriber_email_label']="Email adres";
$plugin_tx['newsletter']['charset']="utf-8";
$plugin_tx['newsletter']['date_format']="d-m-Y H:i:s";
$plugin_tx['newsletter']['admin_confirmation_template']="Confirmation";

$plugin_tx['newsletter']['cf_separator']=' You can have several newsletters on the same page. Only the one over the separator will be send. Default is "&lt;hr&gt;".';
$plugin_tx['newsletter']['cf_adminmail']='The mail address where you want to send test mail. If left empty the CMSimple mailform email is used.';
$plugin_tx['newsletter']['cf_from']='The senders mail. If left empty the CMSimple mailform email is used instead.';
$plugin_tx['newsletter']['cf_from_name']='The senders name. If left empty the CMSimple site title from your language file is used (CMSimple_XH only)';
$plugin_tx['newsletter']['cf_smtp']='Insert your internet providers smtp server name. If left blank newsletter will send mails through php mail command. The best sending method is smtp.';
$plugin_tx['newsletter']['cf_max_execution_time']='The server time in seconds before timeout. You can find it by executing phpinfo.php on your server. Look for max_execution_time. If you experience timeouts when sending newsletters reduce this value to something lower.';
$plugin_tx['newsletter']['cf_mail_confirm_subscribtion']='<p>Values: <b>mail</b>, <b>user</b> or <b>no</b> (yes can be used instead of mail).</p><p><b>mail</b> or <b>yes</b>: the mail is send to new subscriber. The content of this mails is defined in a confirmation template in Plugin Main Settings".</p><p><b>user</b>: To activate the subscription the subscriber must reply the mail containing a link to confirmation page on your website. The content of the mail is defined in plugin language options "subscribe confirm subject" and "subscribe confirm". All subscription data is encrypted.</p>';
$plugin_tx['newsletter']['cf_mail_confirm_unsubscribtion']='<p>Values: yes or no.</p><p>If yes the mail is send to the subscriber otherwise no. The content of this mail is defined in plugin language "unsubscribe success subject" and "unsubscribe success".</p>';
$plugin_tx['newsletter']['cf_encrypt_key']='8 to 32 arbitrary characters without spaces. Used to encrypt subscription information in activation mail.';
$plugin_tx['newsletter']['cf_selected_img']='The name of an icon for selected newsletter. Only visible if you have more then one newsletter. Default is selcted.png. The icon image must be located in Newsletter images folder';

//Changes Version: 2.3.0  	
$plugin_tx['newsletter']["subscription_for"]="Subscription for: ";
$plugin_tx['newsletter']['select_newsletters_subscribe']="Please select newsletters you wish to subscribe/unsubscribe from the list below";
$plugin_tx['newsletter']['select_newsletters_unsubscribe']="Please select newsletters you wish to unsubscribe from the list below";
$plugin_tx['newsletter']["error_newsletter_selected"]="Error: No newsletter specified. Please check at least one newsletter subscription from the list of available newsletters.";
$plugin_tx['newsletter']["mail_subscribe_succes"]="Thank you for signing up. You can cancel your subscription on our <a href=\"http:your_domain.com\">website</a>.";
$plugin_tx['newsletter']["mail_subscribe_thx"]="Thank you for signing up.";
$plugin_tx['newsletter']['utf8_marker']="æøå";
//Version .2.3.2
$plugin_tx['newsletter']['cf_editor_relative_urls']="<p>Values true/false</p><p>Default: true</p><p>If true then Newsletter will rewrite relative urls to absolute</p><p>If links and images does not work in your newsletters then you have to change this value to false and change configuration of your editor to relative urls = false (or absolute urls=true).</p>";
$plugin_tx['newsletter']['cf_attachment_folder']="<p>The name of the attachment subfolder</p><p>The attachment subfolder must be a subfolder of CMSimple default downloads folder</p><p>If left blank the CMSimple default downloads folder is used.</p><p>You need to use ftp to copy files to attachment folder</p>";
$plugin_tx['newsletter']['adm_newsletter_subject']="<p>Subject of your newsletter</p>";
$plugin_tx['newsletter']['adm_newsletter_attachment']="<p>Select an attachment file from the list of files</p><p>Attachment files are stored in a folder defined in plugin configuration variable \"attachment folder\".</p><p>Use FTP to move files to the attchment folder.</p>";
$plugin_tx['newsletter']['adm_newsletter_sendto']="<p>If \"Send test mail\" is checked then you will send only a test newsletter to the specified e-mail adress.</p><p>Check the content, links and images (they must have an absolute path) and if your received newsletter is as expected then uncheck the check box and push Publish button again to send newsletters to all recipients.</p>";
$plugin_tx['newsletter']['adm_newsletter_restore']="<p>In case of time out this field will contain the number of the last succesfull newsletter (ff the field is empty then you need to insert the number of last succesfull newsletter from the log file).</p><p>Click the Publish button to continue sending mails.</p>";
//version 2.3.3
$plugin_tx['newsletter']['cf_smtp_auth']="<p>Set to true if your smtp server requires authentication.</p><p>If true is selected then you must fill in the fields Auth username and Auth password</p>"; 
$plugin_tx['newsletter']['cf_smtp_auth_username']="<p>Authentification username to SMTP server.</p>";
$plugin_tx['newsletter']['cf_smtp_auth_password']="<p>Authentification password to SMTP server.</p>";
$plugin_tx['newsletter']['terms of use']='<p><strong>Terms of use:</strong></p><p>Newsletter may only be used to send messages to subscribers who voluntary have agreed to receive messages from your website by subscribing. All other use is to be regarded as spamming and is prohibited. </p><p>Newsletter is released under Remove Link and Commercial License. Visit Newsletters page on <a href="http://simplesolutions.dk">simpleSolutions</a> to buy license</p><p> By using the Newsletter you automatically accept these conditions.</p>';
