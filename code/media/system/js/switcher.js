/**
* @version		$Id: switcher.js 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Config
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
 * Switcher behavior for configuration component
 *
 * @package		Joomla.Extensions
 * @subpackage	Config
 * @since		1.5
 */
var JSwitcher = new Class({

	toggler : null, //holds the active toggler
	page    : null, //holds the active page

	options : {
		cookieName: 'switcher'
	},

	initialize: function(toggler, element, options)
	{
		this.setOptions(options);

		var self = this;

		togglers = $ES('a', toggler);
		for (i=0; i < togglers.length; i++) {
			togglers[i].addEvent( 'click', function() { self.switchTo(this); } );
		}

		//hide all
		elements = element.getElements('div[id^=page-]');
		for (i=0; i < elements.length; i++) {
			this.hide(elements[i])
		}

		this.toggler = $E('a.active', toggler);
		this.page    = document.id('page-'+ this.toggler.id);

		this.show(this.page);
		if (this.options.cookieName)
		{
			if((page = Cookie.get(this.options.cookieName))) {
				this.switchTo(document.id(page));
			}
		}
	},

	switchTo: function(toggler)
	{
		page = $chk(toggler) ? document.id('page-'+toggler.id) : null;
		if(page && page != this.page)
		{
			//hide old element
			if(this.page) {
				this.hide(this.page);
			}

			//show new element
			this.show(page);

			toggler.addClass('active');
			if (this.toggler) {
				this.toggler.removeClass('active');
			}
			this.page    = page;
			this.toggler = toggler;
			Cookie.set(this.options.cookieName, toggler.id);
		}
	},

	hide: function(element) {
		element.setStyle('display', 'none');
	},

	show: function (element) {
		element.setStyle('display', 'block');
	}
});

JSwitcher.implement(new Options);

document.switcher = null;
window.addEvent('domready', function(){
 	toggler = document.id('submenu')
  	element = document.id('config-document')
  	if(element) {
  		document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getAttribute('class')});
  	}
});
