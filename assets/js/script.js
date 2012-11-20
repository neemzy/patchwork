(function($)
{
    // HTML5 placeholder polyfill
    function initPlaceHolders()
    {
        if ( ! ('placeholder' in document.createElement('input')))
        {
            $('[type=password][placeholder]').attr('placeholder', $('[type=password][placeholder]').eq(0).attr('placeholder'));
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
    


    // DOM ready
    $(function()
    {
        if ($.browser.mozilla) $('form').attr('autocomplete', 'off');
        $('select').mouseleave(function(event) { event.stopPropagation(); });
        if (typeof console === 'undefined') console = { log: function(){} };
        initPlaceHolders();
    });
    


    // Loading complete
    $(window).load(function()
    {
        
    });
})(jQuery);