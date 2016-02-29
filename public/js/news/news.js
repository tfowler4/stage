// news model event binder
var NewsEventBinder = function() {
    var stopClickRecent    = false;
    var stopClickMedia     = false;
    var stopClickRankPanel = false;
    var userClicked = false;
    var autoSliding = true;

    var numOfMediaItems;
    var mediaSlideDelay;
    var mediaSlideWidth;
    var maxMediaPaneSize;
    var mediaAutoSliderId;
    var mediaSliderPosition;

    // side rankings system click to display different dungeons
    $(document).on('click touchstart', '.side-ranking-header.clickable', function() {
        var slideDelay      = 500;
        var blockRankHeight = '';

        if( stopClickRankPanel ) { return; }

        var paneTitleId = $(this).prop('id').replace('dungeon-rankings-clicker-', '');
        var currentPane = $('#dungeon-rankings-wrapper-' + paneTitleId);

        if ( currentPane.css('display') == 'none' ) {
            stopClickRankPanel = true;
            
            var activePane = $(document).find('.active-dungeon');
            activePane.slideToggle(slideDelay, 'linear', function() {
                activePane.removeClass('active-dungeon');

                currentPane.slideToggle(slideDelay, 'linear', function() {
                    stopClickRankPanel = false;
                    currentPane.addClass('active-dungeon');
                });
            });
        }
    });

    // side rankings system click to display different ranking systems data
    $(document).on('click touchstart', 'button.clickable', function() {
        if( stopClickRankPanel ) { return; }

        var systemId = $(this).prop('id').replace('system-selector-', '');
        var numOfTables = $(document).find('.side-tables').length;

        if ( !$(this).hasClass('highlight') && numOfTables > 0 ) {
            stopClickRankPanel = true;

            $(this).parent().children('.highlight').removeClass('highlight');
            $(this).addClass('highlight');

            hideAndShowSideRankings(this, 'side-rankings-details', systemId, 300, false);
            hideAndShowSideRankings(this, 'side-rankings-details-small', systemId, 300, true);
        }
    });
    var hideAndShowSideRankings = function(me, detailsClass, systemId, delay, enableClick) {
        var identifier    = '.' + detailsClass + '.active-rank';
        var newIdentifier = '.' + systemId + '.' + detailsClass + '.hidden';
        me = $(me).parent().parent();

        $(me).parent().find(identifier).slideToggle(delay, 'linear', function() {
            $(this).addClass('hidden');
            $(this).removeClass('active-rank');
        });

        $(me).parent().find(newIdentifier).each(function() {
            $(this).addClass('active-rank');
            $(this).removeClass('hidden');
            $(this).css('display', 'block');
        });

        if ( enableClick ) {
            stopClickRankPanel = false;
        }
    };

    // media viewer buttons click to scroll through different video/streams
    $(document).on('click touchstart', '.scroll-button-media', function() {
        if( stopClickMedia || numOfMediaItems <= 1) { return; }

        if ( !userClicked ) {
            userClicked = true;
            autoSliding = false;

            if ( mediaAutoSliderId ) {
                clearInterval(mediaAutoSliderId);
            }
        }

        mediaSliderPosition = $('#media-pane ul').css("left").replace("px", "");
        var direction;

        if ( $(this).hasClass('left') ) {
            direction           = 'left';
            mediaSliderPosition = parseInt(mediaSliderPosition) + mediaSlideWidth;
        }

        if ( $(this).hasClass('right') && (mediaSliderPosition-mediaSlideWidth) == (-1*maxMediaPaneSize) ) {
            direction           = 'right';
            mediaSliderPosition = 0;
        } else if ( $(this).hasClass('right') ) {
            direction           = 'right';
            mediaSliderPosition = parseInt(mediaSliderPosition) - mediaSlideWidth;
        }

        scrollMediaSlider(direction, mediaSliderPosition);
    });
    var scrollMediaSlider = function(direction, pos) {
        if ( (direction == 'left' && parseInt(pos) <= 0) 
             || (direction == 'right' && pos > (-1*maxMediaPaneSize) ) ) {
            stopClickMedia = true;

            var navigationNumber = -1 * (pos / mediaSlideWidth);
            $('.circle').not('.clickable.faded').addClass('clickable faded');

            $('.circle.'+navigationNumber).removeClass('clickable faded');

            // media overlay top bar logo
            $('.media-overlay-top img').fadeToggle(mediaSlideDelay).delay(mediaSlideDelay).fadeToggle(mediaSlideDelay);

            // media overlay bottom bar
            $('.media-overlay-bottom').slideToggle(mediaSlideDelay).delay(mediaSlideDelay).slideToggle(mediaSlideDelay);

            // image slider
            $('#media-pane ul').delay(mediaSlideDelay).animate({ left: pos }, mediaSlideDelay);

            // place guild logo back to its original place by fading out and resetting
            if ( $('.media-guild-logo').css('margin-right') == '5px' ) {
                $('.media-guild-logo').fadeToggle(mediaSlideDelay, function() {
                    $('.media-guild-logo').css('margin-right', '500px');
                    $('.media-guild-logo').css('display', 'block');

                    $('.media-guild-flag img, media-guild-logo img').css('margin-top', '0px');

                    $('.media-guild-flag img').css('margin-top', '-15px');
                })
            }

            // animate guild logo 
            $('.media-guild-logo').delay(mediaSlideDelay).animate({ 'margin-right': 5 }, mediaSlideDelay, function() {
                stopClickMedia = false;
            });
        }
    };

    // when page finishes loading
    $(window).load(function(){
        // when page loads, re-adjust guild logos on media overlay to be centered based on image height
        

        /*
        $('.media-guild-logo img').each(function() {
            var parentHeight = parseInt($(this).parent().parent().css('height').replace('px', ''));
            var height       = parseInt($(this).css('height').replace('px', ''));
            var marginTop    = -1 *(height - parentHeight) / 2;

            $(this).css('margin-top', marginTop +'px');
        });

        $('.media-guild-flag img').each(function() {
            var parentHeight = parseInt($(this).parent().parent().css('height').replace('px', ''));
            var height       = parseInt($(this).css('height').replace('px', ''));
            var marginTop    = -1 *(height - parentHeight) / 2;

            $(this).css('margin-top', '-15px');
        });

        numOfMediaItems     = $("#media-pane ul li").length;

        if ( numOfMediaItems == 0 ) { return; }

        mediaSlideDelay     = 350;
        mediaSlideWidth     = 900;
        maxMediaPaneSize    = numOfMediaItems * mediaSlideWidth;
        mediaSliderPosition = $('#media-pane ul').css("left").replace("px", "");

        // automatically move slider to right until user clicks
        if ( !userClicked && autoSliding && numOfMediaItems > 1 ) {
            mediaAutoSliderId = setInterval(function(){
                if ( (mediaSliderPosition-mediaSlideWidth) == (-1*maxMediaPaneSize) ) {
                    mediaSliderPosition = 0;
                } else {
                    mediaSliderPosition = parseInt(mediaSliderPosition) - mediaSlideWidth;
                }

                scrollMediaSlider('right', mediaSliderPosition);
            }, 1000*10 );
        }
        */
    });
};