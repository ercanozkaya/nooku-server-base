<?php
/**
* @version      $Id: browser.php 14401 2010-01-26 14:10:00Z louis $
* @package      Joomla.Framework
* @subpackage   Environment
* @copyright    Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Browser class, provides capability information about the current web client.
 *
 * Browser identification is performed by examining the HTTP_USER_AGENT
 * environment variable provided by the web server.
 *
 * This class has many influences from the lib/Browser.php code in
 * version 3 of Horde by Chuck Hagenbuch and Jon Parise
 *
 * @package     Joomla.Framework
 * @subpackage  Environment
 * @since       1.5
 */

class JBrowser extends JObject
{
    /**
     * Major version number.
     *
     * @var integer
     */
    var $_majorVersion = 0;

    /**
     * Minor version number.
     *
     * @var integer
     */
    var $_minorVersion = 0;

    /**
     * Browser name.
     *
     * @var string
     */
    var $_browser = '';

    /**
     * Full user agent string.
     *
     * @var string
     */
    var $_agent = '';

    /**
     * Lower-case user agent string.
     *
     * @var string
     */
    var $_lowerAgent = '';

    /**
     * HTTP_ACCEPT string
     *
     * @var string
     */
    var $_accept = '';

     /**
     * Platform the browser is running on.
     *
     * @var string
     */
    var $_platform = '';

    /**
     * Known robots.
     *
     * @var array
     */
    var $_robots = array(
        /* The most common ones. */
        'Googlebot',
        'msnbot',
        'Slurp',
        'Yahoo',
        /* The rest alphabetically. */
        'Arachnoidea',
        'ArchitextSpider',
        'Ask Jeeves',
        'B-l-i-t-z-Bot',
        'Baiduspider',
        'BecomeBot',
        'cfetch',
        'ConveraCrawler',
        'ExtractorPro',
        'FAST-WebCrawler',
        'FDSE robot',
        'fido',
        'geckobot',
        'Gigabot',
        'Girafabot',
        'grub-client',
        'Gulliver',
        'HTTrack',
        'ia_archiver',
        'InfoSeek',
        'kinjabot',
        'KIT-Fireball',
        'larbin',
        'LEIA',
        'lmspider',
        'Lycos_Spider',
        'Mediapartners-Google',
        'MuscatFerret',
        'NaverBot',
        'OmniExplorer_Bot',
        'polybot',
        'Pompos',
        'Scooter',
        'Teoma',
        'TheSuBot',
        'TurnitinBot',
        'Ultraseek',
        'ViolaBot',
        'webbandit',
        'www.almaden.ibm.com/cs/crawler',
        'ZyBorg',
    );

    /**
     * Is this a mobile browser?
     *
     * @var boolean
     */
    var $_mobile = false;

    /**
     * Features.
     *
     * @var array
     */
    var $_features = array(
        'html'          => true,
        'hdml'          => false,
        'wml'           => false,
        'images'        => true,
        'iframes'       => false,
        'frames'        => true,
        'tables'        => true,
        'java'          => true,
        'javascript'    => true,
        'dom'           => false,
        'utf'           => false,
        'rte'           => false,
        'homepage'      => false,
        'accesskey'     => false,
        'optgroup'      => false,
        'xmlhttpreq'    => false,
        'cite'          => false,
        'xhtml+xml'     => false,
        'mathml'        => false,
        'svg'           => false
    );

    /**
     * Quirks
     *
     * @var array
     */
    var $_quirks = array(
        'avoid_popup_windows'           => false,
        'break_disposition_header'      => false,
        'break_disposition_filename'    => false,
        'broken_multipart_form'         => false,
        'cache_same_url'                => false,
        'cache_ssl_downloads'           => false,
        'double_linebreak_textarea'     => false,
        'empty_file_input_value'        => false,
        'must_cache_forms'              => false,
        'no_filename_spaces'            => false,
        'no_hidden_overflow_tables'     => false,
        'ow_gui_1.3'                    => false,
        'png_transparency'              => false,
        'scrollbar_in_way'              => false,
        'scroll_tds'                    => false,
        'windowed_controls'             => false,
    );

    /**
     * List of viewable image MIME subtypes.
     * This list of viewable images works for IE and Netscape/Mozilla.
     *
     * @var array
     */
    var $_images = array('jpeg', 'gif', 'png', 'pjpeg', 'x-png', 'bmp');


    /**
     * Create a browser instance (Constructor).
     *
     * @param string $userAgent  The browser string to parse.
     * @param string $accept     The HTTP_ACCEPT settings to use.
     */
    function __construct($userAgent = null, $accept = null)
    {
        $this->match($userAgent, $accept);
    }

    /**
     * Returns a reference to the global Browser object, only creating it
     * if it doesn't already exist.
     *
     * This method must be invoked as:
     *      <pre>  $browser = &JBrowser::getInstance([$userAgent[, $accept]]);</pre>
     *
     * @access public
     * @param string $userAgent  The browser string to parse.
     * @param string $accept     The HTTP_ACCEPT settings to use.
     * @return JBrowser  The Browser object.
     */
    function &getInstance($userAgent = null, $accept = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $signature = serialize(array($userAgent, $accept));

        if (empty($instances[$signature])) {
            $instances[$signature] = new JBrowser($userAgent, $accept);
        }

        return $instances[$signature];
    }

   /**
     * Parses the user agent string and inititializes the object with
     * all the known features and quirks for the given browser.
     *
     * @param string $userAgent  The browser string to parse.
     * @param string $accept     The HTTP_ACCEPT settings to use.
     */
    function match($userAgent = null, $accept = null)
    {
        // Set our agent string.
        if (is_null($userAgent)) {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $this->_agent = trim($_SERVER['HTTP_USER_AGENT']);
            }
        } else {
            $this->_agent = $userAgent;
        }
        $this->_lowerAgent = strtolower($this->_agent);

        // Set our accept string.
        if (is_null($accept)) {
            if (isset($_SERVER['HTTP_ACCEPT'])) {
                $this->_accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
            }
        } else {
            $this->_accept = strtolower($accept);
        }


        // Check if browser excepts content type xhtml+xml
        if (strpos($this->_accept, 'application/xhtml+xml')) {
            $this->setFeature('xhtml+xml');
        }

        // Check for a mathplayer plugin is installed, so we can use MathML on several browsers
        if (strpos($this->_lowerAgent, 'mathplayer') !== false) {
            $this->setFeature('mathml');
        }

        // Check for UTF support.
        if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
            $this->setFeature('utf', strpos(strtolower($_SERVER['HTTP_ACCEPT_CHARSET']), 'utf') !== false);
        }

        if (!empty($this->_agent)) {
            $this->_setPlatform();

            if (strpos($this->_lowerAgent, 'mobileexplorer') !== false ||
                strpos($this->_lowerAgent, 'openwave') !== false ||
                strpos($this->_lowerAgent, 'opera mini') !== false ||
                strpos($this->_lowerAgent, 'operamini') !== false) {
                $this->setFeature('frames', false);
                $this->setFeature('javascript', false);
                $this->setQuirk('avoid_popup_windows');
                $this->_mobile = true;
            } elseif (preg_match('|Opera[/ ]([0-9.]+)|', $this->_agent, $version)) {
                        $this->setBrowser('opera');
                        list($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
                        $this->setFeature('javascript', true);
                        $this->setQuirk('no_filename_spaces');

                if ($this->_majorVersion >= 7) {
                    $this->setFeature('dom');
                    $this->setFeature('iframes');
                    $this->setFeature('accesskey');
                    $this->setFeature('optgroup');
                    $this->setQuirk('double_linebreak_textarea');
                }
            } elseif (strpos($this->_lowerAgent, 'elaine/') !== false ||
                        strpos($this->_lowerAgent, 'palmsource') !== false ||
                        strpos($this->_lowerAgent, 'digital paths') !== false) {
                $this->setBrowser('palm');
                $this->setFeature('images', false);
                $this->setFeature('frames', false);
                $this->setFeature('javascript', false);
                $this->setQuirk('avoid_popup_windows');
                $this->_mobile = true;
            } elseif ((preg_match('|MSIE ([0-9.]+)|', $this->_agent, $version)) ||
                        (preg_match('|Internet Explorer/([0-9.]+)|', $this->_agent, $version))) {

                $this->setBrowser('msie');
                $this->setQuirk('cache_ssl_downloads');
                $this->setQuirk('cache_same_url');
                $this->setQuirk('break_disposition_filename');

                if (strpos($version[1], '.') !== false) {
                    list($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
                } else {
                    $this->_majorVersion = $version[1];
                    $this->_minorVersion = 0;
                }

                /* IE (< 7) on Windows does not support alpha transparency in
                 * PNG images. */
                if (($this->_majorVersion < 7) &&
                    preg_match('/windows/i', $this->_agent)) {
                    $this->setQuirk('png_transparency');
                }

                /* Some Handhelds have their screen resolution in the
                 * user agent string, which we can use to look for
                 * mobile agents. */
                if (preg_match('/; (120x160|240x280|240x320|320x320)\)/', $this->_agent)) {
                    $this->_mobile = true;
                }

                switch ($this->_majorVersion) {
                case 7:
                    $this->setFeature('javascript', 1.4);
                    $this->setFeature('dom');
                    $this->setFeature('iframes');
                    $this->setFeature('utf');
                    $this->setFeature('rte');
                    $this->setFeature('homepage');
                    $this->setFeature('accesskey');
                    $this->setFeature('optgroup');
                    $this->setFeature('xmlhttpreq');
                    $this->setQuirk('scrollbar_in_way');
                    break;

                case 6:
                    $this->setFeature('javascript', 1.4);
                    $this->setFeature('dom');
                    $this->setFeature('iframes');
                    $this->setFeature('utf');
                    $this->setFeature('rte');
                    $this->setFeature('homepage');
                    $this->setFeature('accesskey');
                    $this->setFeature('optgroup');
                    $this->setFeature('xmlhttpreq');
                    $this->setQuirk('scrollbar_in_way');
                    $this->setQuirk('broken_multipart_form');
                    $this->setQuirk('windowed_controls');
                    break;

                case 5:
                    if ($this->getPlatform() == 'mac') {
                        $this->setFeature('javascript', 1.2);
                        $this->setFeature('optgroup');
                    } else {
                        // MSIE 5 for Windows.
                        $this->setFeature('javascript', 1.4);
                        $this->setFeature('dom');
                        $this->setFeature('xmlhttpreq');
                        if ($this->_minorVersion >= 5) {
                            $this->setFeature('rte');
                            $this->setQuirk('windowed_controls');
                        }
                    }
                    $this->setFeature('iframes');
                    $this->setFeature('utf');
                    $this->setFeature('homepage');
                    $this->setFeature('accesskey');
                    if ($this->_minorVersion == 5) {
                        $this->setQuirk('break_disposition_header');
                        $this->setQuirk('broken_multipart_form');
                    }
                    break;

                case 4:
                    $this->setFeature('javascript', 1.2);
                    $this->setFeature('accesskey');
                    if ($this->_minorVersion > 0) {
                        $this->setFeature('utf');
                    }
                    break;

                case 3:
                    $this->setFeature('javascript', 1.5);
                    $this->setQuirk('avoid_popup_windows');
                    break;
                }
            } elseif (preg_match('|amaya/([0-9.]+)|', $this->_agent, $version)) {
                $this->setBrowser('amaya');
                $this->_majorVersion = $version[1];
                if (isset($version[2])) {
                    $this->_minorVersion = $version[2];
                }
                if ($this->_majorVersion > 1) {
                    $this->setFeature('mathml');
                    $this->setFeature('svg');
                }
                $this->setFeature('xhtml+xml');
            } elseif (preg_match('|W3C_Validator/([0-9.]+)|', $this->_agent, $version)) {
                $this->setFeature('mathml');
                $this->setFeature('svg');
                $this->setFeature('xhtml+xml');
            } elseif (preg_match('|ANTFresco/([0-9]+)|', $this->_agent, $version)) {
                $this->setBrowser('fresco');
                $this->setFeature('javascript', 1.5);
                $this->setQuirk('avoid_popup_windows');
            } elseif (strpos($this->_lowerAgent, 'avantgo') !== false) {
                $this->setBrowser('avantgo');
                $this->_mobile = true;
            } elseif (preg_match('|Konqueror/([0-9]+)|', $this->_agent, $version) ||
                        preg_match('|Safari/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version)) {
                // Konqueror and Apple's Safari both use the KHTML
                // rendering engine.
                $this->setBrowser('konqueror');
                $this->setQuirk('empty_file_input_value');
                $this->setQuirk('no_hidden_overflow_tables');
                $this->_majorVersion = $version[1];
                if (isset($version[2])) {
                    $this->_minorVersion = $version[2];
                }

                if (strpos($this->_agent, 'Safari') !== false &&
                    $this->_majorVersion >= 60) {
                    // Safari.
                    $this->setFeature('utf');
                    $this->setFeature('javascript', 1.4);
                    $this->setFeature('dom');
                    $this->setFeature('iframes');
                    if ($this->_majorVersion > 125 ||
                        ($this->_majorVersion == 125 &&
                         $this->_minorVersion >= 1)) {
                        $this->setFeature('accesskey');
                        $this->setFeature('xmlhttpreq');
                    }
                    if ($this->_majorVersion > 522) {
                        $this->setFeature('svg');
                        $this->setFeature('xhtml+xml');
                    }
                } else {
                    // Konqueror.
                    $this->setFeature('javascript', 1.5);
                    switch ($this->_majorVersion) {
                    case 3:
                        $this->setFeature('dom');
                        $this->setFeature('iframes');
                $this->setFeature('xhtml+xml');
                        break;
                    }
                }
            } elseif (preg_match('|Mozilla/([0-9.]+)|', $this->_agent, $version)) {
                $this->setBrowser('mozilla');
                $this->setQuirk('must_cache_forms');

                list($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
                switch ($this->_majorVersion) {
                case 5:
                    if ($this->getPlatform() == 'win') {
                        $this->setQuirk('break_disposition_filename');
                    }
                    $this->setFeature('javascript', 1.4);
                    $this->setFeature('dom');
                    $this->setFeature('accesskey');
                    $this->setFeature('optgroup');
                    $this->setFeature('xmlhttpreq');
                    $this->setFeature('cite');
                    if (preg_match('|rv:(.*)\)|', $this->_agent, $revision)) {
                        if ($revision[1] >= 1) {
                            $this->setFeature('iframes');
                        }
                        if ($revision[1] >= 1.3) {
                            $this->setFeature('rte');
                        }
                        if ($revision[1] >= 1.5) {
                            $this->setFeature('svg');
                            $this->setFeature('mathml');
                $this->setFeature('xhtml+xml');
                        }
                    }
                    break;

                case 4:
                    $this->setFeature('javascript', 1.3);
                    $this->setQuirk('buggy_compression');
                    break;

                case 3:
                default:
                    $this->setFeature('javascript', 1);
                    $this->setQuirk('buggy_compression');
                    break;
                }
            } elseif (preg_match('|Lynx/([0-9]+)|', $this->_agent, $version)) {
                $this->setBrowser('lynx');
                $this->setFeature('images', false);
                $this->setFeature('frames', false);
                $this->setFeature('javascript', false);
                $this->setQuirk('avoid_popup_windows');
            } elseif (preg_match('|Links \(([0-9]+)|', $this->_agent, $version)) {
                $this->setBrowser('links');
                $this->setFeature('images', false);
                $this->setFeature('frames', false);
                $this->setFeature('javascript', false);
                $this->setQuirk('avoid_popup_windows');
            } elseif (preg_match('|HotJava/([0-9]+)|', $this->_agent, $version)) {
                $this->setBrowser('hotjava');
                $this->setFeature('javascript', false);
            } elseif (strpos($this->_agent, 'UP/') !== false ||
                        strpos($this->_agent, 'UP.B') !== false ||
                        strpos($this->_agent, 'UP.L') !== false) {
                $this->setBrowser('up');
                $this->setFeature('html', false);
                $this->setFeature('javascript', false);
                $this->setFeature('hdml');
                $this->setFeature('wml');

                if (strpos($this->_agent, 'GUI') !== false &&
                    strpos($this->_agent, 'UP.Link') !== false) {
                    /* The device accepts Openwave GUI extensions for
                     * WML 1.3. Non-UP.Link gateways sometimes have
                     * problems, so exclude them. */
                    $this->setQuirk('ow_gui_1.3');
                }
                $this->_mobile = true;
            } elseif (strpos($this->_agent, 'Xiino/') !== false) {
                $this->setBrowser('xiino');
                $this->setFeature('hdml');
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_agent, 'Palmscape/') !== false) {
                $this->setBrowser('palmscape');
                $this->setFeature('javascript', false);
                $this->setFeature('hdml');
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_agent, 'Nokia') !== false) {
                $this->setBrowser('nokia');
                $this->setFeature('html', false);
                $this->setFeature('wml');
                $this->setFeature('xhtml');
                $this->_mobile = true;
            } elseif (strpos($this->_agent, 'Ericsson') !== false) {
                $this->setBrowser('ericsson');
                $this->setFeature('html', false);
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_lowerAgent, 'wap') !== false) {
                $this->setBrowser('wap');
                $this->setFeature('html', false);
                $this->setFeature('javascript', false);
                $this->setFeature('hdml');
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_lowerAgent, 'docomo') !== false ||
                        strpos($this->_lowerAgent, 'portalmmm') !== false) {
                $this->setBrowser('imode');
                $this->setFeature('images', false);
                $this->_mobile = true;
        } elseif (strpos($this->_agent, 'BlackBerry') !== false) {
                $this->setBrowser('blackberry');
                $this->setFeature('html', false);
                $this->setFeature('javascript', false);
                $this->setFeature('hdml');
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_agent, 'MOT-') !== false) {
                $this->setBrowser('motorola');
                $this->setFeature('html', false);
                $this->setFeature('javascript', false);
                $this->setFeature('hdml');
                $this->setFeature('wml');
                $this->_mobile = true;
            } elseif (strpos($this->_lowerAgent, 'j-') !== false) {
                $this->setBrowser('mml');
                $this->_mobile = true;
            }
        }
    }

     /**
     * Match the platform of the browser.
     *
     * This is a pretty simplistic implementation, but it's intended
     * to let us tell what line breaks to send, so it's good enough
     * for its purpose.
     */
    function _setPlatform()
    {
        if (strpos($this->_lowerAgent, 'wind') !== false) {
            $this->_platform = 'win';
        } elseif (strpos($this->_lowerAgent, 'mac') !== false) {
            $this->_platform = 'mac';
        } else {
            $this->_platform = 'unix';
        }
    }

    /**
     * Return the currently matched platform.
     *
     * @return string  The user's platform.
     */
    function getPlatform()   {
        return $this->_platform;
    }

    /**
     * Sets the current browser.
     *
     * @param string $browser  The browser to set as current.
     */
    function setBrowser($browser) {
        $this->_browser = $browser;
    }

    /**
     * Retrieve the current browser.
     *
     * @return string  The current browser.
     */
    function getBrowser()    {
        return $this->_browser;
    }

    /**
     * Retrieve the current browser's major version.
     *
     * @return integer  The current browser's major version.
     */
    function getMajor()  {
        return $this->_majorVersion;
    }

    /**
     * Retrieve the current browser's minor version.
     * @return integer  The current browser's minor version.
     */
    function getMinor()  {
        return $this->_minorVersion;
    }

    /**
     * Retrieve the current browser's version.
     * @return string  The current browser's version.
     */
    function getVersion()    {
        return $this->_majorVersion . '.' . $this->_minorVersion;
    }

    /**
     * Return the full browser agent string.
     *
     * @return string  The browser agent string.
     */
    function getAgentString()    {
        return $this->_agent;
    }

    /**
     * Returns the server protocol in use on the current server.
     *
     * @return string  The HTTP server protocol version.
     */
    function getHTTPProtocol()
    {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            if (($pos = strrpos($_SERVER['SERVER_PROTOCOL'], '/'))) {
                return substr($_SERVER['SERVER_PROTOCOL'], $pos + 1);
            }
        }
        return null;
    }

    /**
     * Set unique behavior for the current browser.
     *
     * @param string $quirk  The behavior to set.
     * @param string $value  Special behavior parameter.
     */
    function setQuirk($quirk, $value = true) {
        $this->_quirks[$quirk] = $value;
    }

    /**
     * Check unique behavior for the current browser.
     *
     * @param string $quirk  The behavior to check.
     * @return boolean  Does the browser have the behavior set?
     */
    function hasQuirk($quirk) {
        return !empty($this->_quirks[$quirk]);
    }

     /**
     * Retrieve unique behavior for the current browser.
     *
     * @param string $quirk  The behavior to retrieve.
     * @return string  The value for the requested behavior.
     */
    function getQuirk($quirk)
    {
        return isset($this->_quirks[$quirk])
                ? $this->_quirks[$quirk]
                : null;
    }

    /**
     * Set capabilities for the current browser.
     *
     * @param string $feature  The capability to set.
     * @param string $value Special capability parameter.
     */
    function setFeature($feature, $value = true) {
        $this->_features[$feature] = $value;
    }


    /**
     * Check the current browser capabilities.
     * @param string $feature  The capability to check.
     * @return boolean  Does the browser have the capability set?
     */
    function hasFeature($feature)    {
        return !empty($this->_features[$feature]);
    }

    /**
     * Retrieve the current browser capability.
     *
     * @param string $feature  The capability to retrieve.
     * @return string  The value of the requested capability.
     */
    function getFeature($feature)    {
         return isset($this->_features[$feature])
                ? $this->_features[$feature]
                : null;
    }

    /**
     * Determines if a browser can display a given MIME type.
     *
     * @param string $mimetype  The MIME type to check.
     * @return boolean  True if the browser can display the MIME type.
     */
    function isViewable($mimetype)
    {
        $mimetype = strtolower($mimetype);
        list($type, $subtype) = explode('/', $mimetype);

        if (!empty($this->_accept)) {
            $wildcard_match = false;

            if (strpos($this->_accept, $mimetype) !== false) {
                return true;
            }

            if (strpos($this->_accept, '*/*') !== false) {
                $wildcard_match = true;
                if ($type != 'image') {
                    return true;
                }
             }

            /* image/jpeg and image/pjpeg *appear* to be the same
            * entity, but Mozilla doesn't seem to want to accept the
            * latter.  For our purposes, we will treat them the
            * same.
            */
                if ($this->isBrowser('mozilla') &&
                ($mimetype == 'image/pjpeg') &&
                (strpos($this->_accept, 'image/jpeg') !== false)) {
                    return true;
                }

            if (!$wildcard_match) {
                return false;
            }
        }

        if (!$this->hasFeature('images') || ($type != 'image')) {
            return false;
        }

        return (in_array($subtype, $this->_images));
    }

    /**
     * Determine if the given browser is the same as the current.
     *
     * @param string $browser  The browser to check.
     * @return boolean  Is the given browser the same as the current?
     */
    function isBrowser($browser)
    {
        return ($this->_browser === $browser);
    }

    /**
     * Determines if the browser is a robot or not.
     *
     * @return boolean  True if browser is a known robot.
     */
    function isRobot()
    {
         foreach ($this->_robots as $robot) {
             if (strpos($this->_agent, $robot) !== false) {
                 return true;
            }
        }
        return false;
    }

    /**
     * Determine if we are using a secure (SSL) connection.
     *
     * @return boolean  True if using SSL, false if not.
     */
    function isSSLConnection()
    {
        return ((isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on')) ||
            getenv('SSL_PROTOCOL_VERSION'));
    }
}