<?php
$labels = array();

// title
$labels['title'] = 'Send SMS';

// button header
$labels['button'] = 'Send SMS';

// buttons
$labels['check-msisdn'] = 'Test Phone Number';
$labels['send'] = 'Send';
$labels['check-balance'] = 'Account Request';

// head lines
$labels['msisdn'] = 'Phone Number';
$labels['message'] = 'Message';





$messages = array();

// hints
$messages['hint_msisdn'] = 'Add the phone number here.';
$messages['hint_message'] = 'Add the message here. Messages with more than 160 signs will split.';

// all
$messages['result_msisdn'] = 'Phone Number: {0}';
// send
$messages['result_id-and-msisdn'] = 'SMS with ID {0} sent to {1}';
$messages['result_no-of-sms-send'] = 'Sent SMS: {0}';
$messages['result_price'] = 'Used Credit: {0}';
$messages['result_text'] = 'Sent Text: {0}';
// check MSISDN
$messages['result_country'] = 'Country: {0}';
// check balance
$messages['result_account'] = 'Account: {0}';
$messages['result_balance'] = 'Exists Credit: {0}';

// errors from plugin
$messages['error_msisdn'] = 'Please add a phone number.';
$messages['error_message'] = 'Please add a message.';
$messages['error_config_user'] = 'No user name set in config.inc.php[.dist].';
$messages['error_config_password'] = 'No password name set in config.inc.php[.dist].';
$messages['error_msisdn_is_test'] = 'Cannot use test phone number in this case.';

// exceptions from textmagic
$messages['exception-authentication'] = 'Exception from Textmagic: Authentication failed.';
$messages['exception-disabled-account'] = 'Exception from Textmagic: Account is disabled.';
$messages['exception-ipaddress'] = 'Exception from Textmagic: IP Address is not allowed.';
$messages['exception-low-balance'] = 'Exception from Textmagic: Low balance.';
$messages['exception-request-limit'] = 'Exception from Textmagic: Request limit reached.';
$messages['exception-too-long-message'] = 'Exception from Textmagic: Message is too long.';
$messages['exception-too-many-items'] = 'Exception from Textmagic: To many items.';
$messages['exception-unicode-symbols'] = 'Exception from Textmagic: Unicode symbols.';
$messages['exception-unknown-message-id'] = 'Exception from Textmagic: Message ID is unknown.';
$messages['exception-wrong-parameter-value'] = 'Exception from Textmagic: Pameter has a wrong value.';
$messages['exception-wrong-phone-number'] = 'Exception from Textmagic: phone number is wrong.';
$messages['exception-unknown'] = 'Exception from Textmagic: Unknown Exception.';
?>