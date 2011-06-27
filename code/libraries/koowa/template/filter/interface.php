<?php
/**
* @version      $Id: interface.php 3416 2011-06-02 22:43:51Z johanjanssens $
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template filter interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Filter 
 */
interface KTemplateFilterInterface  extends KCommandInterface, KObjectIdentifiable
{
  	/**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate();
}