/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

window.addEvent('domready', function() {
	var itemModules = document.id('jform_params_fusion_children_type1'), itemPositions = document.id('jform_params_fusion_children_type2'), itemMenu = document.id('jform_params_fusion_children_type0');
	var blockModules = document.id('jform_params_fusion_modules'), blockPositions = document.id('jform_params_fusion_module_positions');
	
	if (blockModules) var blockModulesTr = blockModules.getParent('li');
	if (blockPositions) var blockPositionsTr = blockPositions.getParent('li');
	if (itemModules && blockModules) {
		itemModules.addEvent('click', function() {
			if (blockPositionsTr) blockPositionsTr.setStyle('display', 'none');
			if (blockModulesTr) blockModulesTr.setStyle('display', 'block');
			var wrapper = blockModulesTr.getParent('.pane-slider');
			if (wrapper.getStyle('height').toInt() > 0) {
				wrapper.setStyle('height', blockModulesTr.getParent('.panelform').getSize().y);
			}
		});
	}
	if (itemPositions && blockPositions) {
		itemPositions.addEvent('click', function() {
			if (blockModulesTr) blockModulesTr.setStyle('display', 'none');
			if (blockPositionsTr) blockPositionsTr.setStyle('display', 'block');
			var wrapper = blockPositionsTr.getParent('.pane-slider');
			if (wrapper.getStyle('height').toInt() > 0) {
				wrapper.setStyle('height', blockPositionsTr.getParent('.panelform').getSize().y);
			}
		});
	}
	if (itemMenu) {
		itemMenu.addEvent('click', function() {
			if (blockModulesTr) blockModulesTr.setStyle('display', 'none');
			if (blockPositionsTr) blockPositionsTr.setStyle('display', 'none');
			var wrapper = blockModulesTr.getParent('.pane-slider');
			if (wrapper.getStyle('height').toInt() > 0) {
				wrapper.setStyle('height', blockModulesTr.getParent('.panelform').getSize().y);
			}
		});
	}

	if (itemMenu.checked) itemMenu.fireEvent('click');
	if (itemModules.checked) itemModules.fireEvent('click');
	if (itemPositions.checked) itemPositions.fireEvent('click');
});