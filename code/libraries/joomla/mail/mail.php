<?php
/**
 * @version		$Id: mail.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	Mail
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('phpmailer.phpmailer');
jimport('joomla.mail.helper');

/**
 * E-Mail Class.  Provides a common interface to send e-mail from the Joomla! Framework
 *
 * @package 	Joomla.Framework
 * @subpackage		Mail
 * @since		1.5
 */
class JMail extends PHPMailer
{

	/**
	 * Constructor
	 *
	 */
	function JMail()
	{
		 // phpmailer has an issue using the relative path for it's language files
		 $this->SetLanguage('joomla', JPATH_LIBRARIES.DS.'phpmailer'.DS.'language'.DS);
	}

	/**
	 * Returns a reference to a global e-mail object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $mail =& JMail::getInstance();</pre>
	 *
	 * NOTE: If you need an instance to use that does not have the global configuration
	 * values, use an id string that is not 'Joomla'.
	 *
	 * @static
	 * @access public
	 * @param string $id The id string for the JMail instance [optional]
	 * @return object The global JMail object
	 * @since 1.5
	 */
	function & getInstance($id = 'Joomla')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (empty ($instances[$id])) {
			$instances[$id] = new JMail();
		}

		return $instances[$id];
	}

	/**
	 * @return mixed True if successful, a JError object otherwise
	 */
	function &Send()
	{
		if ( ( $this->Mailer == 'mail' ) && ! function_exists('mail') )
		{
			return JError::raiseNotice( 500, JText::_('MAIL_FUNCTION_DISABLED') );
		}

		@ $result = parent::Send();

		if ($result == false)
		{
			// TODO: Set an appropriate error number
			$result =& JError::raiseNotice( 500, JText::_($this->ErrorInfo) );
		}
		return $result;
	}

	/**
	 * Set the E-Mail sender
	 *
	 * @access public
	 * @param array $from E-Mail address and Name of sender
	 * 		<pre>
	 * 			array( [0] => E-Mail Address [1] => Name )
	 * 		</pre>
	 * @return void
	 * @since 1.5
	 */
	function setSender($from)
	{
		// If $from is an array we assume it has an address and a name
		if (is_array($from))
		{
			$this->From 	= JMailHelper::cleanLine( $from[0] );
			$this->FromName = JMailHelper::cleanLine( $from[1] );
		// If it is a string we assume it is just the address
		} elseif (is_string($from)) {
			$this->From = JMailHelper::cleanLine( $from );
		// If it is neither, we throw a warning
		} else {
			JError::raiseWarning( 0, "JMail::  Invalid E-Mail Sender: $from", "JMail::setSender($from)");
		}
	}

	/**
	 * Set the E-Mail subject
	 *
	 * @access public
	 * @param string $subject Subject of the e-mail
	 * @return void
	 * @since 1.5
	 */
	function setSubject($subject) {
		$this->Subject = JMailHelper::cleanLine( $subject );
	}

	/**
	 * Set the E-Mail body
	 *
	 * @access public
	 * @param string $content Body of the e-mail
	 * @return void
	 * @since 1.5
	 */
	function setBody($content)
	{
		/*
		 * Filter the Body
		 * TODO: Check for XSS
		 */
		$this->Body = JMailHelper::cleanText( $content );
	}

	/**
	 * Add recipients to the email
	 *
	 * @access public
	 * @param mixed $recipient Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addRecipient($recipient)
	{
		// If the recipient is an aray, add each recipient... otherwise just add the one
		if (is_array($recipient))
		{
			foreach ($recipient as $to) {
				$to = JMailHelper::cleanLine( $to );
				$this->AddAddress($to);
			}
		} else {
			$recipient = JMailHelper::cleanLine( $recipient );
			$this->AddAddress($recipient);
		}
	}

	/**
	 * Add carbon copy recipients to the email
	 *
	 * @access public
	 * @param mixed $cc Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addCC($cc)
	{
		//If the carbon copy recipient is an aray, add each recipient... otherwise just add the one
		if (isset ($cc))
		{
			if (is_array($cc)) {
				foreach ($cc as $to) {
					$to = JMailHelper::cleanLine( $to );
					parent::AddCC($to);
				}
			} else {
				$cc = JMailHelper::cleanLine( $cc );
				parent::AddCC($cc);
			}
		}
	}

	/**
	 * Add blind carbon copy recipients to the email
	 *
	 * @access public
	 * @param mixed $cc Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addBCC($bcc)
	{
		// If the blind carbon copy recipient is an aray, add each recipient... otherwise just add the one
		if (isset ($bcc))
		{
			if (is_array($bcc)) {
				foreach ($bcc as $to) {
					$to = JMailHelper::cleanLine( $to );
					parent::AddBCC($to);
				}
			} else {
				$bcc = JMailHelper::cleanLine( $bcc );
				parent::AddBCC($bcc);
			}
		}
	}

	/**
	 * Add file attachments to the email
	 *
	 * @access public
	 * @param mixed $attachment Either a string or array of strings [filenames]
	 * @return void
	 * @since 1.5
	 */
	function addAttachment($attachment)
	{
		// If the file attachments is an aray, add each file... otherwise just add the one
		if (isset ($attachment))
		{
			if (is_array($attachment)) {
				foreach ($attachment as $file) {
					parent::AddAttachment($file);
				}
			} else {
				parent::AddAttachment($attachment);
			}
		}
	}

	/**
	 * Add Reply to e-mail address(es) to the e-mail
	 *
	 * @access public
	 * @param array $reply Either an array or multi-array of form
	 * 		<pre>
	 * 			array( [0] => E-Mail Address [1] => Name )
	 * 		</pre>
	 * @return void
	 * @since 1.5
	 */
	function addReplyTo($replyto)
	{
		// Take care of reply email addresses
		if (is_array($replyto[0]))
		{
			foreach ($replyto as $to) {
				$to0 = JMailHelper::cleanLine( $to[0] );
				$to1 = JMailHelper::cleanLine( $to[1] );
				parent::AddReplyTo($to0, $to1);
			}
		} else {
			$replyto0 = JMailHelper::cleanLine( $replyto[0] );
			$replyto1 = JMailHelper::cleanLine( $replyto[1] );
			parent::AddReplyTo($replyto0, $replyto1);
		}
	}

	/**
	 * Use sendmail for sending the e-mail
	 *
	 * @access public
	 * @param string $sendmail Path to sendmail [optional]
	 * @return boolean True on success
	 * @since 1.5
	 */
	function useSendmail($sendmail = null)
	{
		$this->Sendmail = $sendmail;

		if (!empty ($this->Sendmail)) {
			$this->IsSendmail();
			return true;
		} else {
			$this->IsMail();
			return false;
		}
	}

	/**
	 * Use SMTP for sending the e-mail
	 *
	 * @access public
	 * @param string $auth SMTP Authentication [optional]
	 * @param string $host SMTP Host [optional]
	 * @param string $user SMTP Username [optional]
	 * @param string $pass SMTP Password [optional]
	 * @param string $secure SMTP Secure ssl,tls [optinal]
	 * @param string $port SMTP Port [optional]
	 * @return boolean True on success
	 * @since 1.5
	 */
	function useSMTP($auth = null, $host = null, $user = null, $pass = null,$secure = null, $port = 25)
	{
		$this->SMTPAuth = $auth;
		$this->Host 	= $host;
		$this->Username = $user;
		$this->Password = $pass;
		$this->Port     = $port;
		if ($secure == 'ssl' || $secure == 'tls') {
			$this->SMTPSecure = $secure;
		}

		if ($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null) {
			$this->IsSMTP();
		}
	}
}
