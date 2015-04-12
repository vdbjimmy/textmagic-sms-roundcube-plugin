## Description ##

A roundcube plugin to send SMS with your textmagic account from TextMagic. This project use the [textmagic-sms-api-php](http://code.google.com/p/textmagic-sms-api-php/) project to have access to TextMagic.

  * **The project contains no stable version at the time.**
  * **At the time the all users use the global TextMagic account configuration! See [Issue 1](http://code.google.com/p/textmagic-sms-roundcube-plugin/issues/detail?id=1)**
  * **[Open Issues](http://code.google.com/p/textmagic-sms-roundcube-plugin/issues/list)**

## Quick Start Guide ##

  * Download an configure http://www.roundcube.net/
  * Create an account at http://www.textmagic.com/
  * Checkout latest source code, the plugin name have to be _textmagic\_sms_
```
svn checkout http://textmagic-sms-roundcube-plugin.googlecode.com/svn/trunk/ textmagic_sms
```
  * Checkout the [textmagic-sms-api-php](http://code.google.com/p/textmagic-sms-api-php/) project in the textmagic\_sms folder which creates the api folder, see _checkout-api.sh_
```
svn checkout http://textmagic-sms-api-php.googlecode.com/svn/trunk/ api
```
  * activate plugin, see [Roundcube Doc](http://trac.roundcube.net/wiki/Doc_Plugins)
  * copy config/config.inc.php.dist to config/config.inc.php and modify config.inc.php
    * 'textmagic\_login' is your TextMagic user
    * 'textmagic\_apipassword' is your Textmagic api password (generate in TextMagic administer view)
    * 'textmagic\_defaultMsisdnPrefix' is the prefix which will replaced the leading zeros from the input phone number to build an international phone number _(example with german number: 0173 1234567 will changed to 49173 1234567)_
  * Test with a phone number stated with '999'. This is a TextMagic test number without balance decrease