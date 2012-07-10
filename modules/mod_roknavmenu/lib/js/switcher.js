/**
 * Switcher.js RokNavMenu Fields Switcher
 *
 * @package		Joomla
 * @subpackage	RokNavMenu Fields Switcher
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */

(function(){

	var NavMenuSwitcher = NS = {
		init: function(el, label){
			NS.list = document.id(el);
			NS.label = document.id(label);
			
			NS.dummy = new Element('<form></form>').inject(document.body).setStyle('display', 'none');
			NS.dummy2 = new Element('<form></form>').inject(document.body).setStyle('display', 'none');
			
			NS.sets = NS.getSets();
			NS.rePopulate();
			
			NS.sets = $$(NS.sets);
			NS.list.addEvent('change', NS.change.bind(NS));
		},
		
		getSets: function(){
			var options = [];
			NS.list.getChildren().each(function(opt){
				var set = document.id('themeset-' + opt.get('value'));
				if (!set) return;
				
				options.push(set);
				
				if (opt.get('value') != NS.list.get('value')) set.inject(NS.dummy);
			});
			
			return options;
		},
		
		rePopulate: function(){
			NS.sets.each(function(set){
				var forms = set.getElements('input, select');
				forms.each(function(form){
					if (form.get('tag') == 'select'){
						var selected = form.getElement('option[selected]');
						form.set('value', selected.get('value'));
					}
					
					if (form.get('tag') == 'input' && form.get('type') == 'radio' && form.checked){
						form.checked = true;
					}
				});
			});
		},
		
		change: function(){
			var value = NS.list.get('value');
			var active = document.id('themeset-' + value);
			
			if (active) active.inject(NS.dummy2);
			NS.sets.filter(function(set) {
				return set != active;
			}).inject(NS.dummy);
			if (active) active.inject(NS.label, 'after');
		}
	};

	window.addEvent('domready', NS.init.pass(['jform_params_theme', 'jform_params_themeoptions-lbl']));

})();