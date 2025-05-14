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

/*
 * create an entry in the XH log file
 *
 * return void
 */
function nl_XH_logMessage($failmessage) {

    XH_logMessage('warning',
                  'Newsletter_XH',
                  'registration - ' . $_POST['subscribermail'],
                  'Spam suspicion: ' . $failmessage);
}

/*
 * creates an error message
 *
 * return html
 */
function nl_renderErrorMessage($ptxPart2) {

    global $plugin_tx;

    $o = '<p class="xh_fail">'
       . $plugin_tx['newsletter'][$ptxPart2]
       . '</p>';

    return $o;
}

/*
 * creates a form field (type text)
 *
 * return html
 */
function nl_renderFormField($ptxPart2, $name, $value, $placeholder = false) {

    global $plugin_tx;

    $o = '<br>'
       . '<span class="newsletter_label newsletter_noeyes">'
       . $plugin_tx['newsletter'][$ptxPart2]
       . '</span>'
       . "\n"
       . '<input'
       . ($placeholder
            ? ' placeholder="' . $plugin_tx['newsletter'][$ptxPart2] . '"'
            : '')
       . ' class="newsletter_inputfield newsletter_noeyes" name="'
       . $name
       . '" type="text" value="'
       . $value
       . '">'
       . "\n";

    return $o;
}

/*
 * returns the form field hp-subject
 *
 * return html
 */
function nl_fieldHoneypotDisplay() {

    return nl_renderFormField('field_leave_blank',
                              'hp-subject',
                              '',
                              'field_leave_blank');
}

/*
 * check the form field hp-subject
 *
 * return bool
 */
function nl_fieldHoneypotCheck() {

    global $plugin_tx;

    $o = false;

    if (!empty($_POST['hp-subject'])) {
        $failmessage = 'Honeypot';
        nl_XH_logMessage($failmessage);
        $ptxPart2 = 'info_spam_suspicious';
        return nl_renderErrorMessage($ptxPart2);
    }

    return $o;
}

/*
 * creates an encrypted timpstamp
 *
 * return string
 */
function nl_generateTimeHash() {

    global $cf;

    // the key for encrypt
    $key = $cf['security']['secret'];
    // second unique key
    $iv = md5($cf['security']['secret'], true);
    // the encryption method
    $cipher = 'aes-128-cbc';

    $o = time();
    // test whether encryption method is available
    if(in_array($cipher, openssl_get_cipher_methods())) {
        // returns encrypted timestamp value
        $o = openssl_encrypt($o, $cipher, $key, 0, $iv);
    }

  return $o;
}

/*
 * returns the form field sp_time
 *
 * return html
 */
function nl_fieldSpamTimeDisplay() {

    return nl_renderFormField('field_do_not_change',
                              'sp_time',
                              nl_generateTimeHash());
}

/*
 * check the form field sp_time
 *
 * return bool
 */
function nl_fieldSpamTimeCheck() {

    global $cf, $plugin_cf;

    $o = false;
    // the key for encrypt
    $key = $cf['security']['secret'];
    // second unique key
    $iv = md5($cf['security']['secret'], true);
    // the encryption method
    $cipher = 'aes-128-cbc';
    $minTime = (int)$plugin_cf['newsletter']['spam_protection_min_time'];
    $maxTime = (int)$plugin_cf['newsletter']['spam_protection_max_time'];

    $givenTime = isset($_POST['sp_time']) ? $_POST['sp_time'] : '';

    $decryptedTime = $givenTime;
    // decode passed value
    if (in_array($cipher, openssl_get_cipher_methods())) {
        $decryptedTime = openssl_decrypt($givenTime, $cipher, $key, 0, $iv);
        // supplied value could not be decypted -> input field was edited
        if (!$decryptedTime) {
            $failmessage = 'The Time field has been manipulated.';
            nl_XH_logMessage($failmessage);
            $ptxPart2 = 'info_spam_suspicious';
            return nl_renderErrorMessage($ptxPart2);
        }
    }
    // check whether the transferred value could be checked
    $currentTime = time();
    if (($currentTime - $decryptedTime) <= $minTime
    || ($currentTime - $decryptedTime) >= $maxTime ) {
        $failmessage = 'The submission is outside from the specified time frame.';
        nl_XH_logMessage($failmessage);
        $ptxPart2 = 'info_spam_suspicious';
        return nl_renderErrorMessage($ptxPart2);
    }

    return $o;
}
