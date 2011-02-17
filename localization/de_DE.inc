<?php
$labels = array();

// title
$labels['title'] = 'SMS versenden';

// button header
$labels['button'] = 'SMS versenden';

// buttons
$labels['check-msisdn'] = 'Teste Telefonnummer';
$labels['send'] = 'Senden';
$labels['check-balance'] = 'Kontoabfrage';

// head lines
$labels['msisdn'] = 'Telefonnummer';
$labels['message'] = 'Meldung';





$messages = array();

// hints
$messages['hint_msisdn'] = 'Geben Sie hier die Telefonnummer des Empfängers ein.';
$messages['hint_message'] = 'Geben Sie hier Ihre Meldung ein. Meldungen mit mehr als 160 Zeichen werden automatisch in mehreren SMS versendet.';

// all
$messages['result_msisdn'] = 'Telefonnummer: {0}';
// send
$messages['result_id-and-msisdn'] = 'SMS mit ID {0} zu {1} gesendet';
$messages['result_no-of-sms-send'] = 'Versendete SMS: {0}';
$messages['result_price'] = 'Benutztes Guthaben: {0}';
$messages['result_text'] = 'Versendeter Text: {0}';
// check MSISDN
$messages['result_country'] = 'Land: {0}';
// check balance
$messages['result_account'] = 'Account: {0}';
$messages['result_balance'] = 'Existierendes Guthaben: {0}';

// errors from plugin
$messages['error_msisdn'] = 'Bitte geben Sie eine Telefonnummer an.';
$messages['error_message'] = 'Bitte geben Sie eine Meldung an.';
$messages['error_config_user'] = 'Kein Textmagic Nutzername in config.inc.php[.dist] eingetragen.';
$messages['error_config_password'] = 'Kein Textmagic Passwort in config.inc.php[.dist] eingetragen.';
$messages['error_msisdn_is_test'] = 'In diesem Fall kann keine Test-Telefonnummer benutzt werden.';

// exceptions from textmagic
//$messages['exception-authentication'] = '';
//$messages['exception-disabled-account'] = '';
//$messages['exception-ipaddress'] = '';
//$messages['exception-low-balance'] = '';
//$messages['exception-request-limit'] = '';
//$messages['exception-too-lon-message'] = '';
//$messages['exception-too-many-items'] = '';
//$messages['exception-unicode-symbols'] = '';
//$messages['exception-unknown-message-id'] = '';
//$messages['exception-wrong-parameter-value'] = '';
//$messages['exception-wrong-phone-number'] = '';
//$messages['exception-unknown'] = '';
?>