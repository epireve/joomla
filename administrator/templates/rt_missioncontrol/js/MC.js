/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var b=this.MC={init:function(){a.init();if(this.MC.Notice){this.MC.Notice.shake.delay(500,this.MC.Notice.shake,3);}if(this.MC.Lang){this.MC.Lang.init();
}b.fixIOS();b.QCC();b.QCheckins();b.Session();},fixIOS:function(){var d=document.id("mctrl-menu");if(d){var c=d.getElements("li");if(c.length){c.addEvent("mouseenter",function(f){new Event(f).stop();
});}}},QCC:function(){var c=$$(".qcc");c.each(function(d){d.store("value",d.getElement("a").get("text"));d.store("badge",d.getElement(".badge"));d.store("ajax",new Request({url:"index.php?process=ajax&model=quickcachecleaner&action=clean",onRequest:function(e){b.QCCRequest(this,d,e);
},onSuccess:function(e){b.QCCSuccess(this,d,e);}}));d.addEvent("click",function(g){g.stop();var f=this.retrieve("ajax");if(!f.isRunning()){f.send();}});
});},QCCRequest:function(d,e,c){e.getElement("a").set("text","Cleaning Cache...");},QCCSuccess:function(d,e,c){e.getElement("a").set("text",e.retrieve("value"));
e.retrieve("badge").set("text",c);},QCheckins:function(){var c=$$(".qci");c.each(function(d){d.store("value",d.getElement("a").get("text"));d.store("badge",d.getElement(".badge"));
d.store("ajax",new Request({url:"index.php?process=ajax&model=quickcheckin&action=checkin",onRequest:function(e){b.QCheckinsRequest(this,d,e);},onSuccess:function(e){b.QCheckinsSuccess(this,d,e);
}}));d.addEvent("click",function(g){g.stop();var f=this.retrieve("ajax");if(!f.isRunning()){f.send();}});});},QCheckinsRequest:function(e,d,c){d.getElement("a").set("text","Cleaning Checkins...");
},QCheckinsSuccess:function(e,d,c){d.getElement("a").set("text",d.retrieve("value"));d.retrieve("badge").set("text",c);},Session:function(){if(typeof keepAlive=="function"){return;
}var c=document.getElement(".session_expire"),d=document.getElement(".session_progress");b.timeout=(typeof MCSessionTimeout!="undefined")?MCSessionTimeout/1000:0;
b.countdown=b.timeout;if(c&&d&&b.timeout){b.sessionTip=new Element("div",{"class":"session-tip"}).inject(c,"top").setStyle("opacity",0).set("text","-");
d.setStyle("width","100%");b.update=b.UpdateMeter.periodical(1000,b.UpdateMeter,d);}},UpdateMeter:function(e){var d=(--b.countdown*100)/b.timeout;var c=new Date();
c.set("ms",b.countdown*1000);b.sessionTip.fade("in").set("text",c.timeDiff(new Date()," "));b.sessionTip.setStyles({left:-(b.sessionTip.offsetWidth+5)});
if(b.countdown==60||b.countdown<=30){b.sessionTip.highlight("#fbda4e");}if(b.countdown<=10){b.sessionTip.highlight("#ec5b4f");}if(b.countdown<=0){clearTimeout(b.update);
b.sessionTip.addClass("expired");b.sessionTip.set("text",MCSessionLang.expired);b.sessionTip.setStyles({width:b.sessionTip.offsetWidth,left:-(b.sessionTip.offsetWidth+10)});
}if(e){e.setStyle("width",d+"%");}}};var a=this.MC.SelectBoxes={init:function(){this.selects=$$(".dropdown select");this.selects.each(function(c){c.getParent().addEvent("mouseenter",function(d){d.stop();
});this.build(c);},this);},build:function(f){var c=new Element("a",{"class":"mc-dropdown-selected"}).inject(f,"before");var e=new Element("ul",{"class":"mc-dropdown"}).inject(c,"after");
f.setStyle("display","none");f.getChildren().each(function(j,h){var k=j.get("selected")||false;var l=new Element("a",{href:"#"}).set("text",j.get("text"));
var g=new Element("li").inject(e).adopt(l);g.addEvent("click",function(i){i.stop();f.selectedIndex=h;c.getFirst().set("text",j.get("text"));f.fireEvent("change");
});g.store("selected",k);g.store("value",j.get("value")||"");if(k){c.set("html",'<span class="select-active">'+j.get("text")+"</span>");}});var d=new Element("span",{"class":"select-arrow"}).set("html","&#x25BE;").inject(c);
}};window.addEvent("domready",b.init);})();