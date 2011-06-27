<?php
/**
 * @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class ContactViewContact extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		$user		= &JFactory::getUser();
		$pathway	= &$mainframe->getPathway();
		$document	= & JFactory::getDocument();
		$model		= &$this->getModel();

		// Get the parameters of the active menu item
		$menus	= &JSite::getMenu();
		$menu    = $menus->getActive();

		$pparams = &$mainframe->getParams('com_contact');

		// Push a model into the view
		$model		= &$this->getModel();
		$modelCat	= &$this->getModel( 'Category' );

		// Selected Request vars
		// ID may come from the contact switcher
		if (!($contactId	= JRequest::getInt( 'contact_id',	0 ))) {
			$contactId	= JRequest::getInt( 'id',			$contactId );
		}

		// query options
		$options['id']	= $contactId;
		$options['aid']	= $user->get('aid', 0);

		$contact	= $model->getContact( $options );

		// check if we have a contact
		if (!is_object( $contact )) {
			JError::raiseError( 404, 'Contact not found' );
			return;
		}
		
		// check if access is registered/special
		if (($contact->access > $user->get('aid', 0)) || ($contact->category_access > $user->get('aid', 0))) {
			$uri		= JFactory::getURI();
			$return		= $uri->toString();
			
			$url  = 'index.php?option=com_user&view=login';
			$url .= '&return='.base64_encode($return);
			
			$mainframe->redirect($url, JText::_('You must login first') );
			
		}

		$options['category_id']	= $contact->catid;
		$options['order by']	= 'cd.default_con DESC, cd.ordering ASC';

		$contacts = $modelCat->getContacts( $options );

		// Set the document page title
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu ) && isset($menu->query['view']) && $menu->query['view'] == 'contact' && isset($menu->query['id']) && $menu->query['id'] == $contact->id) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$pparams->set('page_title',	$contact->name);
			}
		} else {
			$pparams->set('page_title',	$contact->name);
		}
		$document->setTitle( $pparams->get( 'page_title' ) );

		//set breadcrumbs
		if (isset( $menu ) && isset($menu->query['view']) && $menu->query['view'] != 'contact'){
			$pathway->addItem($contact->name, '');
		}

		// Adds parameter handling
		$contact->params = new JParameter($contact->params);

		$pparams->merge($contact->params);

		// Handle component/menu overides for some contact parameters if set
		/*
		$contact->params->def('contact_icons',	$pparams->get('contact_icons'));
		$contact->params->def('icon_address',	$pparams->get('icon_address'));
		$contact->params->def('icon_email',		$pparams->get('icon_email'));
		$contact->params->def('icon_telephone',	$pparams->get('icon_telephone'));
		$contact->params->def('icon_fax',		$pparams->get('icon_fax'));
		$contact->params->def('icon_misc',		$pparams->get('icon_misc'));
		$contact->params->def('show_position',	$pparams->get('show_position'));
		$contact->params->def('show_email',		$pparams->get('show_email'));
		$contact->params->def('show_telephone',	$pparams->get('show_telephone'));
		$contact->params->def('show_mobile',	$pparams->get('show_mobile'));
		$contact->params->def('show_fax',		$pparams->get('show_fax'));
		$contact->params->def('allow_vcard',	$pparams->get('allow_vcard'));
		*/

		// Handle email cloaking
		if ($contact->email_to && $contact->params->get('show_email')) {
			$contact->email_to = JHTML::_('email.cloak', $contact->email_to);
		}

		if ($contact->params->get('show_street_address') || $contact->params->get('show_suburb') || $contact->params->get('show_state') || $contact->params->get('show_postcode') || $contact->params->get('show_country'))
		{
			if (!empty ($contact->address) || !empty ($contact->suburb) || !empty ($contact->state) || !empty ($contact->country) || !empty ($contact->postcode)) {
				$contact->params->set('address_check', 1);
			}
		} else {
			$contact->params->set('address_check', 0);
		}

		 // Manage the display mode for contact detail groups
		switch ($contact->params->get('contact_icons'))
		{
			case 1 :
				// text
				$contact->params->set('marker_address', 	JText::_('Address').": ");
				$contact->params->set('marker_email', 		JText::_('Email').": ");
				$contact->params->set('marker_telephone', 	JText::_('Telephone').": ");
				$contact->params->set('marker_fax', 		JText::_('Fax').": ");
				$contact->params->set('marker_mobile',		JText::_('Mobile').": ");
				$contact->params->set('marker_misc', 		JText::_('Information').": ");
				$contact->params->set('column_width', 		'100');
				break;

			case 2 :
				// none
				$contact->params->set('marker_address', 	'');
				$contact->params->set('marker_email', 		'');
				$contact->params->set('marker_telephone', 	'');
				$contact->params->set('marker_mobile', 	'');
				$contact->params->set('marker_fax', 		'');
				$contact->params->set('marker_misc', 		'');
				$contact->params->set('column_width', 		'0');
				break;

			default :
				// icons
				$image1 = JHTML::_('image.site', 'con_address.png', 	'/media/com_contact/images/', $contact->params->get('icon_address'), 	'/media/com_contact/images/', JText::_('Address').": ");
				$image2 = JHTML::_('image.site', 'emailButton.png', 	'/media/com_contact/images/', $contact->params->get('icon_email'), 		'/media/com_contact/images/', JText::_('Email').": ");
				$image3 = JHTML::_('image.site', 'con_tel.png', 		'/media/com_contact/images/', $contact->params->get('icon_telephone'), 	'/media/com_contact/images/', JText::_('Telephone').": ");
				$image4 = JHTML::_('image.site', 'con_fax.png', 		'/media/com_contact/images/', $contact->params->get('icon_fax'), 		'/media/com_contact/images/', JText::_('Fax').": ");
				$image5 = JHTML::_('image.site', 'con_info.png', 		'/media/com_contact/images/', $contact->params->get('icon_misc'), 		'/media/com_contact/images/', JText::_('Information').": ");
				$image6 = JHTML::_('image.site', 'con_mobile.png', 		'/media/com_contact/images/', $contact->params->get('icon_mobile'), 	'/media/com_contact/images/', JText::_('Mobile').": ");

				$contact->params->set('marker_address', 	$image1);
				$contact->params->set('marker_email', 		$image2);
				$contact->params->set('marker_telephone', 	$image3);
				$contact->params->set('marker_fax', 		$image4);
				$contact->params->set('marker_misc',		$image5);
				$contact->params->set('marker_mobile', 	$image6);
				$contact->params->set('column_width', 		'40');
				break;
		}

		JHTML::_('behavior.formvalidation');

		$this->assignRef('contact',		$contact);
		$this->assignRef('contacts',	$contacts);
		$this->assignRef('params',		$pparams);

		parent::display($tpl);
	}
}
