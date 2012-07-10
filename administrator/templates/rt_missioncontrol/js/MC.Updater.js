/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a={init:function(){var b=$$(".mc-update a");a.ajax=new Request({url:"index.php?process=ajax&model=updater",onRequest:a.request,onSuccess:a.success});
b.each(a.addAjax);},addAjax:function(b){b.addEvent("click",function(c){c.stop();if(a.ajax.running){return;}var d=this.getNext();if(!d.hasClass("spinner")){d=this.getPrevious();
}if(!d.hasClass("spinner")){d=null;}d.setStyle("display","block");a.ajax.spinner=d;a.ajax.request();});},request:function(){},success:function(d){if(a.ajax.spinner){a.ajax.spinner.setStyle("display","none");
}a.ajax.spinner=null;var b=new Element("div").set("html",d);b=b.getFirst();var c=document.id(document.body).getElement(".mc-update-check");c.className=b.className;
c.innerHTML=b.innerHTML;a.addAjax(c.getElement("a"));}};if(!this.MC){this.MC={};}this.MC.Updater=a;window.addEvent("domready",a.init);})();