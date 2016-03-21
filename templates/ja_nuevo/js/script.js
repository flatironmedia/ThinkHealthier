/**
 * ------------------------------------------------------------------------
 * JA Nuevo template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
 
(function($){
	//Add grayscale image for partners 
	$(window).load(function() {
		$('.partners img').each(function() {
			$(this).wrap('<div style="display:inline-block;width:' + this.width + 'px;height:' + this.height + 'px;">').clone().addClass('gotcolors').css({'position': 'absolute', 'opacity' : 0 }).insertBefore(this);
			this.src = grayscale(this.src);
		}).animate({opacity: 0.5}, 500);
	});
	
	$(document).ready(function() {
		$(".partners a").hover(
			function() {
				$(this).find('.gotcolors').stop().animate({opacity: 1}, 200);
			}, 
			function() {
				$(this).find('.gotcolors').stop().animate({opacity: 0}, 500);
			}
		);
		
		//video
		var iframe = $('#player1')[0];
		
		if (iframe) {
		
			var player = $f(iframe);
	
			$('.btn-pause').hide();
			
			player.addEvent('ready', function() {
			    player.addEvent('pause', onPause);
			    player.addEvent('finish', onFinish);
			});
		
			// Call the API when a button is pressed
			$('.btn-play').bind('click', function() {
			    player.api('play');
			    $('.btn-play').hide();
			    $('.btn-pause').show();
			});
			
			$('.btn-pause').bind('click', function() {
			    player.api('pause');
			});
		
			function onPause(id) {
				$('.btn-play').show();
			  $('.btn-pause').hide();
			}
		
			function onFinish(id) {
				$('.btn-play').show();
			  $('.btn-pause').hide();
			}
		
		}
		
	});
	
	function grayscale(src) {
		var supportsCanvas = !!document.createElement('canvas').getContext;
		if (supportsCanvas) {
			var canvas = document.createElement('canvas'), 
			context = canvas.getContext('2d'), 
			imageData, px, length, i = 0, gray, 
			img = new Image();
			
			img.src = src;
			canvas.width = img.width;
			canvas.height = img.height;
			context.drawImage(img, 0, 0);
				
			imageData = context.getImageData(0, 0, canvas.width, canvas.height);
			px = imageData.data;
			length = px.length;
			
			for (; i < length; i += 4) {
				//gray = px[i] * .3 + px[i + 1] * .59 + px[i + 2] * .11;
				//px[i] = px[i + 1] = px[i + 2] = gray;
				px[i] = px[i + 1] = px[i + 2] = (px[i] + px[i + 1] + px[i + 2]) / 3;
			}
					
			context.putImageData(imageData, 0, 0);
			return canvas.toDataURL();
		} else {
			return src;
		}
	}
	
	//Fix bug tab typography
	 $(document).ready(function(){
		if($('.docs-section .nav.nav-tabs').length > 0){
			$('.docs-section .nav.nav-tabs a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
            });
		}
	});
 })(jQuery);
 
 //Portfolio page - Used JQuery Isotope & infinitescroll
(function($){
    // Modified Isotope methods for gutters in masonry
    $.Isotope.prototype._getMasonryGutterColumns = function() {
        var gutter = this.options.masonry && this.options.masonry.gutterWidth || 0;

        containerWidth = this.element.width();

        this.masonry.columnWidth = this.options.masonry && this.options.masonry.columnWidth ||
            // Or use the size of the first item
            this.$filteredAtoms.outerWidth(true) ||
            // If there's no items, use size of container
            containerWidth;

        this.masonry.columnWidth += gutter;

        this.masonry.cols = Math.floor((containerWidth + gutter) / this.masonry.columnWidth);
        this.masonry.cols = Math.max(this.masonry.cols, 1);
    };

    $.Isotope.prototype._masonryReset = function() {
        // Layout-specific props
        this.masonry = {};
        // FIXME shouldn't have to call this again
        this._getMasonryGutterColumns();
        var i = this.masonry.cols;
        this.masonry.colYs = [];
        while (i--) {
            this.masonry.colYs.push(0);
        }
    };

    $.Isotope.prototype._masonryResizeChanged = function() {
        var prevSegments = this.masonry.cols;
        // Update cols/rows
        this._getMasonryGutterColumns();
        // Return if updated cols/rows is not equal to previous
        return (this.masonry.cols !== prevSegments);
    };
})(jQuery);

(function($){ 
	$(document).ready(function(){ 
		//Checking div grid.blog - used only Portfolio page 
        if($('.ja-masonry-wrap').find('.grid').length > 0){
            //isotope grid
            var $container = $('.grid.blog'),
                paginfo = $('#page-next-link'),
                nextbtn = $('#infinity-next'),
                gutter = parseInt(T3JSVars.gutter),
				iOS = parseFloat(('' + (/CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec(navigator.userAgent) || [0,''])[1]).replace('undefined', '3_2').replace('_', '.').replace('_', '')) || false;
                firstLoad = function(){
                    if(!(nextbtn.attr('data-fixel-infinity-end') || nextbtn.attr('data-fixel-infinity-done'))){
                        nextbtn.removeClass('hidden');
                    }
                },
                pathobject = {
                    init: function(link){
                        var pagenext = $('#page-next-link'),
                            fromelm = false;
                        if(!link) {
                            fromelm = true;
                            link = pagenext.attr('href') || '';
                        }
                        this.path = link;
                        var match = this.path.match(/((page|limitstart|start)[=-])(\d*)(&*)/i);
                        if(match){
                            this.type = match[2].toLowerCase();
                            this.number = parseInt(match[3]);
                            this.limit = this.type == 'page' ? 1 : this.number;
                            this.number = this.type == 'page' ? this.number : 1;
                            this.init = 0;
                            var limit = parseInt(pagenext.attr('data-limit')),
                                init = parseInt(pagenext.attr('data-start'));
                            if(isNaN(limit)){
                                limit = 0;
                            }
                            if(isNaN(init)){
                                init = 0;
                            }
                            if(fromelm && this.limit != limit && (this.type == 'start' || this.type == 'limitstart')){
                                this.init = Math.max(0, init);
                                this.number = 1;
                                this.limit = limit;
                            }

                        } else {
                            this.type = 'unk';
                            this.number = 2;
                            this.path = this.path + (this.path.indexOf('?') == -1 ? '?' : '') + 'start=';
                        }

                        var urlparts = this.path.split('#');
                        if(urlparts[0].indexOf('?') == -1){
                            urlparts[0] += '?tmpl=component';
                        } else {
                            urlparts[0] += '&tmpl=component';
                        }

                        this.path = urlparts.join('#');
                    },

                    join: function(){
                        if(pathobject.type == 'unk'){
                            return pathobject.path + pathobject.number++;
                        } else{
                            return pathobject.path.replace(/((page|limitstart|start)[=-])(\d*)(&*)/i, '$1' + (pathobject.init + pathobject.limit * pathobject.number++) + '$4');
                        }
                    }
                },
                colWidth = function () {
                    var w = $container.width(),
                        columnNum = 1,
                        columnWidth = 0;
                    if ($(window).width() > 1200) {
                        columnNum  = T3JSVars.itemlg;
                    } else if ($(window).width() >= 992) {
                        columnNum  = T3JSVars.itemmd;
                    } else if ($(window).width() >= 768) {
                        columnNum  = T3JSVars.itemsm;
                    } else if ($(window).width() >= 480) {
                        columnNum  = T3JSVars.itemsmx;
                    }else{
                        columnNum  = T3JSVars.itemxs;
                    }
                    columnWidth = Math.floor((w - gutter*(columnNum-1))/columnNum);

                    $container.find('.item').each(function() {
                        var $item = $(this),
                            $itemimg = $item.find('img'),
							columnw = $item.attr('class').match(/item-w(\d)/),
                            multiplier_w = columnw?((columnw[1] > columnNum) ? columnNum : columnw[1]):'',
							
							roww = $item.attr('class').match(/item-h(\d)/),
                            multiplier_h = roww?((roww[1] > columnNum) ? columnNum : roww[1]):'',
							
                            width = multiplier_w ? (columnWidth*multiplier_w)+gutter*(multiplier_w-1) : columnWidth,
                            height = (width/4) * 3;
                        
                        $item.css({
                            width: width,
                            height: height+gutter
                        });
                        //Set item article height
                        $item.find('article').css({
                            height: height
                        });

                        //add maxwidth or maxheight
                        if($itemimg.length >0){
                            $itemimg.each(function(){
                                //Remove all style before add
                                $(this).removeAttr('style');
								if($container.hasClass('grid-list')){
									$(this).css("max-height","100%");
								}else{
                                    (width/height ) > ($(this).prop('naturalWidth')/$(this).prop('naturalHeight'))?$(this).css("max-width","100%"):$(this).css("max-height","100%");
								}
                            });
                        }
						if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || iOS) {
							// some code..
							$item.find('.item-desc').css('display','block');
						}
                    });
                    return columnWidth;
                },
                isotope = function(){
                    $container.isotope({
                        resizable: true,
                        layoutMode : 'masonry',
                        itemSelector: '.item',
                        masonry: {
                            columnWidth: colWidth(),
                            gutterWidth : gutter
                        },
                        animationEngine:'jQuery',
                        animationOptions: {
                            duration: 500,
                            easing: 'linear',
                            queue: false
                        }
                    },firstLoad());
                };

            pathobject.init();

            $container.infinitescroll({
                    navSelector  : '#page-nav',    // selector for the paged navigation
                    nextSelector : '#page-nav a',  // selector for the NEXT link (to page 2)
                    itemSelector : '.item',     // selector for all items you'll retrieve
                    loading: {
                        finished: function(){
                            $('#infscr-loading').remove();
                        },
                        finishedMsg: T3JSVars.finishedMsg,
                        img: T3JSVars.tplUrl + '/images/ajax-load.gif',
                        msgText : '',
                        speed : 'fast',
                        start: undefined
                    },
                    state: {
                        isDuringAjax: false,
                        isInvalidPage: false,
                        isDestroyed: false,
                        isDone: false, // For when it goes all the way through the archive.
                        isPaused: false,
                        currPage: 0
                    },
                    pathParse: pathobject.join,
                    path: pathobject.join,
                    binder: $(window), // used to cache the selector for the element that will be scrolling
                    extraScrollPx: 150,
                    dataType: 'html',
                    appendCallback: true,
                    bufferPx: 350,
                    debug : false,
                    errorCallback: function () {
                        nextbtn.addClass('disabled').html(T3JSVars.finishedMsg);
                    },
                    prefill: false, // When the document is smaller than the window, load data until the document is larger or links are exhausted
                    maxPage: parseInt(nextbtn.attr('data-page-total')) // to manually control maximum page (when maxPage is undefined, maximum page limitation is not work)
                },
                // call Isotope as a callback
                function( items ) {
                    $container.isotope( 'appended', $( items ) );
                    if((items.length < parseInt(paginfo.attr('data-limit') || nextbtn.attr('data-limit') || 0)) || (parseInt(paginfo.attr('data-total')) == $container.find('.item.isotope-item').length)){
                        nextbtn.addClass('disabled').html(T3JSVars.finishedMsg);
                    }
                    //update disqus if needed
                    if(typeof DISQUSWIDGETS != 'undefined'){
                        DISQUSWIDGETS.getCount();
                    }
                    isotope();
            });

            isotope();

            $(window).unbind('.infscr');

            $(window).smartresize(isotope);
            //Next click
			var btnEvent = 'ontouchstart' in document.documentElement ? 'touchstart' : 'click';
            if(nextbtn.length){
                nextbtn.on(btnEvent, function(){
                    if(!nextbtn.hasClass('finished')){
                        $container.infinitescroll('retrieve');
                    }
                    return false;
                });
            }
        }	
	});
})(jQuery)