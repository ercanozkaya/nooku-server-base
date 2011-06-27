<?php
/**
 * @version		$Id: table.php 3540 2011-06-20 15:00:35Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Row
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Table Row Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Row
 */
class KDatabaseRowTable extends KDatabaseRowAbstract
{
	/**
	 * Table object or identifier (APP::com.COMPONENT.table.NAME)
	 *
	 * @var	string|object
	 */
	protected $_table = false;

	/**
     * Object constructor 
     *
     * @param   object  An optional KConfig object with configuration options.
     */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_table = $config->table;
			
		// Reset the row
        $this->reset();
            
        // Reset the row data
        if(isset($config->data))  {
            $this->setData($config->data->toArray(), $this->_new);
        }
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'table'	=> $this->_identifier->name
		));

		parent::_initialize($config);
	}

	/**
     * Method to get a table object
     * 
     * Function catches KDatabaseTableExceptions that are thrown for tables that 
     * don't exist. If no table object can be created the function will return FALSE.
     *
     * @return KDatabaseTableAbstract
     */
    public function getTable()
    {
        if($this->_table !== false)
        {
            if(!($this->_table instanceof KDatabaseTableAbstract))
		    {   		        
		        //Make sure we have a table identifier
		        if(!($this->_table instanceof KIdentifier)) {
		            $this->setTable($this->_table);
			    }
		        
		        try {
		            $this->_table = KFactory::get($this->_table);
                } catch (KDatabaseTableException $e) {
                    $this->_table = false;
                }
            }
        }

        return $this->_table;
    }
	
	/**
	 * Method to set a table object attached to the rowset
	 *
	 * @param	mixed	An object that implements KObjectIdentifiable, an object that
	 *                  implements KIdentifierInterface or valid identifier string
	 * @throws	KDatabaseRowException	If the identifier is not a table identifier
	 * @return	KDatabaseRowsetAbstract
	 */
    public function setTable($table)
	{
		if(!($table instanceof KDatabaseTableAbstract))
		{
			if(is_string($table) && strpos($table, '.') === false ) 
		    {
		        $identifier         = clone $this->_identifier;
		        $identifier->path   = array('database', 'table');
		        $identifier->name   = KInflector::tableize($table);
		    }
		    else  $identifier = KFactory::identify($table);
		    
			if($identifier->path[1] != 'table') {
				throw new KDatabaseRowsetException('Identifier: '.$identifier.' is not a table identifier');
			}

			$table = $identifier;
		}

		$this->_table = $table;

		return $this;
	}
	
	/**
	 * Test the connected status of the row.
	 *
	 * @return	boolean	Returns TRUE if we have a reference to a live KDatabaseTableAbstract object.
	 */
    public function isConnected()
	{
	    return (bool) $this->getTable();
	}

	/**
	 * Load the row from the database using the data in the row
	 *
	 * @return object	If successfull returns the row object, otherwise NULL
	 */
	public function load()
	{
		$result = null;
		
		if($this->_new)
		{
            if($this->isConnected())
            {
                $data  = $this->getTable()->filter($this->getData(true), true);
		        $row   = $this->getTable()->select($data, KDatabase::FETCH_ROW);

		        // Set the data if the row was loaded succesfully.
		        if(!$row->isNew())
		        {
			        $this->setData($row->toArray(), false);
			        $this->_modified = array();
			        $this->_new      = false;
			    
			        $this->setStatus(KDatabase::STATUS_LOADED);
			        $result = $this;
		        }
            }
		}
	
		return $result;
	}

	/**
	 * Saves the row to the database.
	 *
	 * This performs an intelligent insert/update and reloads the properties
	 * with fresh data from the table on success.
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function save()
	{
	    $result = false;
	    
	    if($this->isConnected())
	    {  
	        if($this->_new) {
	            $result = $this->getTable()->insert($this);
		    } else {
		        $result = $this->getTable()->update($this); 
		    }
	  
	        if($result !== false) 
	        {
	            if(((integer) $result) > 0) {   
	                $this->_modified = $this->getTable()->filter($this->_modified, true);
	            } 
            }
	    }

		return (bool) $result;
    }

	/**
	 * Deletes the row form the database.
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function delete()
	{
		$result = false;
		
		if($this->isConnected())
		{
            if(!$this->_new) 
		    {
		        $result = $this->getTable()->delete($this);
		    
		        if($result !== false) 
	            {
	                if(((integer) $result) > 0) {   
	                    $this->_new = true;
	                } 
                }
		    }
		}

		return (bool) $result;
	}

	/**
	 * Reset the row data using the defaults
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function reset()
	{
		$result = parent::reset();
		
		if($this->isConnected())
		{
	        if($this->_data = $this->getTable()->getDefaults()) 
	        {
		        $this->setStatus(null);
		        $result = true;
		    }
		}
		
		return $result;
	}

	/**
	 * Count the rows in the database based on the data in the row
	 *
	 * @return integer
	 */
	public function count()
	{
		$result = false;
	    
	    if($this->isConnected())
		{
	        $data   = $this->getTable()->filter($this->getData(true), true);
		    $result = $this->getTable()->count($data);
		}

		return $result;
	}

	/**
	 * Unset a row field
	 *
	 * This function will reset required column to their default value, not required
	 * fields will be unset.
	 *
	 * @param	string  The column name.
	 * @return	void
	 */
	public function __unset($column)
	{
		if($this->isConnected())
		{
	        $field = $this->getTable()->getColumn($column);

		    if(isset($field) && $field->required) {
			    $this->_data[$column] = $field->default;
		    } else {
			    parent::__unset($column);
		    }
		}
	}

	/**
	 * Search the mixin method map and call the method or trigger an error
	 *
	 * This functions overloads KDatabaseRowAbstract::__call and implements 
	 * a just in time mixin strategy. Available table behaviors are only mixed 
	 * when needed.
	 *
	 * @param  string 	The function name
	 * @param  array  	The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, array $arguments)
	{ 
	    // If the method hasn't been mixed yet, load all the behaviors.
	    if($this->isConnected() && !isset($this->_mixed_methods[$method]))
		{
			foreach($this->getTable()->getBehaviors() as $behavior) {
				$this->mixin($behavior);
			}
		}

		return parent::__call($method, $arguments);
	}
}