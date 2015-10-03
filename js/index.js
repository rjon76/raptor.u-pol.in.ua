$(document).ready(function(){
	
	$.chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase()); 
	
	if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
	  var msViewportStyle = document.createElement("style");
	  msViewportStyle.appendChild(
		document.createTextNode(
		  "@-ms-viewport{width:auto!important}"
		)
	  );
	  document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
	}

var location = String(window.location);
  $.urlParam = function(name, url){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) { return 0; }
    return results[1] || 0;
  }; 

	var preload_pictures    = function(picture_urls){
		for(var i = 0, j = picture_urls.length; i < j; i++){
			var img     = new Image();
			img.src = picture_urls[i];
		}
	};
	

 var windowWidth = $(window).width(); //retrieve current window width
 var windowHeight = $(window).height(); //retrieve current window height
/*--------------------------------*/
if (typeof ($.colorbox) === "function" && screen.width > 480) {
	
	var cboxOptions = {
  		width: '95%',
  		height: '85%',
  		maxWidth: '1024px',
  		maxHeight: '1024px',
	}

	var colorbox_resize    = function(){
		var $element = $.colorbox.element();
		if($element == undefined || $element.hasClass('overlay'))
			return;
		
    	$.colorbox.resize({
      		width: window.innerWidth > parseInt(cboxOptions.maxWidth) ? cboxOptions.maxWidth : cboxOptions.width,
      		height: window.innerHeight > parseInt(cboxOptions.maxHeight) ? cboxOptions.maxHeight : cboxOptions.height
    	});
	};	

	$(window).resize(function(){
			colorbox_resize();
	});
	
	$(document).bind('cbox_complete', function(){
		colorbox_resize();
	});
			
	if ($.urlParam('page', location)!=0){
	//if(String(window.location).indexOf('&page=')!=-1 || String(window.location).indexOf('?page=')!=-1){
		
		$.colorbox({
					iframe:function(){return $.urlParam('iframe', location) != 0 ? $.urlParam('iframe', location) : true},
					innerHeight:function(){ return $.urlParam('height', location) ? $.urlParam('height', location) : 500 },
					innerWidth:function(){ return $.urlParam('width', location) ? $.urlParam('width', location) :600 },
					href:function(){return $.urlParam('page', location)+'&rnd='+Math.round(Math.random(0)*1000)},
					scrolling:false, title:' ',opacity:'0.50', rel:true, controls:true});
	}	



	$('.screenshot').colorbox({/*opacity:'0.50',*/
							transition:'none',
							rel:function(){return $(this).attr('rel');},
							controls:true,
							scrolling:false,
							scalePhotos:true,
							//speed:100,
							maxWidth: parseInt(cboxOptions.maxWidth),
      						maxHeight:  parseInt(cboxOptions.maxHeight),
						//	 maxWidth:function(){return window.innerWidth - 50},
							//  maxHeight:function(){return window.innerHeight - 50},
							iframe:function(){var param = $.urlParam('iframe', $(this).attr('href')); return  (param !== '') ? param : false},
							fastIframe:function(){var param = $.urlParam('iframe', $(this).attr('href')); return  (param !== '') ? false : true},
							innerWidth:function(){var param = $.urlParam('width', $(this).attr('href')); return  (param !== '') ? param : false},
							innerHeight:function(){var param = $.urlParam('height', $(this).attr('href')); return  (param !== '') ? param : false}
						}); 

	$(".overlay").colorbox({
				iframe:true,
				fastIframe:function(){var param = $.urlParam('iframe', $(this).attr('href')); return  (param !== '') ? false : true},
				//innerWidth:function(){return $.urlParam('width', $(this).attr('href'));},
				//innerHeight: function(){return $.urlParam('height', $(this).attr('href'));},
				innerWidth:function(){var param = $.urlParam('width', $(this).attr('href')); return  (param !== '') ? param : false},
				innerHeight:function(){var param = $.urlParam('height', $(this).attr('href')); return  (param !== '') ? param : false},
				scrolling:false,
				title:' ',
				rel:function(){return $(this).attr('rel');},
				controls:true,
				transition:'none'
				}); 
	
	$('.resize').bind('click', function(){ 

	 		if (parent.jQuery.colorbox !== undefined){
				parent.jQuery.colorbox({iframe:true, innerWidth:$.urlParam('width', $(this).attr('href')), innerHeight: $.urlParam('height', $(this).attr('href')), scrolling:false, title:' ',opacity:'0.50', rel:false, controls:true,
				href:$(this).attr('href')
				}); 
			return false;									 																	
				}

			});
}

/*--------------------------------*/

 /* replace retina src images if use class image2x */
    (function($) {
        
        var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
        
        //var pixelRatio = 2; //hidden for retina
        
        // verify the existence of the file
        var use_if_file_exists = false;
        
        if (pixelRatio > 1) 
        {
    		var els = jQuery("img.image2x").get();
            
            if (use_if_file_exists){
                var http = new XMLHttpRequest();
    		}
            
            for(var i = 0; i < els.length; i++) 
            {
    			var src = els[i].src;
    			src = src.replace(".png", "@2x.png").replace(".jpg", "@2x.jpg");
                
                if (use_if_file_exists)
                {
                    if (UrlExists(src))
                    {
                        els[i].src = src;
                    }
                }
                else
                {
                    els[i].src = src;
                }
    		}
        }
        
        function UrlExists(url)
        {
            http.open('HEAD', url, false);
            http.send();
            return http.status!=404;
        }

        
    })(jQuery);  	
	 
});