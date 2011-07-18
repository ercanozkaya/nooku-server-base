<?php
/**
 * @version		$Id: users.php 2092 2011-06-29 12:57:25Z ercanozkaya $
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Users Database Table Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 */
class ComUsersDatabaseTableUsers extends KDatabaseTableDefault
{
	public function __construct(KConfig $config)
	{
	    parent::__construct($config);

	    //Make sure the email and username fields are unique
	    $this->getColumn('email')->unique = true;
	    $this->getColumn('username')->unique = true;

	    $this->getColumn('users_group_id')->default = 0;
	    $this->getColumn('enabled')->default = 1;
	}

    protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'name'				=> 'users',
			'base' 				=> 'users',
			'column_map'		=> array(
				'users_user_id'		=> 'id',
				'group_name'		=> 'usertype',
				'enabled'			=> 'block',
				'send_email'		=> 'sendEmail',
				'users_group_id'	=> 'gid',
				'registered_on'		=> 'registerDate',
				'last_visited_on'	=> 'lastvisitDate'
			)
		));

		parent::_initialize($config);
	}
}