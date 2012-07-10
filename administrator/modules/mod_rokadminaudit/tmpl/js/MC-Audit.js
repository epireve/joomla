/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){var a=this.RokAudit=new Class({Implements:[Options,Events],options:{url:"",amount:0,start:0,limit:5,details:"low"},initialize:function(c,b){this.setOptions(b);
this.element=document.id(c)||document.getElement(c)||null;if(!this.element){return this;}this.start=this.options.start;this.limit=this.options.limit;this.details=this.options.details;
this.amount=this.options.amount;this.loaded=this.limit;this.left=this.amount-this.loaded;this.bounds={more:{click:this.attachMore.bind(this)},select:{change:this.attachSelect.bind(this)},ajax:{request:this.ajaxRequest.bind(this),success:this.ajaxSuccess.bind(this)}};
this.ajax=new Request({url:this.options.url,method:"post",onRequest:this.bounds.ajax.request,onSuccess:this.bounds.ajax.success});this.more=this.element.getElement(".rok-more a");
this.loader=this.element.getElement(".loader");this.container=this.element.getElement("ul");this.detailsSelect=this.element.getElement("#rok-audit-details");
this.more.addEvents(this.bounds.more);this.detailsSelect.addEvents(this.bounds.select);return this;},attachMore:function(b){b.stop();if(this.left<=0){this.left=0;
this.disable();return;}this.start=this.limit;this.limit=this.limit+this.options.limit;this.details=this.details;this.ajax.send({data:{start:this.start,limit:this.limit,details:this.details}});
},attachSelect:function(b){this.details=this.detailsSelect.get("value");this.ajax.send({data:{start:0,limit:this.limit,details:this.details}});},enable:function(){this.element.removeClass("disabled");
},disable:function(){this.element.addClass("disabled");},ajaxRequest:function(){this.loader.setStyle("display","block");},ajaxSuccess:function(b){this.loader.setStyle("display","none");
var c=new Element("div").set("html",b);c.getElements("li").inject(this.container.empty());this.amount=c.getElement("input[type=hidden]").get("value").toInt();
this.loaded=this.limit;this.left=this.amount-this.limit;if(this.left<=0){this.left=0;this.disable();}else{this.enable();}}});})());