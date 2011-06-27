<?php
/**
* @version		$Id: emailcloak.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'plgContentEmailCloak');

/**
 * Plugin that cloaks all emails in content from spambots via Javascript.
 *
 * @param object|string An object with a "text" property or the string to be
 * cloaked.
 * @param array Additional parameters. See {@see plgEmailCloak()}.
 * @param int Optional page number. Unused. Defaults to zero.
 * @return boolean True on success.
 */
function plgContentEmailCloak(&$row, &$params, $page=0)
{
	if (is_object($row)) {
		return plgEmailCloak($row->text, $params);
	}
	return plgEmailCloak($row, $params);
}

/**
 * Genarate a search pattern based on link and text.
 *
 * @param string The target of an e-mail link.
 * @param string The text enclosed by the link.
 * @return string A regular expression that matches a link containing the
 * parameters.
 */
function plgContentEmailCloak_searchPattern ($link, $text) {
	// <a href="mailto:anyLink">anyText</a>
	$pattern = '~(?:<a [\w "\'=\@\.\-]*href\s*=\s*"(mailto:|https?://(?:[a-z0-9][a-z0-9\-]*[a-z0-9]\.)*(?:[a-z0-9]+)(?::\d+)?[a-z0-9;/\?:\@&=+\$,\-_\.!\~*\'\(\)%]+?%3C)'
		. $link . '(%3E)?"([\w "\'=\@\.\-]*))>' . $text . '</a>~i';

	return $pattern;
}

/**
 * Cloak all emails in text from spambots via Javascript.
 *
 * @param string The string to be cloaked.
 * @param array Additional parameters. Parameter "mode" (integer, default 1)
 * replaces addresses with "mailto:" links if nonzero.
 * @return boolean True on success.
 */
function plgEmailCloak(&$text, &$params)
{

	/*
	 * Check for presence of {emailcloak=off} which is explicits disables this
	 * bot for the item.
	 */
	if (JString::strpos($text, '{emailcloak=off}') !== false) {
		$text = JString::str_ireplace('{emailcloak=off}', '', $text);
		return true;
	}

	// Simple performance check to determine whether bot should process further.
	if (JString::strpos($text, '@') === false) {
		return true;
	}

	$plugin = & JPluginHelper::getPlugin('content', 'emailcloak');

	// Load plugin params info
	$pluginParams = new JParameter($plugin->params);
	$mode = $pluginParams->def('mode', 1);

	// split the string into parts to exclude strcipt tags from being handled
	$text = explode( '<script', $text );
	foreach ( $text as $i => $str ) {
		if ( $i == 0 ) {
			plgEmailCloakString( $text[$i], $mode );
		} else {
			$str_split = explode( '</script>', $str );
			foreach ( $str_split as $j => $str_split_part ) {
				if ( ( $j % 2 ) == 1 ) {
					plgEmailCloakString( $str_split[$i], $mode );
				}
			}
			$text[$i] = implode( '</script>', $str_split );
		}
	}
	$text = implode( '<script', $text );
	return true;
}

/**
 * Cloak all emails in text from spambots via Javascript.
 *
 * @param string The string to be cloaked.
 * @param string The mode.
 * replaces addresses with "mailto:" links if nonzero.
 * @return boolean True on success.
 */
function plgEmailCloakString(&$text, $mode = 1)
{
	// Simple performance check to determine whether bot should process further.
	if (JString::strpos($text, '@') === false) {
		return true;
	}


	// any@email.address.com
	$searchEmail = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';
	// any@email.address.com?subject=anyText
	$searchEmailLink = $searchEmail . '([?&][\x20-\x7f][^"<>]+)';
	// anyText
	$searchText = '((?:[\x20-\x7f]|[\xA1-\xFF]|[\xC2-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF4][\x80-\xBF]{3})[^<>]+)';

	//$searchText = '(+)';
	//Any Image link
	$searchImage	=	"(<img[^>]+>)";

	/*
	 * Search for derivatives of link code <a href="mailto:email@amail.com"
	 * >email@amail.com</a>
	 */
	$pattern = plgContentEmailCloak_searchPattern($searchEmail, $searchEmail);
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
		$mail = $regs[2][0];
		$mailText = $regs[3][0];

		// Check to see if mail text is different from mail addy
		$replacement = JHTML::_('email.cloak', $mail, $mode, $mailText);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
	}

	/*
	 * Search for derivatives of link code <a href="mailto:email@amail.com">
	 * anytext</a>
	 */
	$pattern = plgContentEmailCloak_searchPattern($searchEmail, $searchText);
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
        $prefix = $regs[1][0];
		$mail = $regs[2][0];
        $suffix = $regs[3][0];
        $attribs = $regs[4][0];
		$mailText = $regs[5][0];

		$replacement = JHTML::_('email.cloak', $mail, $mode, $mailText, 0, $prefix, $suffix, $attribs);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
	}

	/*
	 * Search for derivatives of link code <a href="mailto:email@amail.com">
	 * <img anything></a>
	 */
	$pattern = plgContentEmailCloak_searchPattern($searchEmail, $searchImage);
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
		$prefix = $regs[1][0];
		$mail = $regs[2][0];
		$suffix = $regs[3][0];
		$attribs = $regs[4][0];
		$mailText = $regs[5][0];

		$replacement = JHTML::_('email.cloak', $mail, $mode, $mailText, 0, $prefix, $suffix, $attribs);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
	}

	/*
	 * Search for derivatives of link code <a href="mailto:email@amail.com?
	 * subject=Text">email@amail.com</a>
	 */
	$pattern = plgContentEmailCloak_searchPattern($searchEmailLink, $searchEmail);
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
		$mail = $regs[2][0] . $regs[3][0];
		$mailText = $regs[6][0];
		// Needed for handling of Body parameter
		$mail = str_replace( '&amp;', '&', $mail );

		// Check to see if mail text is different from mail addy
		$replacement = JHTML::_('email.cloak', $mail, $mode, $mailText);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
	}

	/*
	 * Search for derivatives of link code <a href="mailto:email@amail.com?
	 * subject=Text">anytext</a>
	 */
	$pattern = plgContentEmailCloak_searchPattern($searchEmailLink, $searchText);
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
		$mail = $regs[2][0] . $regs[3][0];
		$mailText = $regs[6][0];
		// Needed for handling of Body parameter
		$mail = str_replace('&amp;', '&', $mail);

		$replacement = JHTML::_('email.cloak', $mail, $mode, $mailText, 0);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
	}

	// Search for plain text email@amail.com
	$pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
	while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
		$mail = $regs[1][0];
		$replacement = JHTML::_('email.cloak', $mail, $mode);

		// Replace the found address with the js cloaked email
		$text = substr_replace($text, $replacement, $regs[1][1], strlen($mail));
	}
	return true;
}
