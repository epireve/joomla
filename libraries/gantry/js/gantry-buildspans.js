/**
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var GantryBuildSpans=function(c,b,a){(c.length).times(function(d){var h="."+c[d];var g=function(f){f.setStyle("visibility","visible");var e=f.get("text");var k=e.split(" ");first=k[0];rest=k.slice(1).join(" ");html=f.innerHTML;if(rest.length>0){var j=f.clone().set("text"," "+rest),i=new Element("span").set("text",first);i.inject(j,"top");j.replaces(f);}};$$(h).each(function(e){b.each(function(f){e.getElements(f).each(function(i){var j=i.getFirst();if(j&&j.get("tag")=="a"){g(j);}else{g(i);}});});});});};