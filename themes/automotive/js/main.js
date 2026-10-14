jQuery(document).ready( function($){
	$.fn.evenElements = function() {	
		var heights  = [];
		
		$(this).removeAttr("style").height('auto');
		
		this.each( function() {
			if($(this).css('transition-duration')){
				transition_duration = (typeof transition_duration == "undefined" ? $(this).css('transition-duration') : transition_duration);
				$(this).css('transition-duration', '0s');
			}
			
			var height = $(this).height('auto').outerHeight();
			
			heights.push(height);
		});	
		
		var largest = Math.max.apply(Math, heights);
	
		return this.each(function() {
            $(this).height(largest);
				
			$(this).css('transition-duration', transition_duration);
        });
	}
	
	jQuery.fn.extend({
	  	renameAttr: function( name, newName, removeData ) {
			var val;
			return this.each(function() {
			  	val = jQuery.attr( this, name );
		  		jQuery.attr( this, newName, val );
		  		jQuery.removeAttr( this, name );
		  		// remove original data
		  		if (removeData !== false){
					jQuery.removeData( this, name.replace('data-','') );
		  		}
			});
	  	}
	});
	
	function debouncer( func , timeout ) {
	   var timeoutID , timeout = timeout || 200;
	   return function () {
	      var scope = this , args = arguments;
	      clearTimeout( timeoutID );
	      timeoutID = setTimeout( function () {
	          func.apply( scope , Array.prototype.slice.call( args ) );
	      } , timeout );
	   }
	}
			
	function commaSeparateNumber(val){
		while (/(\d+)(\d{3})/.test(val.toString())){
		  val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		}
		return val;
	}
	
	if($("div.inventory").length){
		//$("div.inventory").evenElements();
	}
	
	// if wow exists
	if(typeof WOW == 'function'){
		WOW = new WOW({
				boxClass: 'scroll_effect',
				offset: 15
			});
		
		WOW.init();
	}
	
	// animate progress bars
	$(".progress-bar[data-width]").each( function(){
		$(this).css("width", $(this).data('width') + "%");
	});
	
	$(".portfolioFilter li a").click( function(e){
		e.preventDefault();
		$(".portfolioFilter li.active").removeClass("active");
		$(this).parent().addClass('active');
	});
	
	$(document).on({
		mouseenter: function(){
			var elm = $('ul:first', this);
			var off = elm.offset();
			
			if(typeof off != "undefined"){	
				var l = off.left;
				var w = elm.width();
				var docW = $("body").outerWidth(true);
			
				var isEntirelyVisible = (l+ w <= docW);
			
				if ( ! isEntirelyVisible ) {
					$(this).addClass('other_side');
				}
			}
		},
		
		mouseleave: function(){
			if($(this).hasClass('other_side')){
				$(this).removeClass('other_side');	
			}
		}
	}, ".dropdown li");
	
	// testimonial slider
	if($(".testimonial_slider").length){
		$('.testimonial_slider').bxSlider({
			mode: 'horizontal',
			slideMargin: 3,
			minSlides: 1,
			maxSlides: 1,
			auto: true,
			autoHover: true,
			speed: 500,
			pager: false,
			controls: false
		});
	}
	
	// featured service hover
	if($(".featured-service .featured").length){
		$('.featured-service .featured').hover( function() {
			var image = $(this).find('img');
			
			image.data('img', image.attr('src'));
			image.attr('src', image.data('hoverimg'));
		}, function(){
			var image = $(this).find('img');
			
			image.attr('src', image.data('img'));
		});
	}
	
	// faq sort
	if($(".faq-sort").length){
		// faq shortcode
		if(window.location.hash) {
			var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
			
			$(".list_faq a").each( function(index, element){
				if($(this).text().indexOf(hash) !== -1){
					$(this).parent().addClass("active");
				}
			});
			
			if(hash != "All"){
				$(".faq-sort div.panel").each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(hash) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		} else {
			$(".list_faq li").first().addClass("active");
		}
		
		$("a[data-action='sort']").click( function(){
			var category = $(this).attr("href").replace("#", "");
			var faqs     = $(".faq-sort div.panel");
												
			$(".list_faq li.active").removeClass("active");
			
			$(this).parent().addClass("active");
			
			if(category == "All"){
				faqs.each(function(index, element) {
					$(this).show({effect: "fold", duration: 600});
				});
			} else {
				faqs.each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(category) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		});
	}
	
	// portfolio sorting
	if($(".portfolioContainer").length){
		$(".portfolioContainer").mixItUp({
			callbacks: {
				onMixLoad: function(state){
					//$(".portfolioContainer .mix").each( function(i, n){ $(this).css("float", "none"); });
				}/*,
				onMixStart: function(state){
					$(".portfolioContainer .mix").each( function(i, n){ $(this).height($(".portfolioContainer .mix").height()); });
				}*/
			}
		});
	}
	
	// social likes
	if($('.social-likes').length){
		$('.social-likes').socialLikes({
			zeroes: 'yes'
		});
	}
	
	// select box
	if($(".css-dropdowns").length){
		$(".css-dropdowns").selectbox();
	}
	
	// fancy box - EXCLUDE gallery1 which has its own advanced config with zoom
	if($(".fancybox").length){
		$(".fancybox").not('[data-fancybox="gallery1"]').fancybox();
	}
	
	// parallax effect
	if($(".parallax_scroll").length){
		$(".parallax_scroll").parallax({ speed: 0.15 });
		
		/*$(".parallax_scroll").each( function(index, element){
			$(this).scrolly({ bgParallax: true });
		});*/
		
		$(".parallax_parent").each( function(){
			$(this).height($(this).find(".parallax_scroll").height());
		});
	}


	$( window ).resize( debouncer( function ( e ) {
	    $(".parallax_parent").each( function(){
	    	$(this).height($(this).find(".parallax_scroll").height());
	    });
	} ) );
	
	function animate_number(el, value){		
		var original = value;
		    value    = parseInt(value);
	
		$({ someValue: 0 }).animate({ someValue: value }, {
			duration: 3000,
			easing: 'easeOutExpo', 
			step: function() { 
				el.text(commaSeparateNumber(Math.round(this.someValue)));
			},
			complete: function(){
				// ensure correct number appears after animate, wierd bugs in cause larger numbers to lose a few digits //
				el.text(commaSeparateNumber(Math.round(original)));
			}
		});
	}
	
	// animate numbers
	if($(".animate_number").length){
		$(".animate_number").each( function(){					
			var el 	  = $(this).find(".number");
			
			el.data('value', el.text());
			el.text(0);
			
			$(this).one('inview', function(event, isInView, visiblePartX, visiblePartY){
				var value = el.data('value').replace(/[^0-9]/gi, '');					
				
				if(isInView){
					setTimeout( function(){
						animate_number(el, value);
					}, 500);
				}
				
			});
		});
	}
	
	// fullwidth functionality
	$(".fullwidth_element").each( function(index, element){
		var height = $(this).height();

		$(this).closest(".fullwidth_element_parent").height(height);
	});
	
	// dropdown menu
	if($(".dropdown .dropdown").length){
		$('.dropdown .dropdown').each(function(){
			var $self = $(this);
			var handle = $self.children('[data-toggle="dropdown"]');
			$(handle).click(function(){
				var submenu = $self.children('.dropdown-menu');
				$(submenu).toggle();
				return false;
			});
		});
	}
	
	// grid switch
	if($('#grid-switch-control li a').length){
		$('#grid-switch-control li a').click(function(e) {
			e.preventDefault();
			var _sidebar = $(this).attr('data-sidebar');
			var _boxview = $(this).attr('data-boxview');
			$('#grid-switch').removeClass('no-sidebar').removeClass('left-sidebar').removeClass('right-sidebar').removeClass('list-view').removeClass('grid-view');
			$('#grid-switch').addClass(_sidebar).addClass(_boxview);
		});
	}
	
	// sliders
	if($('.carasouel-slider').length){
		$('.carasouel-slider').bxSlider({
			slideWidth: 155,
			minSlides: 1,
			maxSlides: 6,
			slideMargin: 30,
			infiniteLoop:false,
			pager:false,
			nextSelector:jQuery('#slideControls>.next-btn'),
			prevSelector:jQuery('#slideControls>.prev-btn')
		});
	}
	
	if($('.carasouel-slider3').length){
		$('.carasouel-slider3').bxSlider({
			slideWidth: 170,
			minSlides: 1,
			maxSlides: 6,
			slideMargin: 30,
			infiniteLoop:false,
			pager:false,
			prevSelector:jQuery('#slideControls3>.prev-btn'),
			nextSelector:jQuery('#slideControls3>.next-btn'),
		});
	}
	
	if($('.flexslider2').length){
		$('.flexslider2').flexslider({
			animation: "slide",
			directionNav:true,
			controlNav: false,
			prevText:"",
			nextText:""		
		});
	}
	
	// inventory listing slider
	// Preload lazy images when thumbnail is clicked or becomes visible (FIX for mobile blank slides)
	function preloadSlideImage(slideIndex) {
		var $slide = $('#home-slider-canvas .slides > li').eq(slideIndex);
		var $img = $slide.find('img[loading="lazy"]');
		
		if ($img.length > 0 && $img.attr('loading') === 'lazy') {
			// Force image to load by removing lazy attribute
			$img.attr('loading', 'eager');
			
			// If src is not set or different from data-src, set it
			var imgSrc = $img.attr('src');
			if (!imgSrc || imgSrc === '' || imgSrc === 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7') {
				var dataSrc = $img.attr('data-src') || $img.attr('src');
				if (dataSrc) {
					$img.attr('src', dataSrc);
				}
			}
			
			// Trigger load if image hasn't loaded yet
			if (!$img[0].complete) {
				$img[0].src = $img.attr('src');
			}
		}
	}
	
	if($('#home-slider-canvas').length){
		// Preload images for visible thumbnails when slider initializes
		$('#home-slider-thumbs .slides > li').each(function(index) {
			var $thumb = $(this);
			
			// Preload first 3 images immediately (visible on most screens)
			if (index < 3) {
				preloadSlideImage(index);
			}
			
			// Add click handler to preload image before slide change
			$thumb.on('click', function(e) {
				preloadSlideImage(index);
			});
		});
		
		$('#home-slider-canvas').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav: true,  // Enable navigation arrows
			animationLoop: true,  // Enable infinite loop
			slideshow: false,
			sync: "#home-slider-thumbs",
			// Removed start callback - Fancybox is initialized separately with full zoom config
			before: function(slider) {
				// Preload next slide image before transition (ensures smooth mobile experience)
				var nextIndex = slider.animatingTo;
				preloadSlideImage(nextIndex);
				
				// Also preload adjacent slides for smoother navigation
				if (nextIndex > 0) {
					preloadSlideImage(nextIndex - 1);
				}
				if (nextIndex < slider.count - 1) {
					preloadSlideImage(nextIndex + 1);
				}
			}
		});
	}

	// Fancybox 3.x initialization with native touch support
	// Handle images and videos
	// EXCLUDE Flexslider clones (created for infinite loop) to prevent duplicates in Fancybox gallery
	$('#home-slider-canvas ul.slides > li:not(.clone):not(.flex-clone)').each(function(){
		var $li = $(this);
		var $img = $li.find('img');
		
		// Skip if already wrapped
		if ($li.find('a[data-fancybox]').length > 0) {
			return;
		}
		
		if ($img.length > 0) {
			var videoUrl = $img.data('video-url');
			
			// If this is a video slide, use video URL
			if (videoUrl) {
				// Check if it's a local video file (starts with video/)
				if (videoUrl.indexOf('video/') === 0 || videoUrl.indexOf('/video/') !== -1) {
					// Local video file - construct full URL using BASE_PATH
					var basePath = (typeof BASE_PATH !== 'undefined') ? BASE_PATH : '/';
					// Remove leading slash from videoUrl if present, then combine with BASE_PATH
					var cleanUrl = videoUrl.replace(/^\/+/, '');
					var fullVideoUrl = basePath + cleanUrl;
					
					// Use 'video' type (Fancybox 3.x auto-detects video from extension)
					if ($li.hasClass('video-slide')) {
						$li.wrapInner('<a data-fancybox="gallery1" class="fancybox fancybox_listing_link" data-type="video" href="' + fullVideoUrl + '"></a>');
					} else {
						$img.wrap('<a data-fancybox="gallery1" class="fancybox fancybox_listing_link" data-type="video" href="' + fullVideoUrl + '"></a>');
					}
				} else {
					// YouTube/Vimeo URL - Fancybox will auto-detect
					if ($li.hasClass('video-slide')) {
						$li.wrapInner('<a data-fancybox="gallery1" class="fancybox fancybox_listing_link" href="' + videoUrl + '"></a>');
					} else {
						$img.wrap('<a data-fancybox="gallery1" class="fancybox fancybox_listing_link" href="' + videoUrl + '"></a>');
					}
				}
			} else {
				// Regular image
				var fullImage = $img.data('full-image');
				if (fullImage) {
					$img.wrap('<a data-fancybox="gallery1" class="fancybox fancybox_listing_link" href="' + fullImage + '"></a>');
				}
			}
		}
    });
	
	// Initialize Fancybox 3 with native touch gestures
	$('[data-fancybox="gallery1"]').fancybox({
		// Enable infinite loop navigation
		loop: true,
		
		// Ensure loop works by preventing end of gallery behavior
		protect: false,
		
		// Disable hash in URL (prevents #gallery1-X from appearing in URL)
		hash: false,
		
		// Video configuration for local video files
		video: {
			tpl: '<video class="fancybox-video" controls controlsList="nodownload" poster="{{poster}}"><source src="{{src}}" type="{{format}}" />Sorry, your browser doesn\'t support embedded videos, <a href="{{src}}">download</a> and watch with your favorite video player!</video>',
			format: '',  // Auto-detect from extension
			autoStart: true
		},
		
		// Callback pentru a seta volumul video-ului după ce se încarcă
		afterLoad: function(instance, current) {
			if (current.type === 'video' && current.$content) {
				var $video = current.$content.find('video');
				if ($video.length > 0) {
					var videoElement = $video[0];
					
					// Restaurează volumul salvat dacă există, altfel setează la 70%
					var savedVolume = localStorage.getItem('fancybox_video_volume');
					if (savedVolume !== null) {
						videoElement.volume = parseFloat(savedVolume);
					} else {
						videoElement.volume = 0.7; // Volum default 70%
					}
					
					// Salvează preferința de volum când utilizatorul o schimbă
					$video.on('volumechange', function() {
						localStorage.setItem('fancybox_video_volume', this.volume);
					});
				}
			}
		},
		
	// Native touch support (Fancybox 3 has this built-in!)
	touch: {
		vertical: false,  // Disable vertical swipe to close (keep horizontal only)
		momentum: false,   // Disable momentum to prevent conflict with zoom gestures
		swipeThreshold: 50  // Higher threshold = harder to accidentally swipe when zooming
	},
	
	// Guestures - configure pan when zoomed
	guestures: {
		// Enable pan (move) when zoomed in
		enabled: true,
		// Make pan work even with small movements
		minDistance: 10
	},
	
	// Zoom support with mouse wheel and touchpad gestures - GRADUAL zoom
	wheel: 'zoom',  // Enable mouse wheel and touchpad for gradual zoom on desktop
	
	// Disable idle time to keep arrows always visible
	idleTime: false,
	
	// Force zoom to be enabled
	smallBtn: 'auto',
		
	// Mobile specific settings
	mobile: {
		preventCaptionOverlap: false,
		idleTime: false,
		clickContent: function(current, event) {
			// Return false - we use manual handler for all devices
			return false;
		},
		clickSlide: false,  // Disable close on slide click to prevent accidental closes
		dblclickContent: function(current, event) {
			// Double click zoom
			return current.type === 'image' ? 'zoom' : false;
		},
		dblclickSlide: function(current, event) {
			return false;
		}
	},
		
		// Keyboard navigation - allow all arrows for scrolling
		keyboard: {
			ESCAPE: 'close',
			DELETE: 'close',
			BACKSPACE: 'close',
			PAGE_UP: 'prev',
			PAGE_DOWN: 'next',
			ARROW_UP: true,      // Allow up arrow for scrolling
			ARROW_DOWN: true,    // Allow down arrow for scrolling
			ARROW_LEFT: true,    // Allow left arrow for scrolling
			ARROW_RIGHT: true    // Allow right arrow for scrolling
		},
		
		// Animation settings
		animationEffect: 'fade',
		animationDuration: 250,
		transitionEffect: 'fade',
		transitionDuration: 250,
		
		// SlideShow settings - time between images
		slideShow: {
			autoStart: false,  // Don't start automatically
			speed: 1000  // 1 second per image
		},
		
		// UI settings - toolbar buttons (zoom button removed - use double-click instead)
		buttons: [
			'slideShow',
			'thumbs',
			'close'
		],
		
	// DESKTOP: Click does nothing (allows drag to work), double-click zooms
	clickContent: false,  // Disable zoom on single click (we use dblclick for zoom)
	
	// Double click zooms
	dblclickContent: false,  // Disable default - we handle this manually for better control
	
	// Disable close on slide click (outside image) to prevent accidental closes
	clickSlide: false,
	dblclickSlide: false,
	
	// Image settings
	image: {
		preload: true,
		protect: false
	},
		
	// Toolbar on top
	toolbar: true,
	infobar: true,
		
		// Arrow navigation - ensure visible
		arrows: true,
		
		// Margins
		margin: [44, 0],
		
	// Gutter between slides
	gutter: 30,
	
	// Prevent closing on background click and outside clicks
	closeClickOutside: false,
	closeExisting: false,
		
		// Base class for layout
		baseClass: 'fancybox-custom',
		
		// Custom callbacks for better mobile experience
		beforeShow: function(instance, current) {
			// Disable page scroll when fancybox is open
			$('body').css('overflow', 'hidden');
			
			// Reset zoom state when changing slides
			instance.isManuallyZoomed = false;
			instance.$refs.container.removeClass('fancybox-can-pan fancybox-can-drag');
			
			// Remove hash from URL if Fancybox tries to add it
			if (window.location.hash && window.location.hash.indexOf('gallery1') !== -1) {
				history.replaceState(null, null, window.location.pathname + window.location.search);
			}
		},
		
	afterShow: function(instance, current) {
		// Remove hash from URL after showing (Fancybox might add it despite hash: false)
		if (window.location.hash && window.location.hash.indexOf('gallery1') !== -1) {
			history.replaceState(null, null, window.location.pathname + window.location.search);
		}
		
		// DESKTOP ONLY: Add manual double-click zoom handler (150% zoom toggle)
		// Only apply on DESKTOP, not on MOBILE (mobile uses native Fancybox zoom)
		var isDesktop = !('ontouchstart' in window) && window.innerWidth > 768;
		
		if (isDesktop && current.type === 'image' && current.$content && current.$image) {
			var $image = current.$image;
			var $content = current.$content;
			
			// Store original dimensions for zoom calculation
			var naturalWidth = $image[0].naturalWidth || $image.width();
			var naturalHeight = $image[0].naturalHeight || $image.height();
			
			// Store the "fit to screen" size when image first loads
			var fitToScreenWidth = $content.width();
			var fitToScreenHeight = $content.height();
			
			// Track zoom state: false = fit to screen, true = zoomed to 150%
			var isZoomed = false;
			var zoomFactor = 1.5; // 150% zoom
			
			// Variables for dragging
			var isDragging = false;
			var startX, startY, scrollLeft, scrollTop;
			
			// Function to perform zoom (used by double-click)
			var performZoom = function(e) {
				var $slide = current.$slide;
				
				if (isZoomed) {
					// ZOOM OUT - restore to original size and center
					
					// Restore to original fit-to-screen dimensions (saved at load)
					$content.css({
						'width': fitToScreenWidth + 'px',
						'height': fitToScreenHeight + 'px',
						'max-width': 'none',
						'max-height': 'none',
						'position': 'absolute',
						'left': '50%',
						'top': '50%',
						'transform': 'translate(-50%, -50%)',
						'-webkit-transform': 'translate(-50%, -50%)',
						'transition': 'none'
					});
					
					isZoomed = false;
					
					// Update cursor
					$content.css('cursor', 'zoom-in');
					if (instance.$refs && instance.$refs.container) {
						instance.$refs.container.removeClass('fancybox-can-zoomOut fancybox-can-pan');
						instance.$refs.container.addClass('fancybox-can-zoomIn');
					}
				} else {
					// ZOOM IN - Simple zoom with transform centering (most reliable method)
					var targetWidth = naturalWidth * zoomFactor;
					var targetHeight = naturalHeight * zoomFactor;
					
					// Apply zoom with absolute positioning and transform centering
					$content.css({
						'width': targetWidth + 'px',
						'height': targetHeight + 'px',
						'max-width': 'none',
						'max-height': 'none',
						'position': 'absolute',
						'left': '50%',
						'top': '50%',
						'transform': 'translate(-50%, -50%)',
						'-webkit-transform': 'translate(-50%, -50%)'
					});
					
					isZoomed = true;
					
					// Update cursor
					$content.css('cursor', 'grab');
					if (instance.$refs && instance.$refs.container) {
						instance.$refs.container.removeClass('fancybox-can-zoomIn');
						instance.$refs.container.addClass('fancybox-can-zoomOut fancybox-can-pan');
					}
				}
			};
			
			// DESKTOP ONLY: Add DOUBLE-CLICK handler for 150% zoom toggle
			$content.off('dblclick.manualZoom').on('dblclick.manualZoom', function(e) {
				e.preventDefault();
				e.stopPropagation();
				performZoom(e);
			});
			
			// DESKTOP ONLY: Add DRAG functionality when zoomed in
			$content.off('mousedown.drag').on('mousedown.drag', function(e) {
				// Only allow dragging if zoomed in
				if (!isZoomed) {
					return;
				}
				
				isDragging = true;
				$content.css('cursor', 'grabbing');
				
				var $slide = current.$slide;
				startX = e.pageX - $slide.offset().left;
				startY = e.pageY - $slide.offset().top;
				scrollLeft = $slide.scrollLeft();
				scrollTop = $slide.scrollTop();
			});
			
			$(document).off('mousemove.drag').on('mousemove.drag', function(e) {
				if (!isDragging) return;
				
				e.preventDefault();
				
				var $slide = current.$slide;
				var x = e.pageX - $slide.offset().left;
				var y = e.pageY - $slide.offset().top;
				var walkX = (x - startX) * 2; // Multiply by 2 for faster scrolling
				var walkY = (y - startY) * 2;
				
				$slide.scrollLeft(scrollLeft - walkX);
				$slide.scrollTop(scrollTop - walkY);
			});
			
			$(document).off('mouseup.drag').on('mouseup.drag', function() {
				if (isDragging) {
					isDragging = false;
					if (isZoomed) {
						$content.css('cursor', 'grab');
					}
					
					// Small delay to prevent double-click trigger after drag
					setTimeout(function() {
						isDragging = false;
					}, 100);
				}
			});
			
			// Initial cursor state - can zoom in
			if (instance.$refs && instance.$refs.container) {
				instance.$refs.container.addClass('fancybox-is-zoomable fancybox-can-zoomIn');
			}
		}
	},
		
		afterClose: function(instance, current) {
			// Remove hash from URL when closing Fancybox
			if (window.location.hash && window.location.hash.indexOf('gallery1') !== -1) {
				history.replaceState(null, null, window.location.pathname + window.location.search);
			}
			// Re-enable page scroll
			$('body').css('overflow', '');
		}
	});
	
	if($('#home-slider-thumbs').length){
		$('#home-slider-thumbs').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav:true,
			animationLoop: true,  // Enable infinite loop
			slideshow: false,
			itemWidth: 171,
			itemMargin: 5,
			asNavFor: '#home-slider-canvas',
			start: function(slider) {
				// Preload thumbnail images that are visible
				$('#home-slider-thumbs .slides > li').each(function(index) {
					if (index < 6) {
						// Preload first 6 thumbnail images (visible on most screens)
						var $thumbImg = $(this).find('img[loading="lazy"]');
						if ($thumbImg.length > 0) {
							$thumbImg.attr('loading', 'eager');
						}
					}
				});
			}
		}); 
	}
	
	
  	if($('.recent_blog_posts').length){
		$('.recent_blog_posts').bxSlider({
			mode: 'vertical',
			moveSlides: 1,
			auto: false,
			speed: 500,
			pager: false,
			minSlides: 2,
			maxSlides: 2,
			nextSelector: '.blog_post_controls',
			prevSelector: '.blog_post_controls',
			nextText: '<i class=" fa fa-chevron-up"></i>',
			prevText: '<i class=" fa fa-chevron-down"></i>',
			adaptiveHeight: true
		});
	}
	
	// my tab
	if($('#myTab a').length){
		$('#myTab a').click(function (e) {
			e.preventDefault()
			$(this).tab('show');
		});
		
		$('#myTab a').on('shown.bs.tab', function (e) {			
			if($("div.inventory").length){
				//$("div.inventory").evenElements();
			}
		})
	}
	
	// fix parent nav links
	/*$("header li.dropdown a.dropdown-toggle").each( function(index, element){
		if(typeof $(this).attr("href") !== "undefined"){
			$(this).addClass("disabled");
		}
	});*/
	
	$(".flip").on({
		mouseenter: function(){	
			if($(this).css('border-top-color') != 'rgb(0, 255, 0)'){
				$(this).find('.card').addClass('flipped');
			}
		},
		mouseleave: function(){
			if($(this).css('border-top-color') != 'rgb(0, 255, 0)'){
				$(this).find('.card').removeClass('flipped');
			}
		}
	});
	
	if($(".fancybox_div").length){
		$(".fancybox_div").each( function(index, element){
			$(this).fancybox({
				'width' : '600',
				'type'  : 'ajax'
			});
		});
	}
	
	if($(".view-video").length){
		$(".view-video").each(function() {
			var ele = $(this);
			
			$(this).fancybox({
				'href'       : '#youtube_video',
				'height'     : '320',
				'width'      : '560',
				'fitToView'  : false,
				'autoSize'   : false,
				'maxWidth'	 : '90%',
				'beforeLoad' : function(){
					var youtube_url = 'http://www.youtube.com/embed/' + ele.data("youtube-id") + '?vq=hd720&autoplay=1';
					
					$("#youtube_video iframe").attr("src", youtube_url);
				},
				'afterClose' : function(){
					$("#youtube_video iframe").attr("src", "#");
				}
			});
		});
	}
	
	function flip_card_size(){
		$(".flip").each( function(index, element){	
			var frontHeight = $(this).find('.front img').outerHeight();
			var frontWidth  = $(this).find('.front img').outerWidth();
			
			$(this).find('.flip, .back, .front, .card').height(frontHeight);
			$(this).height(frontHeight);
			
			$(this).find('.flip, .back, .front, .card').width(frontWidth);
			$(this).width(frontWidth);
		});
	}
	
	function flip_card_reset_size(){
		$(".flip").each( function(index, element){				
			$(this).find('.flip, .back, .front, .card').removeAttr("style").css("width", "auto");
			$(this).removeAttr("style").css("width", "auto"); 
		});
	}
	

	//\\flip_card_size();
	
	if($(".back_to_top").length){
		$(".back_to_top").click(function() {
		   $("html, body").animate({ scrollTop: 0 }, "slow");
		   return false;
		});
		
		$(window).scroll(function() {
			var height = $(window).scrollTop();
		
			if(height > 300) {
				$(".back_to_top").fadeIn();
			} else {
				$(".back_to_top").fadeOut();
			}
		});
	}
	
	if($(".portfolioContainer").length){
		//$(".portfolioContainer > div").evenElements();
	}
	
	// ajax_form
	$(document).on("submit", ".ajax_form", function(e){
		e.preventDefault();
		
		var empty_input = false;
		
		$(this).find("*:not(input[type='submit'])").filter(":input").each( function(index, element){
			if(!$(this).val().trim()){
				empty_input = true;
				
				$(this).css("border", "1px solid #F00");
			} else {
				$(this).attr("style", "");
			}
		});
			
		//check recaptcha
		// if($("#recaptcha").length){
		// 	var recaptcha_challenge_field = $(this).find("input[name='recaptcha_challenge_field']").val();
		// 	var recaptcha_response_field  = $(this).find("input[name='recaptcha_response_field']").val();
			
		// 	$.ajax({
		// 		type: "POST",
		// 		url: "includes/recaptcha_check.php",
		// 		data: {recaptcha_challenge_field: recaptcha_challenge_field, recaptcha_response_field: recaptcha_response_field },
		// 		success: function(data){
		// 			if(data != "success"){
		// 				if(!$(".recaptcha_message").length){
		// 					$("#recaptcha").after( "<div class='recaptcha_message'>" + data + "</div>" );
		// 				} else {
		// 					$(".recaptcha_message").html(data);
		// 				}
		// 				return false;
		// 			}
		// 		}
		// 	});
		// }
		
		if(empty_input == false){
			var form_values  = $(this).serialize();
			var form_name    = $(this).attr("name");
			var current_form = $(this);
			
			$.ajax({
				type: "POST",
				url: "ajax_form.php",
				data: form_values + "&form=" + form_name,
				success: function(data){
					if(data == "Sent Successfully"){
						$.fancybox.close();				
					} else {
						if($(".error_list").length){
							$(".error_list").remove();
						}
						
						current_form.prepend(data);
					}
				}
			});
		}
	});
	
	function print_tabs(){
		if(!$(".print_tabs").length){
			// generate google map
			var longitude  = $("#google-map-listing").data("longitude");
			var latitude   = $("#google-map-listing").data("latitude");
			var zoom       = $("#google-map-listing").data("zoom");
			
			var google_map = "<img data-no-retina src='http://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=" + zoom + "&size=700x200&markers=color:blue|label:S|" + latitude + "," + longitude + "&sensor=false'>";
			
			$(".example-tabs").each( function() {		
				var tabs_html = "";			
				$(this).find(".nav-tabs li").each( function(index, element) {			
					tabs_html += "<div><h2>" + $(this).text() + "</h2><br />";			
					
					tabs_html += ($(this).find("a").attr("href") == "#location" ? google_map : $(".tab-content .tab-pane").eq(index).html()) + "</div><br /><br />";			
				});
				
				$(this).after("<div class='print_friendly print_tabs'>" + tabs_html + "</div>");		
			});
		}
	}
	
	function print_header(){
		if(!$(".print_header").length){
			var header_html = "";
			
			header_html += $(".logo").html();
			header_html += $(".company_info").html();
			
			$("body").prepend("<div class='print_friendly print_header'>" + header_html + "</div>");
			
			$(".inventory-heading").append("<div style='clear: both;'></div>");
		}
	}
	
	function print_images(){
		if(!$(".print_image").length){
			var images_html = "";
			
			$("#home-slider-thumbs li").slice(0, 6).each( function(index, element){
				images_html += $(this).html() + (index == 1 || index == 3 ? "<br>" : "");
			});
			
			var car_info = $(".car-info").clone().html();
			
			$(".print_tabs").prepend("<div class='print_friendly print_image'>" + images_html + "<br></div><div class='car-info'>" + car_info + "</div><div style='clear: both;'></div>");
		}
	}
	
	$(".print_page").click( function(){
		print_tabs();
		print_header();
		print_images();
		
		window.print();
	});
	
	if($(".financing_calculator").length){
		// Financing Calculator
		$(document).on("click", '.financing_calculator .calculate', function() {		
			var calculator   = $(this).closest(".financing_calculator");
			
			var cost         = calculator.find(".cost").val();
			var down_payment = calculator.find(".down_payment").val();
			var interest     = calculator.find(".interest").val();
			var loan_years   = calculator.find(".loan_years").val();
			var frequency    = calculator.find(".frequency").val();
			
			if( !cost || !down_payment || !interest || !loan_years  || isNaN(cost) || isNaN(down_payment) || isNaN(interest) || isNaN(loan_years) ){
				if(!cost || isNaN(cost)){
					calculator.find(".cost").addClass("error");
				} else { 
					calculator.find(".cost").removeClass("error");
				}
				
				if(!down_payment || isNaN(down_payment)){
					calculator.find(".down_payment").addClass("error");
				} else { 
					calculator.find(".down_payment").removeClass("error");
				}	
					
				if(!interest || isNaN(interest)){
					calculator.find(".interest").addClass("error");
				} else { 
					calculator.find(".interest").removeClass("error");
				}	
						
				if(!loan_years || isNaN(loan_years)){
					calculator.find(".loan_years").addClass("error");
				}	 else { 
					calculator.find(".loan_years").removeClass("error");
				}	
								
				return;
			}
			
			calculator.find("input").removeClass("error");
			
			switch(frequency) {
				case "0":
					frequency_rate = 26;
					break;
				case "1":
					frequency_rate = 52;
					break;
				case "2":
					frequency_rate = 12;
					break;			
			}
			
			interest_rate = (interest) / 100;
			rate          = interest_rate / frequency_rate;
			payments      = loan_years * frequency_rate;
			difference    = cost - down_payment;
		
			payment = Math.floor((difference*rate)/(1-Math.pow((1+rate),(-1*payments)))*100)/100;
			
			calculator.find(".payments").text(payments);
			calculator.find(".payment_amount").text("$" + payment);
		});
	}
	
});
	
// have to wait until DOM is fully loaded (images too)
$(window).load(function(){ 
	
	
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
		
		var index = $(this).parent().index();
		
		if(index == 3){
			setTimeout( function(){
				init_google_map();
			}, 500);
		}
	});
	
	
	// google map
	function init_google_map(){		
		
		if($("#google-map-listing").length){
			var latitude     = $("#google-map-listing").data('latitude');
			var longitude    = $("#google-map-listing").data('longitude');
			var zoom         = $("#google-map-listing").data('zoom');
			var scroll_wheel = $("#google-map-listing").data('scroll');
			var style        = $("#google-map-listing").data('style');
			var parallax     = $("#google-map-listing").data('parallax');
			
			if(latitude && longitude){				
				var myLatlng = new google.maps.LatLng(latitude, longitude);
				var myOptions = {
					zoom: zoom,
					center: myLatlng,
					popup: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}

				if(parallax != false && typeof parallax == "undefined"){
					myOptions.scroll = {
						x:$(window).scrollLeft(),
						y:$(window).scrollTop()
					}
				}
				
				if(scroll_wheel == false && typeof scroll_wheel != "undefined"){
					myOptions.scrollwheel = false;
				}
				
				if(typeof style != "undefined"){
					myOptions.styles = style;
				}				
				
				var map = new google.maps.Map(document.getElementById("google-map-listing"), myOptions);
				
				var marker = new google.maps.Marker({
					position: myLatlng, 
					map: map,
					title: "Our Location"
				});

				if(parallax != false && typeof parallax == "undefined"){
					var offset = $("#google-map-listing").offset();
				    map.panBy(((myOptions.scroll.x-offset.left)/3),((myOptions.scroll.y-offset.top)/3));
				      
				    google.maps.event.addDomListener(window, 'scroll', function(){
					    var scrollY = $(window).scrollTop(),
					        scrollX = $(window).scrollLeft(),
					        scroll  = map.get('scroll');
					    
					    if(scroll){
							map.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
					    }

					    map.set('scroll',{
					    	x:scrollX,
					    	y:scrollY
					    });
					});
				}

				google.maps.event.addListener(marker, 'click', function() {
					map.setZoom(zoom);
				});
			}
		}
	}
	
	init_google_map();
});
	
function rev_iframe(){
	// Check if Revolution Slider is loaded and element exists
	if (typeof jQuery !== 'undefined' && 
	    typeof jQuery.fn.revolution !== 'undefined' && 
	    jQuery('.tp-banner').length > 0) {
		try {
			jQuery('.tp-banner').revolution().revnext();
		} catch(e) {
			console.warn('Revolution Slider initialization error:', e);
		}
	}
}