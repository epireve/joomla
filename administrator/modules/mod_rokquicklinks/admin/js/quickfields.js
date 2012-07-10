/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a={add:function(){var d=this.getParent(".quicklinks-block");var e=d.clone(true);b.sortables.addItems(e);var c=e.getElement("select");c.addEvent("change",a.selects.change);
c.addEvent("blur",a.selects.blur);c.getChildren().addEvent("mouseenter",a.opts);e.getElement(".quicklinks-add").addEvent("click",a.add);e.getElement(".quicklinks-remove").addEvent("click",a.remove);
e.inject(d,"after");b.store();},remove:function(){var c=this.getParent(".quicklinks-block");b.sortables.removeItems(c);c.dispose();b.store();},selects:{change:function(){this.getParent(".quicklinks-block").getElement("img").set("src",b.path+this.value);
b.store();},blur:function(){this.getParent(".quicklinks-block").getElement("img").set("src",b.path+this.value);}},opts:function(){this.getParent(".quicklinks-block").getElement("img").set("src",b.path+this.value);
},inputs:function(){b.store();}};var b={init:function(){var g=document.id("quicklinks-admin");if(!g){return;}b.sortables=new Sortables(g,{handle:".quicklinks-move",opacity:0.5,constrain:true,onComplete:b.store});
b.path=document.id("quicklinks-dir").value;var h=$$(".quicklinks-add"),f=$$(".quicklinks-remove"),e=$$(".quicklinks-select"),c=$$(".quick-input"),d=e.getChildren();
h.each(function(i){i.addEvent("click",a.add);});f.each(function(i){i.addEvent("click",a.remove);});e.each(function(i){i.addEvents({change:a.selects.change,blur:a.selects.blur});
});d.each(function(i){i.addEvent("mouseover",a.opts);});c.each(function(i){i.addEvent("keyup",a.inputs);});},order:function(){var d=$$(".quicklinks-block"),c=["title","link","icon"];
d.getElements("input, select").each(function(f,e){f.each(function(g,h){g.id="jform_params_"+c[h]+"-"+(e+1);g.name="jform[params]["+c[h]+"-"+(e+1)+"]";});
});},store:function(){b.order();var e=$$(".quicklinks-block"),c=[],d=["title","link","icon"];e.getElements("input, select").each(function(h,f){var g={};
h.each(function(i,k){g[d[k]]=i.get("value");});c.push(g);});document.id("jform_params_quickfields").value=JSON.encode(c);}};window.addEvent("domready",b.init);
})();