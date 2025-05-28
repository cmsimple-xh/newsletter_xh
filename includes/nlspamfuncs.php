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
function newsletterXH_logMessage($failmessage) {

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
function newsletterRenderErrorMessage($ptxPart2) {

    global $plugin_tx;

    return '<p class="xh_fail">'
          . $plugin_tx['newsletter'][$ptxPart2]
          . '</p>';
}

/*
 * creates a form field
 *
 * return html
 */
function newsletterRenderFormField($name,
                                   $type,
                                   $value,
                                   $ptxPart2 = '',
                                   $placeholder = false) {

    global $plugin_tx;

    $o = ($type != 'hidden'
            ? '<br><span class="newsletter_label newsletter_noeyes">'
               . $plugin_tx['newsletter'][$ptxPart2]
               . '</span>'
               . "\n"
            : '')
       . '<input'
       . ($placeholder
            ? ' placeholder="' . $plugin_tx['newsletter'][$ptxPart2] . '"'
            : '')
       . ($type != 'hidden'
            ? ' class="newsletter_inputfield newsletter_noeyes"'
            : '')
       . ' name="'
       . $name
       . '" type="'
       . $type
       . '" value="'
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
function newsletterFieldHoneypotDisplay() {

    return newsletterRenderFormField('hp-subject',
                                     'text',
                                     '',
                                     'field_leave_blank',
                                     'field_leave_blank');
}

/*
 * check the form field hp-subject
 *
 * return bool
 */
function newsletterFieldHoneypotCheck() {

    if (!empty($_POST['hp-subject'])) {
        $failmessage = 'Honeypot';
        newsletterXH_logMessage($failmessage);
        $ptxPart2 = 'info_spam_suspicious';
        return newsletterRenderErrorMessage($ptxPart2);
    }

    return false;
}

/*
 * creates an encrypted timpstamp
 *
 * return string
 */
function newsletterGenerateTimeHash($time) {

    global $cf;

    // the key for encrypt
    $key = md5($cf['security']['secret']);
    // the encryption method
    $algo = 'sha1';

    // test whether encryption method is available
    if (in_array($algo, hash_hmac_algos())) {
        return hash_hmac($algo, $time, $key);
    }

  return $time;
}

/*
 * returns the form field sp_time
 *
 * return html
 */
function newsletterFieldSpamTimeDisplay() {

    $time= time();

    $o = newsletterRenderFormField('sp_time',
                                   'hidden',
                                   $time);
    $o .= newsletterRenderFormField('sp_timehash',
                                    'hidden',
                                    newsletterGenerateTimeHash($time));

    return $o;
}

/*
 * check the form field sp_time
 *
 * return bool
 */
function newsletterFieldSpamTimeCheck() {

    global $cf, $plugin_cf;

    $timeManipulated = false;
    // the key for encrypt
    $key = md5($cf['security']['secret']);
    // the encryption method
    $algo = 'sha1';
    $minTime = (int)$plugin_cf['newsletter']['spam_protection_min_time'];
    $maxTime = (int)$plugin_cf['newsletter']['spam_protection_max_time'];

    $givenTime = isset($_POST['sp_time']) ? $_POST['sp_time'] : '0';
    $givenTimeHash = isset($_POST['sp_timehash']) ? $_POST['sp_timehash'] : '0';

    // check passed value
    if (in_array($algo, hash_hmac_algos())) {
        $hmac = hash_hmac($algo, $_POST['sp_time'], $key);
        // fields was edited?
        if (!hash_equals($_POST['sp_timehash'], $hmac)) {
            $timeManipulated = true;
        }
    } else {
        // fields was edited?
        if ($_POST['sp_time'] != $_POST['sp_timehash']) {
            $timeManipulated = true;
        }
    }
    if ($timeManipulated) {
        $failmessage = 'The Time field has been manipulated.';
        newsletterXH_logMessage($failmessage);
        $ptxPart2 = 'info_spam_suspicious';
        return newsletterRenderErrorMessage($ptxPart2);
    }
    // check whether the transferred value could be checked
    $currentTime = time();
    if (($currentTime - (int)$_POST['sp_time']) <= $minTime
    || ($currentTime - (int)$_POST['sp_time']) >= $maxTime ) {
        $failmessage = 'The submission is outside from the specified time frame.';
        newsletterXH_logMessage($failmessage);
        $ptxPart2 = 'info_spam_suspicious';
        return newsletterRenderErrorMessage($ptxPart2);
    }

    return false;
}
