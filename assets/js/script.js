(function($) {
	// Sliders (part 1)
	var busy = false, sObj, sDots, toMove, slideWidth, slideNb, slideDelay = 5000, isAuto, autoSlide, currentSlide = 1;
	function slideIt(slideToGo)
    {
		if (!busy)
        {
			busy = true;
			if (slideToGo == null) slideToGo = true;
			if (isNaN(parseInt(slideToGo))) {
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
	
	function slideItForever()
    {
		if (toMove.children().length > 1)
        {
			toMove.animate({ 'left': '-='+slideWidth+'px' }, 250, 'linear', function()
            {
				if (((-1) * (parseInt(toMove.css('left')))) >= (toMove.width() - slideWidth))
                {
					toMove.children('li:last-child').after(toMove.children('li:first-child'));
					toMove.css('left', function() { return (parseInt(toMove.css('left')) + slideWidth)+'px'; });
				}
			});
		}
	}
	
	$(function()
    {
		// Sliders (part 2)
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
	});
	
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
