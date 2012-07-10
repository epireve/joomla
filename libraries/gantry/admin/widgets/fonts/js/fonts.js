/**
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var GantryFonts={init:function(a,e,f){var d=document.id(GantryParamsPrefix+a),c=document.id(GantryParamsPrefix+e),b=document.id(GantryParamsPrefix+f);if(d&&c){d.addEvent("onChange",function(){var h=c.value;if(b){var g=b.getElements("."+h);if(this.value=="1"){b.getPrevious().getElements("li."+h).removeClass("disabled");g.removeProperty("disabled");}else{b.getPrevious().getElements("li."+h).addClass("disabled");g.setProperty("disabled","disabled");}}});d.fireEvent("onChange");}}};