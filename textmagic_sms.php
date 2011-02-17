<?php

require_once 'api/TextMagicAPI.php';

/**
 * Plugin to send SMS with Textmagic <a href="http://www.textmagic.com/">http://www.textmagic.com/</a>.
 * You need an account from Textmagic and configurate in config.inc.php[.dist] file.
 *
 * TODO
 * <ul>
 * <li>config in config page to use different configs</li>
 * <li>valid HTML</li>
 * <li>clarify license</li>
 * </ul>
 *
 * @author Ariel Küchler <ariel@kuechler.info>
 *
 */
class textmagic_sms extends rcube_plugin
{
	const PLUGIN_NAME = 'textmagic_sms';

	const PARAM_MSISDN = 'param_msisdn';
	const PARAM_MESSAGE = 'param_message';

	const METHOD_SEND = 'method_send';
	const METHOD_CHECK = 'method_check';
	const METHOD_BALANCE = 'method_balance';
	private $PARAMS_METHOD = array(self::METHOD_SEND => 'send', self::METHOD_CHECK => 'checkMsisdn', self::METHOD_BALANCE => 'balance');

	const TEST_MSISDN_PREFIX = '999';

	public $task = '?(?!login).*';
	private $rcmail;
	private $result = '';
	private $error = '';

	function init()
	{
		$rcmail = rcmail::get_instance();
		$this->rcmail = $rcmail;

		if(file_exists('./plugins/' . self::PLUGIN_NAME . '/config/config.inc.php')) {
			$this->load_config('config/config.inc.php');
		} else {
			$this->load_config('config/config.inc.php.dist');
		}

		$this->add_texts('localization/', false);

		$this->register_action('plugin.' . self::PLUGIN_NAME, array($this, 'startup'));
		$this->register_action('plugin.' . self::PLUGIN_NAME . '_send', array($this, 'run'));

		$this->add_button(array(
      'name'    => 'SMS',
	  'class'   => 'textmagic-sms-button',
      'label'   => self::PLUGIN_NAME . '.button',
      'href'    => $rcmail->url(array('_action' => 'plugin.' . self::PLUGIN_NAME, '_task' => 'dummy'))
		), 'taskbar');

		$skin = $rcmail->config->get('skin');
		if(file_exists("./plugins/' . self::PLUGIN_NAME . '/ . $skin . /sms.css")) {
			$this->include_stylesheet('skins/' . $skin . '/sms.css');
		}else {
			$this->include_stylesheet('skins/default/sms.css');
		}
	}

	function startup() {
		$rcmail = $this->rcmail;
		$this->register_handler('plugin.body', array($this, 'printForm'));
		$rcmail->output->set_pagetitle($this->gettext('title'));
		$rcmail->output->send('plugin');
	}

	function run() {
		$rcmail = $this->rcmail;
		foreach($this->PARAMS_METHOD as $param => $method) {
			$pa = $this->getParameter($param);
			if (isset($pa)) {
				//$this->log('call ' . $method);
				$this->$method();
			}
		}
		$this->register_handler('plugin.body', array($this, 'printForm'));
		$rcmail->output->set_pagetitle($this->gettext('title'));
		$rcmail->output->send('plugin');
	}

	function balance() {
		$rcmail = $this->rcmail;

		try {
			$api = $this->getApi();
			if ($api != null) {
				$results = $api->getBalance();
				$this->log('check balance: ' . $results);
				$api_user = $rcmail->config->get('textmagic_login');
				$this->printResultHeader($this->gettext('result_account', $api_user));
				$this->printResultValue($this->gettext('result_balance', $results));
			}
		}catch (Exception $e) {
			$this->addErrorByException($e);
		}
	}

	function checkMsisdn() {
		$rcmail = $this->rcmail;

		$msisdn = $this->getParameter(self::PARAM_MSISDN);
		$msisdn = $this->normalizeMsisdn($msisdn);
		if(empty($msisdn)){
			$this->addError($this->gettext('error_msisdn'));
		}
		if ($this->isTestMsisdn($msisdn)) {
			$this->addError($this->gettext('error_msisdn_is_test'));
		}

		try {
			if(!$this->isError()){
				$api = $this->getApi();
				if ($api != null) {
					$result = $api->checkNumber(array($msisdn));
					$this->log('check MSISDN: ' . print_r($result, true));
					foreach ($result as $x_msisdn => $x_data) {
						$result_price = $x_data['price'];
						$result_country = $x_data['country'];
						$this->printResultHeader($this->gettext('result_msisdn', $x_msisdn));
						$this->printResultValue($this->gettext('result_price',  $result_price));
						$this->printResultValue($this->gettext('result_country', $result_country));
					}
				}
			}
		}catch (Exception $e) {
			$this->addErrorByException($e);
		}
	}

	function send() {
		$rcmail = $this->rcmail;

		$message = $this->getParameter(self::PARAM_MESSAGE);
		$msisdn =  $this->getParameter(self::PARAM_MSISDN);

		if(empty($message)){
			$this->addError($this->gettext('error_message'));
		}
		$msisdn = $this->normalizeMsisdn($msisdn);
		if(empty($msisdn)){
			$this->addError($this->gettext('error_msisdn'));
		}

		try {
			if(!$this->isError()){
				//$this->log($message);

				$phones = array($msisdn);
				$is_unicode = true;

				$api = $this->getApi();
				if ($api != null) {
					$results = $api->send($message, $phones, $is_unicode);

					$result_id = $results['messages'];
					$result_text = $results['sent_text'];
					$result_count = $results['parts_count'];

					$this->log('sent: ' . print_r($result_id, true) . " $result_text no_of_sms: $result_count !");

					$this->printResultHeader($this->gettext('result_msisdn', $msisdn));
					if ($result_id != null) {
						foreach ($result_id as $x_ids=>$x_msisdn) {
							$this->printResultValue($this->gettext('result_id-and-msisdn', array($x_ids, $x_msisdn)));
						}
					}
					$this->printResultValue($this->gettext('result_text', $result_text));
					$this->printResultValue($this->gettext('result_no-of-sms-send', $result_count));
				}
			}
		}catch (Exception $e) {
			$this->addErrorByException($e);
		}
	}

	private function printResultHeader ($text) {
		$this->addResult(html::span('textmagic-sms-result textmagic-sms-header', Q($text)));
	}

	private function printResultValue ($text) {
		$this->addResult(html::span('textmagic-sms-result textmagic-sms-result-value', Q($text)));		
	}

	function printForm()
	{
		$rcmail = $this->rcmail;
		
		$msisdn = new html_inputfield(array('name' => self::PARAM_MSISDN));
		$msisdn_hint = html::p('hint', Q($this->gettext('hint_msisdn')));
		$header_msisdn = html::p(array('class' => 'textmagic-sms-header'), Q($this->gettext('msisdn')));

		$message = new html_textarea(array('cols' => 80, 'rows' => 10, 'name' => self::PARAM_MESSAGE));
		$message_hint = html::p('hint', Q($this->gettext('hint_message')));
		$message_input = $message->show(Q(self::$this->getParameter(self::PARAM_MESSAGE, $rcmail->config->get('default-message'))));
		$message_header = html::p(array('class' => 'textmagic-sms-header'), Q($this->gettext('message')));


		$button_check = html::tag('div', array('class' => 'formbuttons'),
		$msisdn->show(Q(self::$this->getParameter(self::PARAM_MSISDN, $rcmail->config->get('default-msisdn')))) .
		html::tag('input', array('type' => 'submit', 'name' => self::METHOD_CHECK,
	              'class' => 'button textmagic-sms-check-msisdn-button', 'value' => $this->gettext('check-msisdn')))
		);

		$button_send = html::tag('p', array('class' => 'formbuttons'),
		html::tag('input', array('type' => 'submit', 'name' => self::METHOD_SEND,
	              'class' => 'button', 'value' => $this->gettext('send')))		
		);

		$button_balance = 		html::tag('p', array('class' => 'formbuttons'),
		html::tag('input', array('type' => 'submit', 'name' => self::METHOD_BALANCE,
	              'class' => 'button', 'value' => $this->gettext('check-balance')))
		);

		$form = html::tag('form', array(
            'action' => $rcmail->url('plugin.' . self::PLUGIN_NAME . '_send'),
            'method' => 'post'),  

		$header_msisdn . $msisdn_hint . $button_check .
		$message_header . $message_hint . $message_input .

		$button_send .
		$button_balance
		);

		$result = '';
		if ($this->hasResult()) {
			$result = html::p('textmagic-sms-output', $this->result);
		}

		$error = '';
		if ($this->isError()) {
			$error = html::p('textmagic-sms-error', $this->error);
		}

		return $error . $result . $form;
	}

	private function addErrorByException($exception) {
		if ($exception instanceof AuthenticationException) {
			$this->addError($this->gettext('exception-authentication'));
		}else if ($exception instanceof DisabledAccountException) {
			$this->addError($this->gettext('exception-disabled-account'));
		}else if ($exception instanceof IPAddressException) {
			$this->addError($this->gettext('exception-ipaddress'));
		}else if ($exception instanceof LowBalanceException) {
			$this->addError($this->gettext('exception-low-balance'));
		}else if ($exception instanceof RequestsLimitExceededException) {
			$this->addError($this->gettext('exception-request-limit'));
		}else if ($exception instanceof TooLongMessageException) {
			$this->addError($this->gettext('exception-too-long-message'));
		}else if ($exception instanceof TooManyItemsException) {
			$this->addError($this->gettext('exception-too-many-items'));
		}else if ($exception instanceof UnicodeSymbolsDetectedException) {
			$this->addError($this->gettext('exception-unicode-symbols'));
		}else if ($exception instanceof UnknownMessageIdException) {
			$this->addError($this->gettext('exception-unknown-message-id'));
		}else if ($exception instanceof WrongParameterValueException) {
			$this->addError($this->gettext('exception-wrong-parameter-value'));
		}else if ($exception instanceof WrongPhoneFormatException) {
			$this->addError($this->gettext('exception-wrong-phone-number'));
		}else {
			$this->addError($this->gettext('exception-unknown'));
		}
	}

	private function addError($message) {
		$this->error .= $message;
	}

	private function isError() {
		return !empty($this->error);
	}

	private function addResult($message) {
		$this->result .= $message;
	}

	private function hasResult() {
		return !empty($this->result);
	}

	private function isTestMsisdn($msisdn) {
		return strncmp(self::TEST_MSISDN_PREFIX, $msisdn, strlen(self::TEST_MSISDN_PREFIX)) == 0;
	}

	private function normalizeMsisdn($msisdn) {
		$rcmail = $this->rcmail;
		$prefix = $rcmail->config->get('textmagic_defaultMsisdnPrefix');
		$hasZeros = false;

		// removing non-digits
		$msisdn = preg_replace('/[^\d]+/', '', $msisdn);

		// remove '0's at the begining
		while (strlen($msisdn) >= 1) {
			if (strncmp($msisdn, '0', strlen('0')) == 0) {
				$msisdn = substr($msisdn, 1);
				$hasZeros = true;
			}else {
				break;
			}
		}

		if ($hasZeros
		// if no test MSISDN
		&&  !$this->isTestMsisdn($msisdn)
		// add prefix if not at the begining
		&& strncmp($prefix, $msisdn, strlen($prefix)) != 0) {
			$msisdn = $prefix . $msisdn;
		}
		if (strlen($msisdn) >= 9/*?*/) {
			return $msisdn;
		}else {
			return null;
		}
	}

	private function getApi() {
		$rcmail = $this->rcmail;
		$api_user = $rcmail->config->get('textmagic_login');
		$api_password = $rcmail->config->get('textmagic_apipassword');

		if(empty($api_user)){
			$this->addError($this->gettext('error_config_user'));
		}
		if(empty($api_password)){
			$this->addError($this->gettext('error_config_password'));
		}

		if(!$this->isError()){
			$api = new TextMagicAPI(array(
    		"username" => $api_user,
    		"password" => $api_password,
			));
		}
		return $api;
	}

	static function getParameter($paramName, $default=null) {
		$pa = get_input_value($paramName, RCUBE_INPUT_POST, true);
		if (empty($pa)) {
			return $default;
		}else {
			return $pa;
		}
	}

	function gettext($key, $param=null) {
		$result = parent::gettext($key);

		// make array if not
		if ($param == null) {
			$param = array();
		}
		if (!is_array($param)) {
			$param = array($param);
		}

		// build replacement pattern
		$i=0;
		$pattern = array();
		$replacement = array();
		foreach (array_values($param) as $val) {
			$pattern[$i] = "/\{$i\}/";
			$replacement[$i] = $val;
			$i++;
		}

		// add pattern to remove not defined stuff
		$pattern[$i] = '/\{\\d+\}/';
		$replacement[$i] = '';
		//$this->log(print_r($pattern, true) . '-' . print_r($replacement, true));

		// replace
		return preg_replace($pattern, $replacement, $result);
	}

	function log($message) {
		$rcmail = $this->rcmail;

		$api_user = $rcmail->config->get('textmagic_login');

		$msisdn = $this->getParameter(self::PARAM_MSISDN);
		$msisdn = $this->normalizeMsisdn($msisdn);

		write_log('info', '[' . self::PLUGIN_NAME . "][$api_user][$msisdn] $message");
	}
}
?>