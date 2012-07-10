<?php
 /**
  * @version   $Id$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

global $gantry;

$no_captions_percent = 35;

if ($gantry->platform->jslib_version == '1.1'){

	echo "window.addEvent('domready', function(){
		new RokGallery.Slideshow('rg-".$passed_params->moduleid."', {
			onJump: function(index){
				if (!this.slideshowSpacer) this.slideshowSpacer = $('slideshow-spacer');
				this.animation.index = this.current;
				this.animation.setBackground(this.slices[index].getElement('img').getProperty('src'));
				this.animation.setAnimation(this.options.animation);
				this.animation.play();
				
				if (this.captions.length){
					if (this.current == index){
						this.captions[this.current].fx.start(1);
					} else {
						this.captions[this.current].fx.start(0);
						this.captions[index].fx.start(1);
					}
				}

				if (this.slideshowSpacer){
					var height;
					if (this.captions.length) height = this.captions[index].offsetHeight;
					else height = this.container.getStyle('height').toInt() / 100 * ".$no_captions_percent.";
					this.slideshowSpacer.effect('height', {duration: 300, wait: false}).start(height + 50);
				}
			},
			animation: '".$passed_params->animation_type."',
			duration: ".$passed_params->animation_duration.",
			autoplay: {
				enabled: ".$passed_params->autoplay_enabled.",
				delay: ".$passed_params->autoplay_delay."
			}
		});
	});";

} else {

	echo "window.addEvent('domready', function(){
		new RokGallery.Slideshow('rg-".$passed_params->moduleid."', {
			onJump: function(index){
				if (!this.slideshowSpacer) this.slideshowSpacer = document.id('slideshow-spacer');
				this.animation.index = this.current;
				this.animation.setBackground(this.slices[index].getElement('img').get('src'));
				this.animation.setAnimation(this.options.animation);
				this.animation.play();

				if (this.captions.length){
					if (this.current == index){
						this.captions[index].fade('in');
					} else {
						this.captions[this.current].fade('out');
						this.captions[index].fade('in');
					}
				}

				if (this.slideshowSpacer){
					var height;
					this.slideshowSpacer.set('tween', {duration: 300, link: 'cancel'});
					if (this.captions.length) height = this.captions[index].offsetHeight;
					else height = this.container.getStyle('height').toInt() / 100 * ".$no_captions_percent.";
					this.slideshowSpacer.tween('height', height + 50);
				}
			},
			animation: '".$passed_params->animation_type."',
			duration: ".$passed_params->animation_duration.",
			autoplay: {
				enabled: ".$passed_params->autoplay_enabled.",
				delay: ".$passed_params->autoplay_delay."
			}
		});
	});";
	
}