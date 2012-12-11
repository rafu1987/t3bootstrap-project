/*
 * 	easyAccordion 0.1 - jQuery plugin
 *	written by Andrea Cima Serniotti
 *	http://www.madeincima.eu
 *
 *	Copyright (c) 2010 Andrea Cima Serniotti (http://www.madeincima.eu)
 *	Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.
 *	Built for jQuery library http://jquery.com
 */

(function($) {
	$.fn.easyAccordion = function(options) {

		var defaults = {
			slideNum: true,
			autoStart: false,
			slideInterval: 3000
		};

		this.each(function() {

			var settings = $.extend(defaults, options);
			$(this).find('dl').addClass('easy-accordion');


			// -------- Set the variables ------------------------------------------------------------------------------

			$.fn.setVariables = function() {
				dlWidth = $(this).width();
				dlHeight = $(this).height();
				dtWidth = $(this).find('dt').outerHeight();
				if ($.browser.msie){ dtWidth = $(this).find('dt').outerWidth();}
				dtHeight = dlHeight - ($(this).find('dt').outerWidth()-$(this).find('dt').width());
				slideTotal = $(this).find('dt').size();
				ddWidth = dlWidth - (dtWidth*slideTotal) - ($(this).find('dd').outerWidth(true)-$(this).find('dd').width());
				ddHeight = dlHeight - ($(this).find('dd').outerHeight(true)-$(this).find('dd').height());
			};
			$(this).setVariables();


			// -------- Fix some weird cross-browser issues due to the CSS rotation -------------------------------------

			if ($.browser.safari){ var dtTop = (dlHeight-dtWidth)/2; var dtOffset = -dtTop;  /* Safari and Chrome */ }
			if ($.browser.mozilla){ var dtTop = dlHeight - 20; var dtOffset = - 20; /* FF */ }
			if ($.browser.msie){
				/* IE */
				var dtTop = 0;
				var dtOffset = 0;
				if($.browser.version >= 10.0){
					dtWidth = 30;
					$('.easy-accordion dt').css('-ms-transform', 'rotate(-90deg)');
					$('.easy-accordion dt').css('-ms-transform-origin', '123px 122px');
					$('.easy-accordion .slide-number').css('-ms-transform', 'rotate(90deg)');
					$('.easy-accordion .slide-number').css('-ms-transform-origin', '15px 21px');
					ddWidth = dlWidth - (dtWidth*slideTotal) - ($(this).find('dd').outerWidth(true)-$(this).find('dd').width());
				}
			}


			// -------- Getting things ready ------------------------------------------------------------------------------

			var f = 1;
			$(this).find('dt').each(function(){
				$(this).css({'width':dtHeight,'top':dtTop,'margin-left':dtOffset});
				if(settings.slideNum == true){
					$('<span class="slide-number">'+(f<10?'0':'')+f+'</span>').appendTo(this);
					if($.browser.msie){
						var slideNumLeft = parseInt($(this).find('.slide-number').css('left')) - 14;
						$(this).find('.slide-number').css({'left': slideNumLeft})
						if($.browser.version == 6.0 || $.browser.version == 7.0){
							$(this).find('.slide-number').css({'bottom':'auto'});
						}
						if($.browser.version == 8.0){
						var slideNumTop = $(this).find('.slide-number').css('bottom');
						var slideNumTopVal = parseInt(slideNumTop) + parseInt($(this).css('padding-top'))  - 12;
						$(this).find('.slide-number').css({'bottom': slideNumTopVal});
						}
					} else {
						var slideNumTop = $(this).find('.slide-number').css('bottom');
						var slideNumTopVal = parseInt(slideNumTop) + parseInt($(this).css('padding-top'));
						$(this).find('.slide-number').css({'bottom': slideNumTopVal});
					}
				}
				f = f + 1;
			});

			if($(this).find('.active').size()) {
				$(this).find('.active').next('dd').addClass('active');
			} else {
				$(this).find('dt:first').addClass('active').next('dd').addClass('active');
			}

			$(this).find('dt:first').css({'left':'0'}).next().css({'left':dtWidth});
			$(this).find('dd').css({'width':ddWidth,'height':ddHeight});


			// -------- Functions ------------------------------------------------------------------------------

			$.fn.findActiveSlide = function() {
					var i = 1;
					this.find('dt').each(function(){
					if($(this).hasClass('active')){
						activeID = i; // Active slide
					} else if ($(this).hasClass('no-more-active')){
						noMoreActiveID = i; // No more active slide
					}
					i = i + 1;
				});
			};

			$.fn.calculateSlidePos = function() {
				var u = 2;
				$(this).find('dt').not(':first').each(function(){
					var activeDtPos = dtWidth*activeID;
					if(u <= activeID){
						var leftDtPos = dtWidth*(u-1);
						$(this).animate({'left': leftDtPos});
						if(u < activeID){ // If the item sits to the left of the active element
							$(this).next().css({'left':leftDtPos+dtWidth});
						} else{ // If the item is the active one
							$(this).next().animate({'left':activeDtPos});
						}
					} else {
						var rightDtPos = dlWidth-(dtWidth*(slideTotal-u+1));
						$(this).animate({'left': rightDtPos});
						var rightDdPos = rightDtPos+dtWidth;
						$(this).next().animate({'left':rightDdPos});
					}
					u = u+ 1;
				});
				setTimeout( function() {
					$('.easy-accordion').find('dd').not('.active').each(function(){
						$(this).css({'display':'none'});
					});
				}, 400);

			};

			$.fn.activateSlide = function() {
				this.parent('dl').setVariables();
				this.parent('dl').find('dd').css({'display':'block'});
				this.parent('dl').find('dd.plus').removeClass('plus');
				this.parent('dl').find('.no-more-active').removeClass('no-more-active');
				this.parent('dl').find('.active').removeClass('active').addClass('no-more-active');
				this.addClass('active').next().addClass('active');
				this.parent('dl').findActiveSlide();
				if(activeID < noMoreActiveID){
					this.parent('dl').find('dd.no-more-active').addClass('plus');
				}
				this.parent('dl').calculateSlidePos();
			};

			$.fn.rotateSlides = function(slideInterval, timerInstance) {
				var accordianInstance = $(this);
				timerInstance.value = setTimeout(function(){accordianInstance.rotateSlides(slideInterval, timerInstance);}, slideInterval);
				$(this).findActiveSlide();
				var totalSlides = $(this).find('dt').size();
				var activeSlide = activeID;
				var newSlide = activeSlide + 1;
				if (newSlide > totalSlides) newSlide = 1;
				$(this).find('dt:eq(' + (newSlide-1) + ')').activateSlide(); // activate the new slide
			}


			// -------- Let's do it! ------------------------------------------------------------------------------

			function trackerObject() {this.value = null}
			var timerInstance = new trackerObject();

			$(this).findActiveSlide();
			$(this).calculateSlidePos();

			if (settings.autoStart == true){
				var accordianInstance = $(this);
				var interval = parseInt(settings.slideInterval);
				timerInstance.value = setTimeout(function(){
					accordianInstance.rotateSlides(interval, timerInstance);
					}, interval);
			}

			$(this).find('dt').not('active').click(function(){
				$(this).activateSlide();
				clearTimeout(timerInstance.value);
			});

			if (!($.browser.msie && $.browser.version == 6.0)){
				$('dt').hover(function(){
					$(this).addClass('hover');
				}, function(){
					$(this).removeClass('hover');
				});
			}
		});
	};
})(jQuery);