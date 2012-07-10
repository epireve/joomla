/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

var Fusion = new Class({
	Implements: [Options],
	version: "1.9.8",
	options: {
		centered: false,
		tweakInitial: {x: 0, y: 0},
		tweakSubsequent: {x: 0, y: 0},
		tweakSizes: {'width': 0, 'height': 0},
		pill: true,
		direction: { x: 'right', y: 'down' },		
		effect: 'slide and fade',
		orientation: 'horizontal',
		opacity: 1,
		hideDelay: 50000,
		menuFx: {
			duration: 500,
			transition: 'quad:out'
		},
		pillFx: {
			duration: 400,
			transition: 'back:out'
		}
	},
	initialize: function(element, options) {
		this.element = $$(element)[0];
		this.id = $$('.fusion')[0];
		if (this.id) this.id = this.id.id;
		else this.id = '';
		this.setOptions(options);
		var links = this.element.getElements('.item'), opts = this.options;
		
		this.rtl = document.id(document.body).getStyle('direction') == 'rtl';
		
		this.options.tweakSubsequent.x -= this.options.tweakSizes.width / 2;
		this.options.tweakSubsequent.y -= this.options.tweakSizes.height / 2;
		
		if (this.rtl) {
			this.options.direction.x = 'left';
			this.options.tweakInitial.x *= -1;
			this.options.tweakSubsequent.x *= -1;
		}
		
		if (this.options.pill) {
			var pill = new Element('div', {'class': 'fusion-pill-l'}).inject(this.element, 'after').setStyle('display', 'none'), self = this;
			new Element('div', {'class': 'fusion-pill-r'}).inject(pill);
			this.pillsRoots = this.element.getElements('.root');
			var active = this.element.getElement('.active');
			this.pillsMargins = pill.getStyle('margin-left').toInt() + pill.getStyle('margin-right').toInt();
			this.pillsTopMargins = pill.getStyle('margin-top').toInt() + pill.getStyle('margin-bottom').toInt();
			
			if (!active) {
				this.options.pill = false;
			} else {
				pill.setStyle('display', 'block');
				this.pillsDefaults = {
					'left': active.offsetLeft,
					'width': active.offsetWidth - this.pillsMargins,
					'top': active.offsetTop
				};
			
				this.pillFx = new Fx.Morph(pill, {
					duration: opts.pillFx.duration, 
					transition: opts.pillFx.transition, 
					link: 'cancel'
				}).set(this.pillsDefaults);
			
				var ghosts = this.pillsRoots.filter(function(item) { return !item.hasClass('parent'); });
				$$(ghosts).addEvents({
					'mouseenter': function() {
						self.ghostRequest = true;
						self.pillFx.start({
							'left': this.offsetLeft,
							'width': this.offsetWidth - self.pillsMargins,
							'top': this.offsetTop
						});
					},
					'mouseleave': function() {
						self.ghostRequest = false;
						self.pillFx.start(self.pillsDefaults);
					}
				});
			}
		};
		
		this.parentLinks = {};
		this.parentSubMenus = {};
		this.childMenu = {};
		this.menuType = {};
		this.subMenus = [];
		this.hideAllMenusTimeout = null;
		this.subMenuZindex = 1;
		
		links.each(function(link, i) {
			link.getCustomID();
			this.parentLinks[link.id] = link.getParent().getParents('li').getElement('.item');
			this.childMenu[link.id] = link.getNext('.fusion-submenu-wrapper') || link.getNext('ul') || link.getNext('ol');
			if (this.childMenu[link.id]) link.fusionSize = this.childMenu[link.id].getCoordinates();
			if (this.childMenu[link.id] && Browser.Engine.trident) {
				var ul = this.childMenu[link.id].getElement('ul');
				if (ul) {
					var padding = ul.getStyle('padding-bottom').toInt() || 0;
					link.fusionSize.height += padding;
				}
			}
			
			var type = 'subseq';
			if (document.id(link.getParent('.fusion-submenu-wrapper') || link.getParent('ul') || link.getParent('ol')) === this.element) type = 'init';
			
			this.menuType[link.id] = type;
			
		}, this);
		
		this.jsContainer = new Element('div', {'class': 'fusion-js-container menutop'}).inject(document.body);
		this.jsContainer.addEvents({
			'mouseenter': function() { window.RTFUSION = true; },
			'mouseleave': function() { window.RTFUSION = false; }
		});
		var cls = this.element.className.replace("menutop", "");
		if (this.id.length) this.jsContainer.id = this.id;
		if (cls.length) {
			var newCls = "fusion-js-container " + cls + " menutop";
			this.jsContainer.className = newCls.clean();
		}
		
		var els = this.element.getElements('.fusion-submenu-wrapper');
		if (!els.length) els = this.element.getElements('ul');
		els.each(function(item,index){
			var active = item.getElements('.item')[index];
			
			if (active && this.parentLinks[active.id].length == 1) active = this.parentLinks[active.id].getLast().getParents('li')[0];
			
			var subContainer = new Element('div', {'class': 'fusion-js-subs'}).inject(this.jsContainer).adopt(item);

			if (active && active.hasClass('active')) {
				item.getParent().addClass('active');
			}
		}, this);
		
		this.jsContainer.getElements('.item').setProperty('tabindex', '-1');
		
		links.each(function(link, i){
			if (!this.childMenu[link.id]) {return;}
			
			this.childMenu[link.id] = this.childMenu[link.id].getParent('div');
			this.subMenus.include(this.childMenu[link.id]);

			var tmp = [];
			this.parentLinks[link.id].each(function(parent, i) {
				tmp.push(this.childMenu[parent.id]);
			}, this);
			
			this.parentSubMenus[link.id] = tmp;
			var aSubMenu = new FusionSubMenu(this.options,this, link);

		}, this);
	}
	
});

var FusionSubMenu = new Class({
	Implements: [Options],
    options: {
		onSubMenuInit_begin: (function(subMenuClass){}),
		onSubMenuInit_complete: (function(subMenuClass){}),
		
		onMatchWidth_begin: (function(subMenuClass){}),
		onMatchWidth_complete: (function(subMenuClass){}),
		
		onHideSubMenu_begin: (function(subMenuClass){}),
		onHideSubMenu_complete: (function(subMenuClass){}),
		
		onHideOtherSubMenus_begin: (function(subMenuClass){}),
		onHideOtherSubMenus_complete: (function(subMenuClass){}),		
		
		onHideAllSubMenus_begin: (function(subMenuClass){}),
		onHideAllSubMenus_complete: (function(subMenuClass){}),
		
		onPositionSubMenu_begin: (function(subMenuClass){}),
		onPositionSubMenu_complete: (function(subMenuClass){}),
		
		onShowSubMenu_begin: (function(subMenuClass){}),
		onShowSubMenu_complete: (function(subMenuClass){})
	},
	root:null,
	btn:null,
	hidden:true,
	myEffect:null,
		
	initialize: function(options,root,btn){
		this.setOptions(options);		
		this.root = root;
		this.btn = document.id(btn);
		this.childMenu = document.id(root.childMenu[btn.id]);
		this.subMenuType = root.menuType[btn.id];
		this.parentSubMenus =  $$(root.parentSubMenus[btn.id]);
		this.parentLinks =  $$(root.parentLinks[btn.id]);
		this.parentSubMenu = document.id(this.parentSubMenus[0]);
		this.otherSubMenus = {};
		this.fxMorph = {};
		this.rtl = root.rtl;
		
		this.options.tweakInitial = this.root.options.tweakInitial;
		this.options.tweakSubsequent = this.root.options.tweakSubsequent;
		this.options.centered = this.root.options.centered;

		this.childMenu.fusionStatus = 'closed';
		
		this.options.onSubMenuInit_begin(this);		

		
		this.childMenu.addEvent('hide', this.hideSubMenu.bind(this));
		this.childMenu.addEvent('show', this.showSubMenu.bind(this));
		
		var child = this.childMenu;
		if (this.options.effect) {
			this.myEffect = new Fx.Morph(this.childMenu.getFirst(), {	
				duration: this.options.menuFx.duration, 
				transition: this.options.menuFx.transition,  
				link: 'cancel',
				onStart: function() {
					this.element.setStyle('display', 'block');
				},
				onComplete: function() {
					
					if (child.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							child.setStyle('display', 'none');
						} else {
							this.element.setStyle('display', 'none');
						}
					}
				}
			});
		}
		
		if (this.options.effect == 'slide' || this.options.effect == 'slide and fade') {
			if (this.subMenuType == 'init' && this.options.orientation == 'horizontal') this.myEffect.set({'margin-top': '0'});
			else {
				if (!this.rtl) this.myEffect.set({'margin-left': '0'});
				else this.myEffect.set({'margin-right': '0'});
			}
		}
		else if (this.options.effect == 'fade' || this.options.effect == 'slide and fade') this.myEffect.set({'opacity': 0});
		
		if (this.options.effect != 'fade' && this.options.effect != 'slide and fade') this.myEffect.set({'opacity': this.options.opacity});

		
		//attach event handlers to non-parent sub menu buttons
		var nonParentBtns = document.id(this.childMenu).getElements('.item').filter(function(item, index){ return !root.childMenu[item.id]; });
		
		nonParentBtns.each(function(item, index){
			document.id(item).getParent().addClass('f-submenu-item');
			
			var prnt = item.getParent();
			var listParents = item.getParents('li').length;
			
			if (listParents < 2 && !prnt.hasClass('fusion-grouped')) {
				prnt.addEvents({
					'mouseenter': function(e){
						this.childMenu.fireEvent('show');
						this.cancellHideAllSubMenus();					
						this.hideOtherSubMenus();				
					}.bind(this),
				
					'focus': function(e){
						this.childMenu.fireEvent('show');
						this.cancellHideAllSubMenus();		
						this.hideOtherSubMenus();
					}.bind(this),
				
					'mouseleave': function(e){
						this.cancellHideAllSubMenus();
						this.hideAllSubMenus();					
					}.bind(this),
				
					'blur': function(e){
						this.cancellHideAllSubMenus();
						this.hideAllSubMenus();
					}.bind(this)
				});
			} else {
				prnt.addEvents({
					'mouseenter': function(e){
						this.childMenu.fireEvent('show');
						this.cancellHideAllSubMenus();				
						if (!prnt.hasClass('fusion-grouped')) this.hideOtherSubMenus();				
					}.bind(this),
					'mouseleave': function(e){
						//this.childMenu.fireEvent('hide');
						//this.cancellHideAllSubMenus();
						//this.hideAllSubMenus();
					}.bind(this)
				});
			}
		}, this);
		
		this.btn.removeClass('fusion-submenu-item');
		
		if (this.subMenuType == 'init') this.btn.getParent().addClass('f-main-parent');	
		else this.btn.getParent().addClass('f-parent-item');	
		
		//attach event handlers to parent button
		this.btn.getParent().addEvents({
			'mouseenter' : function(e){
				this.cancellHideAllSubMenus();
				this.hideOtherSubMenus();
				this.showSubMenu();
				if (this.subMenuType == 'init' && this.options.mmbClassName && this.options.mmbFocusedClassName) {
					if (!this.fxMorph[this.btn.id]) this.fxMorph[this.btn.id] = {};
					if (!this.fxMorph[this.btn.id]['btnMorph']) 
						this.fxMorph[this.btn.id]['btnMorph'] = new Fx.Morph(this.btn, { 
							'duration': this.options.menuFx.duration, 
							transition: this.options.menuFx.transition, 
							link: 'cancel'
						});
						
						
					this.fxMorph[this.btn.id]['btnMorph'].start(this.options.mmbFocusedClassName);
				}
			}.bind(this),
			
			'focus' : function(e) {
				this.cancellHideAllSubMenus();
				this.hideOtherSubMenus();
				this.showSubMenu();
				if (this.subMenuType == 'init' && this.options.mmbClassName && this.options.mmbFocusedClassName) {
					if (!this.fxMorph[this.btn.id]) this.fxMorph[this.btn.id] = {};
					if (!this.fxMorph[this.btn.id]['btnMorph']) 
						this.fxMorph[this.btn.id]['btnMorph'] = new Fx.Morph(this.btn, { 
							'duration': this.options.menuFx.duration, 
							transition: this.options.menuFx.transition,
							link: 'cancel'
						});
						
					this.fxMorph[this.btn.id]['btnMorph'].start(this.options.mmbFocusedClassName);
				}
			}.bind(this),
				
			'mouseleave': function(e) {
				this.cancellHideAllSubMenus();
				this.hideAllSubMenus(this.btn, this.btn.getParent().getParent().get('tag') == 'ol');
			}.bind(this),
			
			'blur': function(e) {
				this.cancellHideAllSubMenus();
				this.hideAllSubMenus();
			}.bind(this)
		});
		
		this.options.onSubMenuInit_complete(this);
		
    },
	
	matchWidth:function(){
		if (this.widthMatched || this.subMenuType === 'subseq') { return; }

		this.options.onMatchWidth_begin(this);

		var parentWidth = this.btn.getCoordinates().width;

		this.childMenu.getElements('.item').each(function(item,index) {
			var borderWidth = parseFloat(this.childMenu.getFirst().getStyle('border-left-width')) + parseFloat(this.childMenu.getFirst().getStyle('border-right-width'));
			var paddingWidth = parseFloat(item.getStyle('padding-left')) + parseFloat(item.getStyle('padding-right'));

			var offset = borderWidth + paddingWidth;

			if(parentWidth > item.getCoordinates().width){
				item.setStyle('width',parentWidth - offset);
				item.setStyle('margin-right',-borderWidth);
			}

		}.bind(this));

		this.width = this.btn.fusionSize.width;
		this.widthMatched = true;
		this.options.onMatchWidth_complete(this);

	},
	
	hideSubMenu: function() {
		if(this.childMenu.fusionStatus === 'closed'){return;}	
		this.options.onHideSubMenu_begin(this);
		if (this.subMenuType == 'init') {
			if (this.options.mmbClassName && this.options.mmbFocusedClassName) {
				if (!this.fxMorph[this.btn.id]) this.fxMorph[this.btn.id] = {};
				
				if (!this.fxMorph[this.btn.id]['btnMorph'])
					this.fxMorph[this.btn.id]['btnMorph'] = new Fx.Morph(this.btn, {
						'duration': this.options.menuFx.duration, 
						transition: this.options.menuFx.transition, 
						link: 'cancel'
					});
				
				this.fxMorph[this.btn.id]['btnMorph'].start(this.options.mmbClassName).chain(function() {
					this.btn.getParent().removeClass('f-mainparent-itemfocus');
					this.btn.getParent().addClass('f-mainparent-item');
				}.bind(this));

			} else {
				this.btn.getParent().removeClass('f-mainparent-itemfocus');
				this.btn.getParent().addClass('f-mainparent-item');
			}
		} else {
			this.btn.getParent().removeClass('f-menuparent-itemfocus');
			this.btn.getParent().addClass('f-menuparent-item');
		}
		
		this.childMenu.setStyle('z-index',1);
		if (this.options.effect && this.options.effect.toLowerCase() === 'slide') {
			
			if (this.subMenuType == 'init' && this.options.orientation == 'horizontal' && this.options.direction.y == 'down') {
				
				this.myEffect.start({'margin-top': -this.height}).chain(function() { 
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}
				}.bind(this));
				
			} else if (this.subMenuType == 'init' && this.options.orientation == 'horizontal' && this.options.direction.y == 'up') {
				this.myEffect.start({'margin-top': this.height}).chain(function() { 
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			} else if (this.options.direction.x == 'right') {
				if (!this.rtl) tmp = {'margin-left': -this.width};
				else tmp = {'margin-right': this.width};
				
				this.myEffect.start(tmp).chain(function(){
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			} else if (this.options.direction.x == 'left') {
				if (!this.rtl) tmp = {'margin-left': this.width};
				else tmp = {'margin-right': -this.width};
				
				this.myEffect.start(tmp).chain(function(){
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			}
		} else if (this.options.effect == 'fade') {
			
			this.myEffect.start({'opacity': 0}).chain(function() {
				if (this.childMenu.fusionStatus == 'closed') {
					if (!Browser.Engine.trident) {
						this.myEffect.set({'display': 'none'});
					} else {
						this.myEffect.element.setStyle('display', 'none');
					}
				}

			}.bind(this));
			
		} else if (this.options.effect == 'slide and fade') {
			if (this.subMenuType == 'init' && this.options.orientation == 'horizontal' && this.options.direction.y == 'down') {

				this.myEffect.start({'margin-top': -this.height,opacity: 0}).chain(function(){
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			} else if (this.subMenuType == 'init' && this.options.orientation == 'horizontal' && this.options.direction.y == 'up') {
				
				this.myEffect.start({'margin-top': this.height,opacity: 0}).chain(function() {
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			} else if (this.options.direction.x == 'right') {
				if (!this.rtl) tmp = {'margin-left': -this.width, 'opacity': 0};
				else tmp = {'margin-right': this.width, 'opacity': 0};
				
				this.myEffect.start(tmp).chain(function() { 
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			} else if (this.options.direction.x == 'left') {
				if (!this.rtl) tmp = {'margin-left': this.width, 'opacity': 0};
				else tmp = {'margin-right': -this.width, 'opacity': 0};
				
				this.myEffect.start(tmp).chain(function() {
					if (this.childMenu.fusionStatus == 'closed') {
						if (!Browser.Engine.trident) {
							this.myEffect.set({'display': 'none'});
						} else {
							this.myEffect.element.setStyle('display', 'none');
						}
					}

				}.bind(this));
				
			}
		} else {
			if (!Browser.Engine.trident) {
				this.myEffect.set({'display': 'none'});
			} else {
				this.myEffect.element.setStyle('display', 'none');
			}
		}
		
		this.childMenu.fusionStatus = 'closed';
		this.options.onHideSubMenu_complete(this);
	},
	
	hideOtherSubMenus: function() {		
		this.options.onHideOtherSubMenus_begin(this);
		if(!this.otherSubMenus[this.btn.id]){
			this.otherSubMenus[this.btn.id] = $$(this.root.subMenus.filter(function(item){ return !this.root.parentSubMenus[this.btn.id].contains(item) && item != this.childMenu; }.bind(this)) );
		}
		this.parentSubMenus.fireEvent('show');
		this.otherSubMenus[this.btn.id].fireEvent('hide');
		this.options.onHideOtherSubMenus_complete(this);
	},
	
	hideAllSubMenus: function(btn, group){
		this.options.onHideAllSubMenus_begin(this);
		$clear(this.root.hideAllMenusTimeout);
		this.root.hideAllMenusTimeout = (function(){
			if (!window.RTFUSION) {
				$clear(this.hideAllMenusTimeout);
				this.myEffect.cancel();
				if (this.root.options.pill && !this.root.ghostRequest) this.root.pillFx.start(this.root.pillsDefaults);
				if (group) {
					var itms = $$(this.root.subMenus).filter(function(wrap) {
						return !wrap.hasChild(btn);
					});
					$$(itms).fireEvent('hide');
				}
				else $$(this.root.subMenus).fireEvent('hide');			
			}
		}).bind(this).delay(this.options.hideDelay);
		this.options.onHideAllSubMenus_complete(this);		
	},

	cancellHideAllSubMenus: function(){ 
		clearTimeout(this.root.hideAllMenusTimeout);	
		//$clear(this.tmpTimer);
	},
	
	showSubMenu: function(now){
		if (this.root.options.pill && this.subMenuType == 'init') {
			this.root.ghostRequest = false;
			this.root.pillFx.start({
				'left': this.btn.getParent().offsetLeft,
				'width': this.btn.getParent().offsetWidth - this.root.pillsMargins,
				'top': this.btn.getParent().offsetTop
			});
		};

		if(this.childMenu.fusionStatus === 'open') { return; }
		
		this.options.onShowSubMenu_begin(this);
		
		if (this.subMenuType == 'init') {
			this.btn.getParent().removeClass('f-mainparent-item');
			this.btn.getParent().addClass('f-mainparent-itemfocus');
		} else {
			this.btn.getParent().removeClass('f-menuparent-item');
			this.btn.getParent().addClass('f-menuparent-itemfocus');
		}

		this.root.subMenuZindex++;

		this.childMenu.setStyles({'display':'block','visibility':'hidden','z-index': this.root.subMenuZindex });
		
		if(!this.width || !this.height ){
			this.width = this.btn.fusionSize.width;
			this.height = this.btn.fusionSize.height;
			this.childMenu.getFirst().setStyle('height',this.height,'border');
			if(this.options.effect == 'slide' || this.options.effect == 'slide and fade'){
				if (this.subMenuType == 'init' && this.options.orientation == 'horizontal' ) {
					this.childMenu.getFirst().setStyle('margin-top','0' );
					if (this.options.direction.y == 'down') {
						this.myEffect.set({'margin-top': - this.height});
					} else if (this.options.direction.y == 'up') {
						this.myEffect.set({'margin-top': this.height});
					}
				} else {
					if (this.options.direction.x == 'left') {
						if (!this.rtl) tmp = {'margin-left': this.width};
						else tmp = {'margin-right': -this.width};
						
						this.myEffect.set(tmp);
					} else {
						if (!this.rtl) tmp = {'margin-left': -this.width};
						else tmp = {'margin-right': this.width};
						
						this.myEffect.set(tmp);
					}
				}
			}
		}
		
		this.matchWidth();
		this.positionSubMenu();
		
		this.fixedHeader = document.body.hasClass('fixedheader-1');
		
		if (this.fixedHeader && !this.scrollingEvent){
			this.scrollingEvent = true;
			window.addEvent('scroll', function(){
				this.positionSubMenu();
			}.bind(this));
			this.positionSubMenu();
		}
		
		if (this.options.effect == 'slide' ) {
			
			this.childMenu.setStyles({'display': 'block', 'visibility': 'visible'});
			
			if (this.subMenuType === 'init' && this.options.orientation === 'horizontal') {
				if (now) this.myEffect.set({'margin-top': 0}).chain(function() { this.showSubMenuComplete(); }.bind(this));
				else this.myEffect.start({'margin-top': 0}).chain(function() { this.showSubMenuComplete(); }.bind(this));
			} else {
				if (!this.rtl) tmp = {'margin-left': 0};
				else tmp = {'margin-right': 0};
				
				if (now) this.myEffect.set(tmp).chain(function() { this.showSubMenuComplete(); }.bind(this));
				else this.myEffect.start(tmp).chain(function(){	this.showSubMenuComplete();	}.bind(this));
			}
			
		} else if (this.options.effect == 'fade') {
			
			if (now) this.myEffect.set({'opacity': this.options.opacity}).chain(function() { this.showSubMenuComplete(); }.bind(this));
			else this.myEffect.start({'opacity': this.options.opacity}).chain(function() { this.showSubMenuComplete(); }.bind(this));
			
		} else if (this.options.effect == 'slide and fade') {
			
			this.childMenu.setStyles({'display': 'block', 'visibility': 'visible'});
			this.childMenu.getFirst().setStyles({'left': 0});
			if (this.subMenuType == 'init' && this.options.orientation == 'horizontal') {
				if (now) this.myEffect.set({'margin-top': 0, 'opacity': this.options.opacity}).chain(function() { this.showSubMenuComplete(); }.bind(this));
				else this.myEffect.start({'margin-top': 0, 'opacity': this.options.opacity}).chain(function() { this.showSubMenuComplete(); }.bind(this));
				
			} else {
				if (!this.rtl) tmp = {'margin-left': 0, 'opacity': this.options.opacity};
				else tmp = {'margin-right': 0, 'opacity': this.options.opacity};
				
				if (now) {
					if (this.options.direction.x == 'right') {
						this.myEffect.set(tmp).chain(function() { this.showSubMenuComplete(); }.bind(this));
					} else if (this.options.direction.x == 'left') {
						this.myEffect.set(tmp).chain(function() { this.showSubMenuComplete(); }.bind(this));
					}
					
				} else {
					
					if (this.options.direction.x == 'right') {
						
						this.myEffect.set({'margin-left': -this.width, 'opacity': this.options.opacity});						
						this.myEffect.start(tmp).chain(function() { this.showSubMenuComplete(); }.bind(this));
						
					} else if (this.options.direction.x == 'left') {
						this.myEffect.set({'margin-left': this.width, 'opacity': this.options.opacity});
						this.myEffect.start(tmp).chain(function(){ this.showSubMenuComplete(); }.bind(this));
					}
				}
			}
			
		} else {
			
			this.childMenu.setStyles({'display': 'block', 'visibility': 'visible'});
			this.showSubMenuComplete(this);
			
		}
		
		this.childMenu.fusionStatus = 'open';
		
	},
	
	showSubMenuComplete:function(){
		
		this.options.onShowSubMenu_complete(this);
		
	},
	
	positionSubMenu: function(){
		
		this.options.onPositionSubMenu_begin(this);
		
		var height = this.childMenu.getStyle('padding-bottom').toInt() + this.options.tweakSizes.height;
		var width = this.options.tweakSizes.width;
		if (!Browser.Engine.presto || !Browser.Engine.gecko || !Browser.Engine.webkit) {
			width = 0;
			height = 0;
		}
		if (!this.rtl) {
			this.childMenu.setStyles({
				'width': this.width + this.options.tweakSizes.width, 
				'padding-bottom': this.options.tweakSizes.height,
				'padding-top': this.options.tweakSizes.height / 2,
				'padding-left': this.options.tweakSizes.width / 2
			});
		} else {
			this.childMenu.setStyles({
				'width': this.width + this.options.tweakSizes.width, 
				'padding-bottom': this.options.tweakSizes.height,
				'padding-top': this.options.tweakSizes.height / 2,
				'padding-right': this.options.tweakSizes.width / 2
			});
		}
		this.childMenu.getFirst().setStyle('width', this.width);
				
		if (this.subMenuType == 'subseq') {
			this.options.direction.x = 'right';
			this.options.direction.xInverse = 'left';
			this.options.direction.y = 'down';
			this.options.direction.yInverse = 'up';
			
			if (this.rtl) {
				this.options.direction.x = 'left';
				this.options.direction.xInverse = 'right';
			}
		}

		var top;
		var overlap;
		if (this.subMenuType == 'init') {
			if (this.options.direction.y == 'up') {

				if (this.options.orientation == 'vertical') top = this.btn.getCoordinates().bottom - this.height + this.options.tweakInitial.y;
				else top = this.btn.getCoordinates().top - this.height + this.options.tweakInitial.y;

				this.childMenu.style.top = top + 'px';
				
			} else if (this.options.orientation == 'horizontal') this.childMenu.style.top = this.btn.getCoordinates().bottom + this.options.tweakInitial.y + 'px';
			
			else if (this.options.orientation == 'vertical') {
			
				top = this.btn.getPosition().y + this.options.tweakInitial.y;
				
				if ((top + this.childMenu.getSize2().y) >= document.body.getScrollSize2().y) {
					overlap = (top + this.childMenu.getSize2().y) - document.body.getScrollSize2().y;
					top = top - overlap - 20;
				}
				
				this.childMenu.style.top = top + 'px';
			}
			
			if (this.options.orientation == 'horizontal') {
				var position = this.btn.getPosition().x + this.options.tweakInitial.x, compensation = 0;

				if (this.rtl) {
					var x = 0;
					if (this.btn.getStyle('margin-left').toInt() < 0 && !this.options.centered) x = this.btn.getParent().getPosition().x + this.options.tweakInitial.x;
					else if (this.btn.getStyle('margin-left').toInt() < 0 && this.options.centered) x = this.btn.getPosition().x - this.options.tweakInitial.x;
					else x = this.btn.getPosition().x;

					position = x + this.btn.getSize2().x - this.childMenu.getSize2().x;
				}				

				if (this.options.centered) {
					compensation = 0;
					var itemSize = this.btn.getSize2().x;
					if (this.btn.getStyle('margin-left').toInt() < 0 && !this.rtl) compensation = Math.abs(this.btn.getStyle('margin-left').toInt()) - Math.abs(this.btn.getFirst().getStyle('padding-left').toInt());
					else compensation = Math.abs(this.btn.getStyle('margin-right').toInt()) - Math.abs(this.btn.getFirst().getStyle('padding-right').toInt());
					var childSize = this.childMenu.getSize2().x;
					itemSize += compensation;
					var max = Math.max(itemSize, childSize), min = Math.min(itemSize, childSize);

					size = (max-min) / 2;
					if (!this.rtl) position -= size;
					else position += size;
				}
				
				this.childMenu.style.left = position + 'px';
				
			} else if (this.options.direction.x == 'left') {
				this.childMenu.style.left = this.btn.getPosition().x - this.childMenu.getCoordinates().width + this.options.tweakInitial.x + 'px';
				
			} else if (this.options.direction.x == 'right') {
				this.childMenu.style.left = this.btn.getCoordinates().right + this.options.tweakInitial.x + 'px';
				
			}
			
		} else if (this.subMenuType == 'subseq') {
			
			if (this.options.direction.y === 'down') {
				if ((this.btn.getCoordinates().top + this.options.tweakSubsequent.y + this.childMenu.getSize2().y) >= document.body.getScrollSize2().y) {
					
					overlap =  (this.btn.getCoordinates().top + this.options.tweakSubsequent.y + this.childMenu.getSize2().y) - document.body.getScrollSize2().y;
					this.childMenu.style.top = (this.btn.getCoordinates().top + this.options.tweakSubsequent.y) - overlap - 20 + 'px';
					
				} else {
					
					this.childMenu.style.top = this.btn.getCoordinates().top + this.options.tweakSubsequent.y + 'px';
					
				}
				
			} else if (this.options.direction.y === 'up') {
				
				if ((this.btn.getCoordinates().bottom - this.height + this.options.tweakSubsequent.y) < 1) {
					
					this.options.direction.y = 'down';
					this.options.direction.yInverse = 'up';
					this.childMenu.style.top = this.btn.getCoordinates().top + this.options.tweakSubsequent.y + 'px';
					
				} else {
					
					this.childMenu.style.top = this.btn.getCoordinates().bottom - this.height + this.options.tweakSubsequent.y + 'px';
					
				}
			}

			if (this.options.direction.x == 'left') {
				
				this.childMenu.style.left = this.btn.getCoordinates().left - this.childMenu.getCoordinates().width + this.options.tweakSubsequent.x + 'px';

				if (this.childMenu.getPosition().x < 0) {
				
					this.options.direction.x = 'right';
					this.options.direction.xInverse = 'left';
					this.childMenu.style.left = this.btn.getPosition().x + this.btn.getCoordinates().width + this.options.tweakSubsequent.x + 'px';
					
					if(this.options.effect === 'slide' || this.options.effect === 'slide and fade'){
						if (!this.rtl) tmp = {'margin-left': -this.width, 'opacity': this.options.opacity};
						else tmp = {'margin-right': this.width, 'opacity': this.options.opacity};
						
						this.myEffect.set(tmp);
					}
				}
				
			} else if(this.options.direction.x == 'right') {
				
				this.childMenu.style.left = this.btn.getCoordinates().right + this.options.tweakSubsequent.x + 'px';
				var smRight = this.childMenu.getCoordinates().right;
				var viewportRightEdge = document.body.getSize2().x + window.getScroll2().x;
				
				if(smRight > viewportRightEdge) {
					this.options.direction.x = 'left';
					this.options.direction.xInverse = 'right';

					this.childMenu.style.left = this.btn.getCoordinates().left - this.childMenu.getCoordinates().width - this.options.tweakSubsequent.x + 'px';
					
					if (this.options.effect == 'slide' || this.options.effect == 'slide and fade') {
						if (!this.rtl) tmp = {'margin-left': this.width, 'opacity': this.options.opacity};
						else tmp = {'margin-right': -this.width, 'opacity': this.options.opacity};
						
						this.myEffect.set(tmp);
					}
				}
			}
		}
		
		this.options.onPositionSubMenu_complete(this);
	}	
});


Element.implement({
    getCustomID: function(){
        if(!this.id){ 
			var rid = this.get('tag') + "-" + $time() + $random(0, 1000);
			//while ($(rid)) {this.getTag() + "-" + $time() + $random(0, 1000);}
			this.id = rid;
		};
	    return this.id;
    }
});

Native.implement([Element], {
	getSize2: function(){
		if ((/^(?:body|html)$/i).test(this.tagName)) return this.getWindow().getSize();
		return {x: this.offsetWidth, y: this.offsetHeight};
	},

	getScrollSize2: function(){
		if ((/^(?:body|html)$/i).test(this.tagName)) return this.getWindow().getScrollSize();
		return {x: this.scrollWidth, y: this.scrollHeight};
	},

	getScroll2: function(){
		if ((/^(?:body|html)$/i).test(this.tagName)) return this.getWindow().getScroll();
		return {x: this.scrollLeft, y: this.scrollTop};
	}
});

Native.implement([Document, Window], {
	getSize2: function(){
		return this.getSize();
	},

	getScroll2: function(){
		return this.getScroll();
	},

	getScrollSize2: function(){
		return this.getScrollSize();
	}
});