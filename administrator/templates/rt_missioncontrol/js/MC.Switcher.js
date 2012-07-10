/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
var JSwitcher=new Class({toggler:null,page:null,initialize:function(d,c,b){this.setOptions(b);var a=this;togglers=d.getElements("a");for(i=0;i<togglers.length;
i++){togglers[i].addEvent("click",function(){a.switchTo(this);});}elements=c.getElements("div[id^=page-]");for(i=0;i<elements.length;i++){this.hide(elements[i]);
}this.toggler=d.getElement("a.active");this.page=document.id("page-"+this.toggler.id);this.show(this.page);},switchTo:function(a){page=$chk(a)?document.id("page-"+a.id):null;
if(page&&page!=this.page){if(this.page){this.hide(this.page);}this.show(page);a.addClass("active");if(this.toggler){this.toggler.removeClass("active");
}this.page=page;this.toggler=a;}},hide:function(a){a.setStyle("display","none");},show:function(a){a.setStyle("display","block");}});JSwitcher.implement(new Options);
document.switcher=null;