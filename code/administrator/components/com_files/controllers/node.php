<?php
/**
 * @version     $Id: node.php 2029 2011-06-26 17:00:16Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Node Controller Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 */

class ComFilesControllerNode extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'persistable' => false,
			'request'     => array(
				'identifier' => 'files.files'
			)
		));

		parent::_initialize($config);
	}

	public function loadState(KCommandContext $context)
	{
		parent::loadState($context);

		KFactory::get('admin::com.files.model.configs')
			->set($this->getRequest())
			->getItem();
	}
}
