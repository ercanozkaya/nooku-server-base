<?php
/**
 * @version		$Id: html.php 1346 2011-05-18 23:02:23Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Search
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Search Html View Class
 *
 * @author    	Arunas Mazeika <http://nooku.assembla.com/profile/amazeika>
 * @category 	Nooku
 * @package     Nooku_Server
 * @subpackage  Search
 */
class ComSearchViewResultsHtml extends ComDefaultViewHtml
{
	/**
	 * Return the views output
	 * 
	 * This function will auto assign the model data to the view if the auto_assign
	 * property is set to TRUE.
	 *
	 * @return string     The output of the view
	 */
	public function display()
	{
		$params = KFactory::get('lib.joomla.application')->getParams();	
		$this->assign('params', $params);
		
		return parent::display();
	}
}