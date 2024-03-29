<?php
/**
 * @version		$Id: stream.php 3685 2011-07-12 13:02:31Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Template
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

 /**
  * Abstract stream wrapper to convert markup of mostly-PHP templates into PHP prior to include().
  *
  * Based in large part on the example at
  * http://www.php.net/manual/en/function.stream-wrapper-register.php
  * 
  * @author     Johan Janssens <johan@nooku.org>
  * @category   Koowa
  * @package    Koowa_Template
  */
class KTemplateStream
{
    /**
     * Current stream position.
     *
     * @var int
     */
    private $_pos = 0;

    /**
     * Template data
     *
     * @var string
     */
    private $_data;

    /**
     * Stream stats.
     *
     * @var array
     */
    private $_stat;
    
    /**
     * Template path
     *
     * @var string
     */
    private $_path;
    
    /**
     * Register the stream wrapper 
     * 
     * Function prevents from registering the wrapper twice
     */
    public static function register()
    {       
        if (!in_array('tmpl', stream_get_wrappers())) {
            stream_wrapper_register('tmpl', __CLASS__);
        } 
    } 
     
    /**
     * Opens the template file and converts markup.
     * 
     * This function filters the data from the stream by pushing it through the template's 
     * read filter chain. The template object to use for filtering is the top node on the
     * template stack
     * 
     * @param string    The stream path
     * @return boolean
     */
    public function stream_open($path) 
    {        
        //Get the view script source
        $identifier = str_replace('tmpl://', '', $path);
        
        //Get the template object from the template stack and parse it
        $template = KFactory::get($identifier)->top();
        
        //Get the template path
        $this->_path = $template->getPath();
        
        //Get the template data
        $this->_data = $template->parse();
        
       // file_get_contents() won't update PHP's stat cache, so performing
       // another stat() on it will hit the filesystem again. Since the file
       // has been successfully read, avoid this and just fake the stat
       // so include() is happy.
        $this->_stat = array('mode' => 0100777, 'size' => strlen($this->_data));

        return true;
    }
   
    /**
     * Reads from the stream.
     * 
     * @return string
     */
    public function stream_read($count) 
    {
        $ret = substr($this->_data, $this->_pos, $count);
        $this->_pos += strlen($ret);
        return $ret;
    }

    /**
     * Tells the current position in the stream.
     * 
     * @return int
     */
    public function stream_tell() 
    {
        return $this->_pos;
    }
  
    /**
     * Tells if we are at the end of the stream.
     * 
     * @return bool
     */
    public function stream_eof() 
    {
        return $this->_pos >= strlen($this->_data);
    }
    
    /**
     * Stream statistics.
     * 
     * @return array
     */
    public function stream_stat() 
    {
        return $this->_stat;
    }
    
    /**
     * Flushes the output
     * 
     * @return boolean
     */
    public function stream_flush()
    {
        return false;
    }
    
    
    /**
     * Close the stream
     * 
     * @return void
     */
    public function stream_close()
    {
        
    }

    /**
     * Seek to a specific point in the stream.
     * 
     * @return bool
     */
    public function stream_seek($offset, $whence) 
    {
        switch ($whence) 
        {
            case SEEK_SET:
                
                if ($offset < strlen($this->_data) && $offset >= 0) {
                $this->_pos = $offset;
                    return true;
                } 
                else return false;
                break;

            case SEEK_CUR:
                
                if ($offset >= 0) 
                {
                    $this->_pos += $offset;
                    return true;
                } 
                else return false;
                break;

            case SEEK_END:
                
                if (strlen($this->_data) + $offset >= 0) 
                {
                    $this->_pos = strlen($this->_data) + $offset;
                    return true;
                } 
                else return false;
                break;

            default:
                return false;
        }
    }
     
    /**
     * Url statistics.
     *
     * This method is called in response to all stat() related functions on the stream
     * 
     * @param   string  The file path or URL to stat
     * @param   int     Holds additional flags set by the streams API
     * 
     * @return array
     */
    public function url_stat($path, $flags) 
    {
        return $this->_stat;
    }
}