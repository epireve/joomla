joms.extend({
	minitip: {
		id: {
			canvas: 'cMinitip',
			contentWrap: 'cMinitipContentWrap',
			content: 'cMinitipContent',
			contentOuter: 'cMinitipContentOuter'
		},
		className: {
			canvas: 'cMinitipClass',
			canvas_add: 'dialog'
		},
		_init: function(minitipCall, winTitle, contentWidth, contentHeight, winType) {
			joms.jQuery('#'+joms.minitip.id.canvas).remove();

			/* Original HTML at bottom. Edit, encodeURIComponent and put it back here. */
			var cMinitipHTML = decodeURIComponent('%3Cdiv%20id%3D%22'+joms.minitip.id.canvas+'%22%20class%3D%22%7B'+joms.minitip.className.canvas+'%7D%22%3E%0A%09%3Cdiv%20id%3D%22'+joms.minitip.id.contentOuter+'%22%3E%0A%0A%09%09%0A%0A%09%09%3Cdiv%20id%3D%22'+joms.minitip.id.contentWrap+'%22%3E%0A%09%09%09%3Cdiv%20id%3D%22'+joms.minitip.id.content+'%22%3E%3C%2Fdiv%3E%0A%09%09%3C%2Fdiv%3E%0A%0A%09%3C%2Fdiv%3E%0A%09%3Cdiv%20style%3D%22clear%3A%20both%3B%22%3E%3C%2Fdiv%3E%0A%3C%2Fdiv%3E');

			// add additional class to cMinitip
			cMinitipHTML = 	cMinitipHTML.replace('{'+joms.minitip.className.canvas+'}', joms.minitip.className.canvas_add);

			var cMinitip = joms.jQuery(cMinitipHTML);

			var cMinitipSize = {
				contentWrapHeight  : function() { return contentHeight },
				contentOuterWidth  : function() { return contentWidth },
				width              : function() { return this.contentOuterWidth() },
				height             : function() { return this.contentWrapHeight() },
				left               : function() { return (joms.jQuery(window).width() - this.width()) / 2 },
				top                : function() { return joms.jQuery(document).scrollTop() + ((joms.jQuery(window).height() - this.height()) / 2) },
				zIndex             : function() { return joms.minitip.getMaxZIndex() + 1 }
			};

			cMinitip
				.attr(
				{
					'class' : winType
				})
				.css(
				{
					'width' : cMinitipSize.width(),
					'height': cMinitipSize.height(),
					'top'   : cMinitipSize.top(),
					'left'  : cMinitipSize.left(),
					'zIndex': cMinitipSize.zIndex()
				})
				.prependTo('body');
				
			joms.jQuery('#'+joms.minitip.id.contentOuter+', #'+joms.minitip.id.contentWrap).css({
					'width' : cMinitipSize.width(),
					'height': cMinitipSize.height()
				});

			// Set up behaviour
			jax.loadingFunction = function() {
				joms.jQuery('#'+joms.minitip.id.contentWrap).addClass('loading')
												  .css('overflow', 'fix');
			};
			jax.doneLoadingFunction = function() {
				joms.jQuery('#'+joms.minitip.id.contentWrap).removeClass('loading')
												  .css('overflow', 'fix');
			};

			if (minitipCall!=undefined && typeof(minitipCall)=="string") eval(minitipCall);
			if (typeof(minitipCall)=="function") minitipCall();

			// Hide iframe as it appear on top of cMinitip
			joms.jQuery('#community-wrap iframe').css('visibility', 'hidden');
		},
		hide: function() {
			var cMinitip = joms.jQuery('#'+joms.minitip.id.canvas);

			cMinitip.remove();
		},
		addContent: function(html) {
			var cMinitip = joms.jQuery('#'+joms.minitip.id.canvas);
			
			cMinitip.find('#'+joms.minitip.id.content).html(html);
		},
		getMaxZIndex: function() {
			var allElems = document.getElementsByTagName?
			document.getElementsByTagName("*"):
			document.all; // or test for that too
			var maxZIndex = 0;

			for(var i=0;i<allElems.length;i++) {
				var elem = allElems[i];
				var cStyle = null;
				if (elem.currentStyle) {cStyle = elem.currentStyle;}
				else if (document.defaultView && document.defaultView.getComputedStyle) {
					cStyle = document.defaultView.getComputedStyle(elem,"");
				}

				var sNum;
				if (cStyle) {
					sNum = Number(cStyle.zIndex);
				} else {
					sNum = Number(elem.style.zIndex);
				}
				if (!isNaN(sNum)) {
					maxZIndex = Math.max(maxZIndex,sNum);
				}
			}	
			return maxZIndex;
		}
	}
});