<?php
/**
* @version		$Id: string.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Utilities
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

/**
 * PHP mbstring and iconv local configuration
 */
// check if mbstring extension is loaded and attempt to load it if not present except for windows
if (extension_loaded('mbstring') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('mbstring.so')))) {
	//Make sure to surpress the output in case ini_set is disabled
	@ini_set('mbstring.internal_encoding', 'UTF-8');
	@ini_set('mbstring.http_input', 'UTF-8');
	@ini_set('mbstring.http_output', 'UTF-8');
}

// same for iconv
if (function_exists('iconv') || ((!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && dl('iconv.so')))) {
   	// these are settings that can be set inside code
	iconv_set_encoding("internal_encoding", "UTF-8");
	iconv_set_encoding("input_encoding", "UTF-8");
	iconv_set_encoding("output_encoding", "UTF-8");
}

/**
 * Include the utf8 package
 */
require_once(JPATH_LIBRARIES.DS.'phputf8'.DS.'utf8.php');

/**
 * String handling class for utf-8 data
 * Wraps the phputf8 library
 * All functions assume the validity of utf-8 strings.
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	Utilities
 * @since		1.5
 */
class JString
{
	/**
	 * UTF-8 aware alternative to strpos
	 * Find position of first occurrence of a string
	 *
	 * @static
	 * @access public
	 * @param $str - string String being examined
	 * @param $search - string String being searced for
	 * @param $offset - int Optional, specifies the position from which the search should be performed
	 * @return mixed Number of characters before the first match or FALSE on failure
	 * @see http://www.php.net/strpos
	 */
	function strpos($str, $search, $offset = FALSE)
	{
		if ( $offset === FALSE ) {
			return utf8_strpos($str, $search);
		} else {
			return utf8_strpos($str, $search, $offset);
		}
	}

	/**
	 * UTF-8 aware alternative to strrpos
	 * Finds position of last occurrence of a string
	 *
	 * @static
	 * @access public
	 * @param $str - string String being examined
	 * @param $search - string String being searced for
	 * @return mixed Number of characters before the last match or FALSE on failure
	 * @see http://www.php.net/strrpos
	 */
	function strrpos($str, $search){
		return utf8_strrpos($str, $search);
	}

	/**
	 * UTF-8 aware alternative to substr
	 * Return part of a string given character offset (and optionally length)
	 *
	 * @static
	 * @access public
	 * @param string
	 * @param integer number of UTF-8 characters offset (from left)
	 * @param integer (optional) length in UTF-8 characters from offset
	 * @return mixed string or FALSE if failure
	 * @see http://www.php.net/substr
	 */
	function substr($str, $offset, $length = FALSE)
	{
		if ( $length === FALSE ) {
			return utf8_substr($str, $offset);
		} else {
			return utf8_substr($str, $offset, $length);
		}
	}

	/**
	 * UTF-8 aware alternative to strtlower
	 * Make a string lowercase
	 * Note: The concept of a characters "case" only exists is some alphabets
	 * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
	 * not exist in the Chinese alphabet, for example. See Unicode Standard
	 * Annex #21: Case Mappings
	 *
	 * @access public
	 * @param string
	 * @return mixed either string in lowercase or FALSE is UTF-8 invalid
	 * @see http://www.php.net/strtolower
	 */
	function strtolower($str){
		return utf8_strtolower($str);
	}

	/**
	 * UTF-8 aware alternative to strtoupper
	 * Make a string uppercase
	 * Note: The concept of a characters "case" only exists is some alphabets
	 * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
	 * not exist in the Chinese alphabet, for example. See Unicode Standard
	 * Annex #21: Case Mappings
	 *
	 * @access public
	 * @param string
	 * @return mixed either string in uppercase or FALSE is UTF-8 invalid
	 * @see http://www.php.net/strtoupper
	 */
	function strtoupper($str){
		return utf8_strtoupper($str);
	}

	/**
	 * UTF-8 aware alternative to strlen
	 * Returns the number of characters in the string (NOT THE NUMBER OF BYTES),
	 *
	 * @access public
	 * @param string UTF-8 string
	 * @return int number of UTF-8 characters in string
	 * @see http://www.php.net/strlen
	 */
	function strlen($str){
		return utf8_strlen($str);
	}

	/**
	 * UTF-8 aware alternative to str_ireplace
	 * Case-insensitive version of str_replace
	 *
	 * @static
	 * @access public
	 * @param string string to search
	 * @param string existing string to replace
	 * @param string new string to replace with
	 * @param int optional count value to be passed by referene
	 * @see http://www.php.net/str_ireplace
	*/
	function str_ireplace($search, $replace, $str, $count = NULL)
	{
		jimport('phputf8.str_ireplace');
		if ( $count === FALSE ) {
			return utf8_ireplace($search, $replace, $str);
		} else {
			return utf8_ireplace($search, $replace, $str, $count);
		}
	}

	/**
	 * UTF-8 aware alternative to str_split
	 * Convert a string to an array
	 *
	 * @static
	 * @access public
	 * @param string UTF-8 encoded
	 * @param int number to characters to split string by
	 * @return array
	 * @see http://www.php.net/str_split
	*/
	function str_split($str, $split_len = 1)
	{
		jimport('phputf8.str_split');
		return utf8_str_split($str, $split_len);
	}

	/**
	 * UTF-8 aware alternative to strcasecmp
	 * A case insensivite string comparison
	 *
	 * @static
	 * @access public
	 * @param string string 1 to compare
	 * @param string string 2 to compare
	 * @return int < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
	 * @see http://www.php.net/strcasecmp
	*/
	function strcasecmp($str1, $str2)
	{
		jimport('phputf8.strcasecmp');
		return utf8_strcasecmp($str1, $str2);
	}

	/**
	 * UTF-8 aware alternative to strcspn
	 * Find length of initial segment not matching mask
	 *
	 * @static
	 * @access public
	 * @param string
	 * @param string the mask
	 * @param int Optional starting character position (in characters)
	 * @param int Optional length
	 * @return int the length of the initial segment of str1 which does not contain any of the characters in str2
	 * @see http://www.php.net/strcspn
	*/
	function strcspn($str, $mask, $start = NULL, $length = NULL)
	{
		jimport('phputf8.strcspn');
		if ( $start === FALSE && $length === FALSE ) {
			return utf8_strcspn($str, $mask);
		} else if ( $length === FALSE ) {
			return utf8_strcspn($str, $mask, $start);
		} else {
			return utf8_strcspn($str, $mask, $start, $length);
		}
	}

	/**
	 * UTF-8 aware alternative to stristr
	 * Returns all of haystack from the first occurrence of needle to the end.
	 * needle and haystack are examined in a case-insensitive manner
	 * Find first occurrence of a string using case insensitive comparison
	 *
	 * @static
	 * @access public
	 * @param string the haystack
	 * @param string the needle
	 * @return string the sub string
	 * @see http://www.php.net/stristr
	*/
	function stristr($str, $search)
	{
		jimport('phputf8.stristr');
		return utf8_stristr($str, $search);
	}

	/**
	 * UTF-8 aware alternative to strrev
	 * Reverse a string
	 *
	 * @static
	 * @access public
	 * @param string String to be reversed
	 * @return string The string in reverse character order
	 * @see http://www.php.net/strrev
	*/
	function strrev($str)
	{
		jimport('phputf8.strrev');
		return utf8_strrev($str);
	}

	/**
	 * UTF-8 aware alternative to strspn
	 * Find length of initial segment matching mask
	 *
	 * @static
	 * @access public
	 * @param string the haystack
	 * @param string the mask
	 * @param int start optional
	 * @param int length optional
	 * @see http://www.php.net/strspn
	*/
	function strspn($str, $mask, $start = NULL, $length = NULL)
	{
		jimport('phputf8.strspn');
		if ( $start === FALSE && $length === FALSE ) {
			return utf8_strspn($str, $mask);
		} else if ( $length === FALSE ) {
			return utf8_strspn($str, $mask, $start);
		} else {
			return utf8_strspn($str, $mask, $start, $length);
		}
	}

	/**
	 * UTF-8 aware substr_replace
	 * Replace text within a portion of a string
	 *
	 * @static
	 * @access public
	 * @param string the haystack
	 * @param string the replacement string
	 * @param int start
	 * @param int length (optional)
	 * @see http://www.php.net/substr_replace
	*/
	function substr_replace($str, $repl, $start, $length = NULL )
	{
		// loaded by library loader
		if ( $length === FALSE ) {
			return utf8_substr_replace($str, $repl, $start);
		} else {
			return utf8_substr_replace($str, $repl, $start, $length);
		}
	}

	/**
	 * UTF-8 aware replacement for ltrim()
	 * Strip whitespace (or other characters) from the beginning of a string
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise ltrim will
	 * work normally on a UTF-8 string
	 *
	 * @static
	 * @access public
	 * @param string the string to be trimmed
	 * @param string the optional charlist of additional characters to trim
	 * @return string the trimmed string
	 * @see http://www.php.net/ltrim
	*/
	function ltrim( $str, $charlist = FALSE )
	{
		jimport('phputf8.trim');
		if ( $charlist === FALSE ) {
			return utf8_ltrim( $str );
		} else {
			return utf8_ltrim( $str, $charlist );
		}
	}

	/**
	 * UTF-8 aware replacement for rtrim()
	 * Strip whitespace (or other characters) from the end of a string
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise rtrim will
	 * work normally on a UTF-8 string
	 *
	 * @static
	 * @access public
	 * @param string the string to be trimmed
	 * @param string the optional charlist of additional characters to trim
	 * @return string the trimmed string
	 * @see http://www.php.net/rtrim
	*/
	function rtrim( $str, $charlist = FALSE )
	{
		jimport('phputf8.trim');
		if ( $charlist === FALSE ) {
			return utf8_rtrim($str);
		} else {
			return utf8_rtrim( $str, $charlist );
		}
	}

	/**
	 * UTF-8 aware replacement for trim()
	 * Strip whitespace (or other characters) from the beginning and end of a string
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise trim will
	 * work normally on a UTF-8 string
	 *
	 * @static
	 * @access public
	 * @param string the string to be trimmed
	 * @param string the optional charlist of additional characters to trim
	 * @return string the trimmed string
	 * @see http://www.php.net/trim
	*/
	function trim( $str, $charlist = FALSE )
	{
		jimport('phputf8.trim');
		if ( $charlist === FALSE ) {
			return utf8_trim( $str );
		} else {
			return utf8_trim( $str, $charlist );
		}
	}

	/**
	 * UTF-8 aware alternative to ucfirst
	 * Make a string's first character uppercase
	 *
	 * @static
	 * @access public
	 * @param string
	 * @return string with first character as upper case (if applicable)
	 * @see http://www.php.net/ucfirst
	*/
	function ucfirst($str)
	{
		jimport('phputf8.ucfirst');
		return utf8_ucfirst($str);
	}

	/**
	 * UTF-8 aware alternative to ucwords
	 * Uppercase the first character of each word in a string
	 *
	 * @static
	 * @access public
	 * @param string
	 * @return string with first char of each word uppercase
	 * @see http://www.php.net/ucwords
	*/
	function ucwords($str)
	{
		jimport('phputf8.ucwords');
		return utf8_ucwords($str);
	}

	/**
	 * Transcode a string.
	 *
	 * @static
	 * @param string $source The string to transcode.
	 * @param string $from_encoding The source encoding.
	 * @param string $to_encoding The target encoding.
	 * @return string Transcoded string
	 * @since 1.5
	 */
	function transcode($source, $from_encoding, $to_encoding) {

		if (is_string($source)) {
			/*
			 * "//TRANSLIT" is appendd to the $to_encoding to ensure that when iconv comes
			 * across a character that cannot be represented in the target charset, it can
			 * be approximated through one or several similarly looking characters.
			 */
			return iconv($from_encoding, $to_encoding.'//TRANSLIT', $source);
		}
	}
}
