(function($)
{
    // HTML5 placeholder polyfill
    function initPlaceHolders()
    {
        if (!('placeholder' in document.createElement('input'))) {
            $('input[type=password][placeholder]').attr('placeholder', $('input[type=password][placeholder]').eq(0).attr('placeholder'));
            var items = $('[placeholder]');
            items.each(function()
            {
                if (($(this).val() == '') && ($(this).attr('placeholder')))
                    $(this).val($(this).attr('placeholder')).addClass('placeholder');
                else if ($(this).val() == $(this).attr('placeholder'))
                    $(this).addClass('placeholder');
            });
            items.live('focus', function()
            {
                if ($(this).val() == $(this).attr('placeholder'))
                    $(this).val('').removeClass('placeholder');
            });
            items.live('blur', function()
            {
                if (($(this).val() == '') && ($(this).attr('placeholder')))
                    $(this).val($(this).attr('placeholder')).addClass('placeholder');
            });
            $('form').bind('submit', function()
            {
                $(this).find('[placeholder]').each(function()
                {
                    if ($(this).val() == $(this).attr('placeholder'))
                        $(this).val('').removeClass('placeholder');
                });
            });
        }
    }
    
    // jQuery outerHTML
    $.fn.outer = function() { return $($('<div></div>').html(this.clone())).html(); }

    // Slider (part 1)
    var busy = false, sObj, sDots, toMove, slideWidth, slideNb, slideDelay = 5000, isAuto, autoSlide, currentSlide = 1;
    function slideIt(slideToGo)
    {
        if (!busy)
        {
            busy = true;
            if (slideToGo == null) slideToGo = true;
            if (isNaN(parseInt(slideToGo)))
            {
                var nextMove = '-='+slideWidth;
                if (slideToGo)
                    currentSlide++;
                else
                {
                    nextMove = '+='+slideWidth;
                    currentSlide--;
                    if (currentSlide < 1)
                    {
                        currentSlide = slideNb;
                        nextMove = ((-1) * (currentSlide - 1) * slideWidth);
                    }
                }
            }
            else
            {
                nextMove = ((-1) * slideToGo * slideWidth);
                currentSlide = (slideToGo + 1);
            }
            if ((currentSlide > slideNb) || (currentSlide < 1))
            {
                nextMove = '0';
                currentSlide = 1;
            }
            toMove.animate({ 'left': nextMove+'px' }, 'linear', function()
            {
                busy = false;
                sDots.removeClass('current').eq(currentSlide - 1).addClass('current');
                if (isAuto)
                {
                    clearInterval(autoSlide);
                    autoSlide = setInterval('slideIt(true)', slideDelay);
                }
            });
        }
    }
    


    // DOM ready
    $(function()
    {
        if ($.browser.mozilla) $('form').attr('autocomplete', 'off');
        $('select').mouseleave(function(event) { event.stopPropagation(); });
        if (typeof console === 'undefined') console = { log: function(){} };
        initPlaceHolders();

        // Custom rel attribute handling
        $('[data-rel]').each(function()
        {
            var that = $(this);
            that.attr('rel', function(i, val) { return ((typeof val === 'undefined') ? '' : val+' ')+that.data('rel'); }).removeAttr('data-rel');
        });

        // Slider (part 2)
        sObj = $('#slider');
        sDots = sObj.find('.dot');
        isAuto = sObj.hasClass('auto');
        if (sObj.length == 1)
        {
            toMove = sObj.find('ul').eq(0);
            slideWidth = sObj.width();
            slideNb = toMove.children().length;
            sObj.find('.arrow').on('click', function() { slideIt($(this).is('.arrow + .arrow')); });
            sDots.on('click', function() { slideIt(sDots.index($(this))); }).eq(0).addClass('current');
            if (isAuto)
                autoSlide = setInterval('slideIt(true)', slideDelay);
        }

        // lte IE 8
        if ($('body').is('ie6, ie7'))
        {
            $('.gt').prepend('<span>&gt; </span>');
            $('.required').append('<span> *</span>');
        }
    });
    


    // Loading complete
    $(window).load(function()
    {
        var obf, obw, ftr = $('body > footer');
        if (ftr.length > 0)
        {
            obf = (ftr.offset().top + ftr.outerHeight());
            obw = $(window).height();
            if (obf < obw)
                ftr.height(ftr.height() + (obw - obf));
        }
    });
})(jQuery);