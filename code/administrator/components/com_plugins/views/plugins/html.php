<?php
/**
 * @version     $Id: html.php 1769 2011-06-16 14:45:53Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Plugins HTML View class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins    
 */
class ComPluginsViewPluginsHtml extends ComPluginsViewHtml
{
	public function display()
	{
        $this->types = KFactory::tmp('admin::com.plugins.model.plugins')->getColumn('type');

		return parent::display();
	}
}