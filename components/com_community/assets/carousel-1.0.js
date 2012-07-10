function cCarouselInit(id, jaxcall){
	// if jQuery is not ready, wait for a while
	if(typeof jQuery == 'function') {
		cCarouselLoadData(id, jaxcall);
	} else {
		setTimeout('cCarouselInit(\'' + id + '\')', 200);
	}
}

// return number of pixel to slide
function cCarouselSlideByPx(id){
	var clipW = jQuery(id+'.carousel-content-clip').width();
	//console.log(clipW);
	var w = jQuery(id+'.carousel-content-clip li.carousel-item').outerWidth({ margin: true });
	//console.log(w);
	//console.log(parseInt(clipW/w) * w);
	return parseInt(clipW/w) * w
}

function cCarouselPrev(id, jaxcall){
	var oId = id;
	id = '#' + id +' ';	
	var w = cCarouselSlideByPx(id); //jQuery(id+'.carousel-content-clip').width();
	var left = jQuery(id+'.carousel-list').data('margin-left'); //jQuery('.carousel-list').css();
	//console.log(left); 
	if(isNaN(left)) left = 0;
	if(left >= 0)return;
	left = left+w;
	jQuery(id+'.carousel-list').data('margin-left', left); 
	jQuery(id+'.carousel-list').animate({ 
	        marginLeft: left+"px"
	      }, 600 );
	jQuery(id+' a').trigger('blur');
	cCarouselLoadData(oId, jaxcall);
}
function cCarouselNext(id, jaxcall){
	var oId = id;
	id = '#' + id +' ';	
	var w = cCarouselSlideByPx(id); 
	var left = parseInt(jQuery(id+'.carousel-list').data('margin-left'));
	var width = jQuery(id+'.carousel-content-clip li.carousel-item').outerWidth({ margin: true });
	if(isNaN(left))left = 0;
	left = left-w;
	
	//console.log(left);
	jQuery(id+'.carousel-list').data('margin-left', left); 
	jQuery(id+'.carousel-list').animate({ 
	        marginLeft: left+"px"
	      }, 600 );
	jQuery(id+' a').trigger('blur');
	cCarouselLoadData(oId, jaxcall);
}

function cCarouselLoadData(id, jaxcall){
	var oId = id;
	id = '#' + id +' ';	
	jQuery(id + '.carousel-list li');
	var  startItem =0;
	var  endItem = 0;
	
	var left = parseInt(jQuery(id+'.carousel-list').data('margin-left')); 
	if(isNaN(left))left = 0;
	
	var w = jQuery(id+'.carousel-content-clip li.carousel-item').outerWidth({ margin: true });
	var clipW = jQuery(id+'.carousel-content-clip').width();
	
	startItem = parseInt(left / w);
	if(startItem < 0) startItem = startItem*(-1);
	
	endItem = startItem + parseInt(clipW / w);
	if(endItem < 0) endItem = endItem*(-1)
	endItem++;
	
	// Should only get the ajax if the list is empty
	var refrshNow = false;
	for(var i = startItem; i < endItem; i++){
		//console.log(jQuery(id+'.carousel-content-clip li:nth-child('+ (i+1) +')').html());
		if(i+1 <= jQuery(id+'.carousel-content-clip li').length)
			if(jQuery(id+'.carousel-content-clip li:nth-child('+ (i+1) +')').html().indexOf('ajax-wait') != -1 )
				refrshNow = true;
	}
	
	//<div class="ajax-wait">&nbsp;</div>
	if(refrshNow)
		jax.call('community', jaxcall, oId, startItem, endItem);

}

