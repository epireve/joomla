/*
---

name: Touch
description: Class to aid the retrieval of the cursor movements
license: MIT-Style License (http://mootools.net/license.txt)
copyright: Valerio Proietti (http://mad4milk.net)
requires: [Core/MooTools, Core/Array, Core/Function, Core/Number, Core/String, Core/Class, Core/Events, Core/Element]
provides: Touch

...
*/

var Touch=new Class({Implements:Events,initialize:function(a){this.element=document.id(a);this.bound={start:this.start.bind(this),move:this.move.bind(this),end:this.end.bind(this)};if(Browser.Platform.ipod){this.context=this.element;this.startEvent="touchstart";this.endEvent="touchend";this.moveEvent="touchmove";}else{this.context=document;this.startEvent="mousedown";this.endEvent="mouseup";this.moveEvent="mousemove";}this.attach();},attach:function(){this.element.addListener(this.startEvent,this.bound.start);},detach:function(){this.element.removeListener(this.startEvent,this.bound.start);},start:function(a){this.preventDefault(a);document.body.style.WebkitUserSelect="none";this.hasDragged=false;this.context.addListener(this.moveEvent,this.bound.move);this.context.addListener(this.endEvent,this.bound.end);var b=this.getPage(a);this.startX=b.pageX;this.startY=b.pageY;this.fireEvent("start");},move:function(a){this.preventDefault(a);var b=this.getPage(a);this.deltaX=b.pageX-this.startX;this.deltaY=b.pageY-this.startY;this.hasDragged=!(this.deltaX===0&&this.deltaY===0);if(this.hasDragged){this.fireEvent("move",[this.deltaX,this.deltaY]);}},end:function(a){this.preventDefault(a);document.body.style.WebkitUserSelect="";this.context.removeListener(this.moveEvent,this.bound.move);this.context.removeListener(this.endEvent,this.bound.end);this.fireEvent((this.hasDragged)?"end":"cancel");},preventDefault:function(a){if(a.preventDefault){a.preventDefault();}else{a.returnValue=false;}},getPage:function(b){if(b.targetTouches){b=b.targetTouches[0];}if(b.pageX!=null&&b.pageY!=null){return{pageX:b.pageX,pageY:b.pageY};}var a=(!document.compatMode||document.compatMode=="CSS1Compat")?document.documentElement:document.body;return{pageX:b.clientX+a.scrollLeft,pageY:b.clientY+a.scrollTop};}});Touch.build="%build%";