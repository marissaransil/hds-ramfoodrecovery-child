// begin adding 
var $j = jQuery.noConflict();

$j(document).ready(function() {
	"use strict";
	});
// end 

(function ($) {
    $(document).ready(function () {
        doJQueryStuff();
    });

    // TODO: Currently all of my JS may be triggered twice because of these 2 calls
    //$(document).ajaxSuccess(function() {
    //    doJQueryStuff();
    //});

    $(window).on("load, resize", function () {
        resizeSignatureText();
    });

    function doJQueryStuff() {
		
		// if we have a header image, move breadcrumbs	
        if ($('.title_outer').hasClass('with_image')) {
            var crumbs = $('.breadcrumb').detach();
            $('.container_inner .full_section_inner').first().prepend(crumbs);
        }

        // having the footer "uncover" onscroll breaks the Passepartout without moving the footer
        $("footer").appendTo('.paspartu_outer');

        // required for lazy load images to work
        $(window).scrollTop($(window).scrollTop() + 1);
        $("html,body").trigger("scroll");

        // for mobile devices only
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            doMobileStuff();
        }
        else {
            doDesktopStuff();
        }
		
		// if IE/Edge, manually add Google Translate.  No idea why I have to do this.
		if (isIEorEdge()) {
			new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, gaTrack: true, gaId: 'UA-31849006-1'}, 'google_translate_element');
		}

        // if mobile menu is visible change icon
        $('.mobile_menu_button').on('click', function (e) {
            $('span.icon_menu').toggleClass("icon_menu_selected"); //you can list several class names 
        });

        //automatically style tables within the body that have no class
        $('.content table:not([class])').styleTable();

        $('footer').footerReveal({
            shadow: false,
            zIndex: 1
        });

        // run these 2 on every page load as well
        resizeSignatureText();

        // for hall/apt features pages, copy link from read more to p tag
        $('.features .eltd_icon_with_title').each(function () {
            var href = $(this).find('a').attr('href');
            if (typeof href != 'undefined') {
                $(this).find('p').wrapInner('<a href="' + href + '"></a>');
            } else {
                // for some odd reason you can still click the text (and get redirected to /undefined and thus a 404) even though there is no link present
                $(this).find('*').click(false);
            }
        });
	

        $('.cq-cards-container,.eltd_icon_with_title').on('click', function () {
            // Change window.location to the url of the href inside it
            window.location = $(this).find('a').attr('href');
        });

        // place cursor in textbox when searching
        $(".side_menu_button").click(function () {
            setTimeout(
			  function () {
			      $(".search_field").focus();
			  }, 500);
        });

        //var cardhref = $('.slick-slide a').attr('href');
        //console.log(cardhref);
        //$('.cq-cards-container').attr('href',cardhref);

        // expand/collapse all - accordion
        $('#expandAll').on('click', function () {
            // new accordion
            $('.accordion-content').toggleClass('accordion-content accordion-content-expanded');

            $('.ui-accordion-header').removeClass('ui-corner-all').addClass('ui-accordion-header-active ui-state-active ui-corner-top').attr({ 'aria-selected': 'true', 'tabindex': '0' });
            $('.ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
            $('.ui-accordion-content').addClass('ui-accordion-content-active').attr({ 'aria-expanded': 'true', 'aria-hidden': 'false' }).show();
        });
        $('#collapseAll').on('click', function () {
            // new accordion
            $('.accordion-content-expanded').toggleClass('accordion-content-expanded accordion-content ');
            $('.cq-accordion input:checkbox').prop('checked', 'true');

            $('.ui-accordion-header').removeClass('ui-accordion-header-active ui-state-active ui-corner-top').addClass('ui-corner-all').attr({ 'aria-selected': 'false', 'tabindex': '-1' });
            $('.ui-accordion-header .ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
            $('.ui-accordion-content').removeClass('ui-accordion-content-active').attr({ 'aria-expanded': 'false', 'aria-hidden': 'true' }).hide();
        });

        $('#findOnPage').on('click', function () {
            $('#expandAll').click();
            $(this).find();
        });

        //// change Trumba styling
        //var cssLink = document.createElement("link")
        //cssLink.href = "http://samojotest.colostate.edu/wp-content/themes/hds/trumba.css";
        //cssLink.rel = "stylesheet";
        //cssLink.type = "text/css";
        //frames['trumba.spud.0.iframe'].document.head.appendChild(cssLink);
    }

    function doMobileStuff() {
        //$("table.familyaptrates td:nth-of-type(3):before").hide();
		// on mobile we don't need a link to CSU logo
		$('#responsiveLogo a').attr('href', '').css({'cursor': 'pointer', 'pointer-events' : 'none'});
    }

    function doDesktopStuff() {
        // removed .equal-heights .wpb_text_column because it conflicted with https://samojotest.colostate.edu/services/transportation-parking/
		// only need this to run on desktops
        $('.equal-heights .material-card-content,.sub-header-icons .vc_col-sm-2,.equal-heights .eltd_animated_elements_holder, .important-dates-wrapper .events').equalHeights();
        $('.equal-heights2 .material-card-content,.equal-heights2 .eltd_animated_elements_holder').equalHeights(); // in case you want 2 different equal heights on same page
        $('.equal-heights3 .material-card-content,.equal-heights3 .eltd_animated_elements_holder').equalHeights(); // in case you want 2 different equal heights on same page
    }
	
	function isIEorEdge() {
		
		var isIE = false;
		
		if (/MSIE 10/i.test(navigator.userAgent)) {
		   // This is internet explorer 10
		   isIE = true;
		}
		
		if (/MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent)) {
			// This is internet explorer 9 or 11
			isIE = true;
		}
		
		if (/Edge\/\d./i.test(navigator.userAgent)){
		   // This is Microsoft Edge
		   isIE = true;
		}
		
		return isIE;
	}

    function resizeSignatureText() {
        var viewportWidth = $(window).width();
        if ((viewportWidth >= 980) && (viewportWidth <= 1100)) {
            $("#BrandLogo").removeClass("fontLarge").addClass("fontSmall");
        }
        else {
            $("#BrandLogo").removeClass("fontSmall").addClass("fontLarge");
        }
        if (viewportWidth <= 1000) {
            // at smaller viewports, move title
            var titleHolder = $('.title_subtitle_holder').detach();
            $('.container_inner .full_section_inner').first().prepend(titleHolder);

            //$(".").appendTo('.container_inner');
            $(".title_subtitle_holder").show();
        }
        else {
            $(".title_subtitle_holder").show();
        }
    }

    function positionSubHeaderNav() {

        $('.sub-hdr-nav li').css({
            'position': 'absolute',
            'left': '50%',
            'top': '46%',
            'margin-left': -$('.sub-hdr-nav li').outerWidth() / 2,
            'margin-top': -$('.sub-hdr-nav li').outerHeight() / 2
        });

        //$('.sub-hdr-nav li .nav-caption').css({
        //	'position' : 'absolute',
        //	'left' : '50%',
        //	'top' : '50%',
        //	'margin-left' : -$('.sub-hdr-nav li').outerWidth()/2,
        //	'margin-top' : -$('.sub-hdr-nav li').outerHeight()/2
        //});

        //$('.sub-hdr-nav li .nav-caption').height($('.sub-header-icons').height());
        //$('.sub-header-icons').height($('.sub-hdr-nav li .nav-caption').height() + 40);
        //$('.sub-hdr-nav li .nav-caption').css({"padding":"5px"});

        //console.log( $('.sub-hdr-nav li .nav-caption').height() + 40);

    }

    (function ($) {
        $.fn.styleTable = function (options) {
            var defaults = {
                css: 'ui-styled-table'
            };
            options = $.extend(defaults, options);

            return this.each(function () {
                $this = $(this);
                $this.addClass(options.css);

                $this.on('mouseover mouseout', 'tbody tr', function (event) {
                    $(this).children().toggleClass("ui-state-hover",
                                                   event.type == 'mouseover');
                });

                $this.find("th").addClass("ui-state-default");
                $this.find("td").addClass("ui-widget-content");
                $this.find("tr:last-child").addClass("last-child");
            });
        };
    })(jQuery);



    /**
         * footer-reveal.js
         *
         * Licensed under the MIT license.
         * http://www.opensource.org/licenses/mit-license.php
         *
         * Copyright 2014 Iain Andrew
         * https://github.com/IainAndrew
         */
    $.fn.footerReveal = function (options) {
        var $this = $(this),
            $prev = $this.prev(),
            $win = $(window),
            defaults = $.extend({
                shadow: true,
                shadowOpacity: 0.8,
                zIndex: -100
            }, options),
            settings = $.extend(true, {}, defaults, options);
        if ($this.outerHeight() <= $win.outerHeight()) {
            $this.css({
                'z-index': defaults.zIndex,
                position: 'fixed',
                bottom: 0
            });
            if (defaults.shadow) {
                $prev.css({
                    '-moz-box-shadow': '0 20px 30px -20px rgba(0,0,0,' +
                        defaults.shadowOpacity + ')',
                    '-webkit-box-shadow': '0 20px 30px -20px rgba(0,0,0,' +
                        defaults.shadowOpacity + ')',
                    'box-shadow': '0 20px 30px -20px rgba(0,0,0,' +
                        defaults.shadowOpacity + ')'
                });
            }
            $win.on('load resize pageinit scroll', function () {
                $this.css({
                    'width': $prev.outerWidth()
                });
                $prev.css({
                    'margin-bottom': $this.outerHeight()
                });
            });
        }
        return this;
    };
    // make equal height
    $.fn.equalHeights = function () {
        var max = 0;
        return this.each(function () {
            var height = $(this).height();
            max = height > max ? height : max;
        }).height(max + 5);
    };
})(jQuery);
