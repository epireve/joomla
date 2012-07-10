/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){Fx.Transitions.shake=function(b){return function(c){return Math.sin(c*b*2*Math.PI);};};var a={effect:function(){var b=document.getElement(".error.message")||document.getElement(".message.message");
if(!b){return false;}return new Fx.Tween(b,{duration:400});},shake:function(d){var b=(!a.fx)?a.effect():a.fx;if(!b){return;}b.setOptions({transition:Fx.Transitions.shake(d),duration:400});
var c=b.element.getStyle("margin-left").toInt();b.start("margin-left",[c+5,c]);}};if(!this.MC){this.MC={};}this.MC.Notice=a;})();