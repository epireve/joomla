/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a={init:function(){var l=document.getElement("#joomla_token > input[type=hidden]"),c=document.id("form-login"),d=document.id("lang"),j=document.id("hidden_lang");
if(Browser.ie&&Browser.version<10){a.placeholder();}if(l&&c&&d&&j){var e=l.get("name"),g=document.html.get("lang"),f=c.get("action"),k=g.split("-");k[0]=k[0].toLowerCase();
k[1]=k[1].toUpperCase();g=k[0]+"-"+k[1];a.ajax=new Request({url:f,method:"post",onSuccess:function(){window.location.reload();}});a.data={option:"com_login",task:"login"};
a.data[e]=1;d.addEvent("change",function(){var n=d.get("value"),m=n.split("-");m[0]=m[0].toLowerCase();m[1]=m[1].toUpperCase();n=m[0]+"-"+m[1];a.data.lang=n;
j.set("value",n);a.ajax.send({data:a.data});});j.set("value",g);if(d.get("value").toLowerCase()!=g.toLowerCase()){k=g.split("-");k[0]=k[0].toLowerCase();
k[1]=k[1].toUpperCase();g=k[0]+"-"+k[1];var b=document.getElement("option[value="+g+"]");if(b){var i=b.getParent(),h=i.getChildren().get("value").indexOf(g);
if(h!=-1){document.getElement("#mc-status .select-active").set("html",b.innerHTML);}b.fireEvent("click");}}}},placeholder:function(){var b=document.id("modlgn_username"),c=document.id("modlgn_passwd");
if(b&&c){b.store("placeholder",b.get("placeholder"));c.store("placeholder",c.get("placeholder")).store("type","password");$$(b,c).addEvents({focus:function(){this.removeClass("placeholder");
if(this.value==this.retrieve("placeholder")){this.value="";}if(this.retrieve("type")=="password"&&this.get("type")=="text"){this.set("type","password");
}},blur:function(){if(this.value===""){this.value=this.get("placeholder");this.addClass("placeholder");}if(this.retrieve("type")=="password"){if(this.get("type")=="password"&&this.value==this.get("placeholder")){this.set("type","text");
}}}}).fireEvent("blur");}}};if(!this.MC){this.MC={};}this.MC.Lang=a;})();