<?php
/**
 * @version     $Id: template.php 1925 2011-06-23 19:00:37Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Templates
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Controller Class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Templates
 */
class ComTemplatesControllerTemplate extends ComDefaultControllerDefault
{
    protected function _actionRead(KCommandContext $context)
    {
        $template = parent::_actionRead($context);

        if(isset($template->name)) {
            KFactory::get('lib.joomla.language')->load('tpl_'.$template->name, JPATH_ADMINISTRATOR);
        }

        return $template;
    }
}