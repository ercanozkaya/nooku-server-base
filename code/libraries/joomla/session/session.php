<?php
/**
* @version		$Id: session.php 16385 2010-04-23 10:44:15Z ian $
* @package		Joomla.Framework
* @subpackage	Session
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

//Register the session storage class with the loader
JLoader::register('JSessionStorage', dirname(__FILE__).DS.'storage.php');

/**
* Class for managing HTTP sessions
*
* Provides access to session-state values as well as session-level
* settings and lifetime management methods.
* Based on the standart PHP session handling mechanism it provides
* for you more advanced features such as expire timeouts.
*
* @package		Joomla.Framework
* @subpackage	Session
* @since		1.5
*/
class JSession extends JObject
{
	/**
	 * internal state
	 *
	 * @access protected
	 * @var	string $_state one of 'active'|'expired'|'destroyed|'error'
	 * @see getState()
	 */
	var	$_state	= '';

	/**
	 * Maximum age of unused session
	 *
	 * @access protected
	 * @var	string $_expire seconds
	 */
	var	$_expire = 900;

	/**
	 * The session store object
	 *
	 * @access protected
	 * @var	object A JSessionStorage object
	 */
	var	$_store	= null;

	/**
	* security policy
	*
	* Default values:
	*  - fix_browser
	*  - fix_adress
	*
	* @access protected
	* @var array $_security list of checks that will be done.
	*/
	var $_security = array( 'fix_browser' );

	/**
	* Force cookies to be SSL only
	*
	* @access protected
	* @default false
	* @var bool $force_ssl
	*/
	var $_force_ssl = false;

	/**
	* Constructor
	*
	* @access protected
	* @param string $storage
	* @param array 	$options 	optional parameters
	*/
	function __construct( $store = 'none', $options = array() )
	{
		//Need to destroy any existing sessions started with session.auto_start
		if (session_id()) {
			session_unset();
			session_destroy();
		}

		//set default sessios save handler
		ini_set('session.save_handler', 'files');

		//disable transparent sid support
		ini_set('session.use_trans_sid', '0');

		//create handler
		$this->_store = JSessionStorage::getInstance($store, $options);

		//set options
		$this->_setOptions( $options );

		//set the cookie params
		$this->_setCookieParams();

		//initialise the session
		$this->_setCounter();
		$this->_setTimers();

		// perform security checks
		$this->_validate();
	}

    /**
	 * Session object destructor
	 *
	 * @access private
	 * @since 1.5
	 */
	function __destruct() {
		$this->close();
	}

	/**
	 * Returns a reference to the global Session object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $session = &JSession::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JSession	The Session object.
	 * @since	1.5
	 */
	function &getInstance($handler, $options)
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new JSession($handler, $options);
		}

		return $instance;
	}

	/**
	 * Get current state of session
	 *
	 * @access public
	 * @return string The session state
	 */
    function getState() {
		return $this->_state;
	}

	/**
	 * Get expiration time in minutes
	 *
	 * @access public
	 * @return integer The session expiration time in minutes
	 */
    function getExpire() {
		return $this->_expire;
    }

	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param boolean $forceNew If true, force a new token to be created
	 * @access public
	 * @return string The session token
	 */
	function getToken($forceNew = false)
	{
		$token = $this->get( 'session.token' );

		//create a token
		if( $token === null || $forceNew ) 
		{
			$token	=	$this->_createToken( 12 );
			$this->set( 'session.token', $token );
		}

		return $token;
	}

	/**
	 * Method to determine if a token exists in the session. If not the
	 * session will be set to expired
	 *
	 * @param	string	Hashed token to be verified
	 * @param	boolean	If true, expires the session
	 * @since	1.5
	 * @static
	 */
	function hasToken($tCheck, $forceExpire = true)
	{
		// check if a token exists in the session
		$tStored = $this->get( 'session.token' );

		//check token
		if(($tStored !== $tCheck))
		{
			if($forceExpire) {
				$this->_state = 'expired';
			}
			return false;
		}

		return true;
	}


	/**
	 * Get session name
	 *
	 * @access public
	 * @return string The session name
	 */
	function getName()
	{
		if( $this->_state === 'destroyed' ) {
			// @TODO : raise error
			return null;
		}
		
		return session_name();
	}

	/**
	 * Get session id
	 *
	 * @access public
	 * @return string The session name
	 */
	function getId()
	{
		if( $this->_state === 'destroyed' ) {
			// @TODO : raise error
			return null;
		}
		
		return session_id();
	}

	/**
	 * Get the session handlers
	 *
	 * @access public
	 * @return array An array of available session handlers
	 */
	function getStores()
	{
		jimport('joomla.filesystem.folder');
		$handlers = JFolder::files(dirname(__FILE__).DS.'storage', '.php$');

		$names = array();
		foreach($handlers as $handler)
		{
			$name = substr($handler, 0, strrpos($handler, '.'));
			$class = 'JSessionStorage'.ucfirst($name);

			//Load the class only if needed
			if(!class_exists($class)) {
				require_once(dirname(__FILE__).DS.'storage'.DS.$name.'.php');
			}

			if(call_user_func_array( array( trim($class), 'test' ), array())) {
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	* Check whether this session is currently created
	*
	* @access public
	* @return boolean $result true on success
	*/
	function isNew()
	{
		$counter = $this->get( 'session.counter' );
		if( $counter === 1 ) {
			return true;
		}
		
		return false;
	}

	 /**
	 * Get data from the session store
	 *
	 * @static
	 * @access public
	 * @param  string $name			Name of a variable
	 * @param  mixed  $default 		Default value of a variable if not set
	 * @param  string 	$namespace 	Namespace to use, default to 'default'
	 * @return mixed  Value of a variable
	 */
	function &get($name, $default = null, $namespace = 'default')
	{
		$namespace = '__'.$namespace; //add prefix to namespace to avoid collisions

		if($this->_state !== 'active' && $this->_state !== 'expired') 
		{
			// @TODO :: generated error here
			$error = null;
			return $error;
		}

		if (isset($_SESSION[$namespace][$name])) {
			return $_SESSION[$namespace][$name];
		}
		
		return $default;
	}

	/**
	 * Set data into the session store
	 *
	 * @access public
	 * @param  string $name  		Name of a variable
	 * @param  mixed  $value 		Value of a variable
	 * @param  string 	$namespace 	Namespace to use, default to 'default'
	 * @return mixed  Old value of a variable
	 */
	function set($name, $value, $namespace = 'default')
	{
		$namespace = '__'.$namespace; //add prefix to namespace to avoid collisions

		if($this->_state !== 'active') {
			// @TODO :: generated error here
			return null;
		}

		$old = isset($_SESSION[$namespace][$name]) ?  $_SESSION[$namespace][$name] : null;

		if (null === $value) {
			unset($_SESSION[$namespace][$name]);
		} else {
			$_SESSION[$namespace][$name] = $value;
		}

		return $old;
	}

	/**
	* Check wheter data exists in the session store
	*
	* @access public
	* @param string 	$name 		Name of variable
	* @param  string 	$namespace 	Namespace to use, default to 'default'
	* @return boolean $result true if the variable exists
	*/
	function has( $name, $namespace = 'default' )
	{
		$namespace = '__'.$namespace; //add prefix to namespace to avoid collisions

		if( $this->_state !== 'active' ) {
			// @TODO :: generated error here
			return null;
		}

		return isset( $_SESSION[$namespace][$name] );
	}

	/**
	* Unset data from the session store
	*
	* @access public
	* @param  string 	$name 		Name of variable
	* @param  string 	$namespace 	Namespace to use, default to 'default'
	* @return mixed $value the value from session or NULL if not set
	*/
	function clear( $name, $namespace = 'default' )
	{
		$namespace = '__'.$namespace; //add prefix to namespace to avoid collisions

		if( $this->_state !== 'active' ) {
			// @TODO :: generated error here
			return null;
		}

		$value	=	null;
		if( isset( $_SESSION[$namespace][$name] ) ) {
			$value	=	$_SESSION[$namespace][$name];
			unset( $_SESSION[$namespace][$name] );
		}

		return $value;
	}

	/**
	* Start a session
	*
	* Creates a session (or resumes the current one based on the state of the session)
 	*
	* @access private
	* @return boolean $result true on success
	*/
	function start()
	{
		// session was already started
		if( $this->_state == 'active' ) {
			return true;
		}
		
		//  start session if not startet
		if( $this->_state == 'restart' ) {
			session_id( $this->_createId() );
		}

		session_cache_limiter('none');
		session_start();

		// Send modified header for IE 6.0 Security Policy
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		
		// Set the state to active
		$this->_state =	'active';

		return true;
	}


	/**
	 * Frees all session variables and destroys all data registered to a session
	 *
	 * This method resets the $_SESSION variable and destroys all of the data associated
	 * with the current session in its storage (file or DB). It forces new session to be
	 * started after this method is called. It does not unset the session cookie.
	 *
	 * @static
	 * @access public
	 * @return void
	 * @see	session_unset()
	 * @see	session_destroy()
	 */
	function destroy()
	{
		// session was already destroyed
		if( $this->_state === 'destroyed' ) {
			return true;
		}

		// In order to kill the session altogether, like to log the user out, the session id
		// must also be unset. If a cookie is used to propagate the session id (default behavior),
		// then the session cookie must be deleted.
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		
		session_unset();
		session_destroy();

		$this->_state = 'destroyed';
		return true;
	}

	/**
    * restart an expired or locked session
	*
	* @access public
	* @return boolean $result true on success
	* @see destroy
	*/
	function restart()
	{
		$this->destroy();
		if( $this->_state !==  'destroyed' ) {
			// @TODO :: generated error here
			return false;
		}

		// Re-register the session handler after a session has been destroyed, to avoid PHP bug
		$this->_store->register();

		$this->_state = 'restart';
		
		//regenerate session id
		$id	=	$this->_createId( strlen( $this->getId() ) );
		session_id($id);
		$this->start();
	
		$this->_validate();
		$this->_setCounter();

		return true;
	}

	/**
	* Create a new session and copy variables from the old one
	*
	* @abstract
	* @access public
	* @return boolean $result true on success
	*/
	function fork()
	{
		if( $this->_state !== 'active' ) {
			// @TODO :: generated error here
			return false;
		}

		// save values
		$values	= $_SESSION;

		// keep session config
		$trans	=	ini_get( 'session.use_trans_sid' );
		if( $trans ) {
			ini_set( 'session.use_trans_sid', 0 );
		}
		$cookie	=	session_get_cookie_params();

		// create new session id
		$id	=	$this->_createId( strlen( $this->getId() ) );

		// first we grab the session data
		$data = $this->_store->read($this->getId());

		// kill session
		session_destroy();

		// re-register the session store after a session has been destroyed, to avoid PHP bug
		$this->_store->register();

		// restore config
		ini_set( 'session.use_trans_sid', $trans );
		session_set_cookie_params( $cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'] );

		// restart session with new id
		session_id( $id );
		session_start();
		$_SESSION = $values;

		//now we put the session data back
		$this->_store->write($id, $data);
		return true;
	}

	 /**
	 * Writes session data and ends session
	 *
	 * Session data is usually stored after your script terminated without the need
	 * to call JSession::close(),but as session data is locked to prevent concurrent
	 * writes only one script may operate on a session at any time. When using
	 * framesets together with sessions you will experience the frames loading one
	 * by one due to this locking. You can reduce the time needed to load all the
	 * frames by ending the session as soon as all changes to session variables are
	 * done.
	 *
	 * @access public
	 * @see	session_write_close()
	 */
	function close() 
	{
		session_write_close();
	}

	 /**
	 * Create a session id
	 *
	 * @static
	 * @access private
	 * @return string Session ID
	 */
	function _createId( )
	{
		$id = 0;
		while (strlen($id) < 32)  {
			$id .= mt_rand(0, mt_getrandmax());
		}

		$id	= md5( uniqid($id, true));
		return $id;
	}

	 /**
	 * Set session cookie parameters
	 *
	 * @access private
	 */
	function _setCookieParams() 
	{
		$cookie	= session_get_cookie_params();
		if($this->_force_ssl) {
			$cookie['secure'] = true;
		}
		
		session_set_cookie_params( $cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'] );
	}

	/**
	* Create a token-string
	*
	* @access protected
	* @param int $length lenght of string
	* @return string $id generated token
	*/
	function _createToken( $length = 32 )
	{
		static $chars	=	'0123456789abcdef';
		$max			=	strlen( $chars ) - 1;
		$token			=	'';
		$name 			=  session_name();
		
		for( $i = 0; $i < $length; ++$i ) {
			$token .=	$chars[ (rand( 0, $max )) ];
		}

		return md5($token.$name);
	}

	/**
	* Set counter of session usage
	*
	* @access protected
	* @return boolean $result true on success
	*/
	function _setCounter()
	{
		$counter = $this->get( 'session.counter', 0 );
		++$counter;

		$this->set( 'session.counter', $counter );
		return true;
	}

	/**
	* Set the session timers
	*
	* @access protected
	* @return boolean $result true on success
	*/
	function _setTimers()
	{
		if( !$this->has( 'session.timer.start' ) )
		{
			$start	=	time();

			$this->set( 'session.timer.start' , $start );
			$this->set( 'session.timer.last'  , $start );
			$this->set( 'session.timer.now'   , $start );
		}

		$this->set( 'session.timer.last', $this->get( 'session.timer.now' ) );
		$this->set( 'session.timer.now', time() );

		return true;
	}

	/**
	* set additional session options
	*
	* @access protected
	* @param array $options list of parameter
	* @return boolean $result true on success
	*/
	function _setOptions( &$options )
	{
		// set name
		if( isset( $options['name'] ) ) {
			session_name( 'KSESSION_'.md5($options['name']) );
		}

		// set id
		if( isset( $options['id'] ) ) {
			session_id( $options['id'] );
		}

		// set expire time
		if( isset( $options['expire'] ) ) {
			$this->_expire	=	$options['expire'];
		}

		// get security options
		if( isset( $options['security'] ) ) {
			$this->_security	=	explode( ',', $options['security'] );
		}

		if( isset( $options['force_ssl'] ) ) {
			$this->_force_ssl = (bool) $options['force_ssl'];
		}

		//sync the session maxlifetime
		ini_set('session.gc_maxlifetime', $this->_expire);

		return true;
	}

	/**
	* Do some checks for security reason
	*
	* - timeout check (expire)
	* - ip-fixiation
	* - browser-fixiation
	*
	* If one check failed, session data has to be cleaned.
	*
	* @access protected
	* @param boolean $restart reactivate session
	* @return boolean $result true on success
	* @see http://shiflett.org/articles/the-truth-about-sessions
	*/
	function _validate( $restart = false )
	{
		// allow to restart a session
		if( $restart )
		{
			$this->_state	=	'active';

			$this->set( 'session.client.address'	, null );
			$this->set( 'session.client.forwarded'	, null );
			$this->set( 'session.client.browser'	, null );
			$this->set( 'session.token'				, null );
		}

		// check if session has expired
		if( $this->_expire )
		{
			$curTime =	$this->get( 'session.timer.now' , 0  );
			$maxTime =	$this->get( 'session.timer.last', 0 ) +  $this->_expire;

			// empty session variables
			if( $maxTime < $curTime ) {
				$this->_state	=	'expired';
				return false;
			}
		}

		// record proxy forwarded for in the session in case we need it later
		if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$this->set( 'session.client.forwarded', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}

		// check for client adress
		if( in_array( 'fix_adress', $this->_security ) && isset( $_SERVER['REMOTE_ADDR'] ) )
		{
			$ip	= $this->get( 'session.client.address' );

			if( $ip === null ) {
				$this->set( 'session.client.address', $_SERVER['REMOTE_ADDR'] );
			}
			else if( $_SERVER['REMOTE_ADDR'] !== $ip )
			{
				$this->_state	=	'error';
				return false;
			}
		}

		// check for clients browser
		if( in_array( 'fix_browser', $this->_security ) && isset( $_SERVER['HTTP_USER_AGENT'] ) )
		{
			$browser = $this->get( 'session.client.browser' );

			if( $browser === null ) {
				$this->set( 'session.client.browser', $_SERVER['HTTP_USER_AGENT']);
			}
			else if( $_SERVER['HTTP_USER_AGENT'] !== $browser )
			{
//				$this->_state	=	'error';
//				return false;
			}
		}

		return true;
	}
}