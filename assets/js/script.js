(function($)
{
    // HTML5 placeholder polyfill

    function initPlaceHolders()
    {
        if ( ! ('placeholder' in document.createElement('input')))
        {
            $('[type=password][placeholder]').attr('placeholder', $('[type=password][placeholder]').eq(0).attr('placeholder'));
            var items = '[placeholder]';
            $(items).each(function()
            {
                var $this = $(this);
                if (($this.val() == '') && ($this.attr('placeholder')))
                    $this.val($this.attr('placeholder')).addClass('placeholder');
                else if ($this.val() == $this.attr('placeholder'))
                    $this.addClass('placeholder');
            });
            $(document).on('focus', items, function()
            {
                var $this = $(this);
                if ($this.val() == $this.attr('placeholder'))
                    $this.val('').removeClass('placeholder');
            });
            $(document).on('blur', items, function()
            {
                var $this = $(this);
                if (($this.val() == '') && ($this.attr('placeholder')))
                    $this.val($this.attr('placeholder')).addClass('placeholder');
            });
            $('form').on('submit', function()
            {
                var $this = $(this);
                $this.find('[placeholder]').each(function()
                {
                    if ($this.val() == $this.attr('placeholder'))
                        $this.val('').removeClass('placeholder');
                });
            });
        }
    }
    


    // DOM ready

    $(function()
    {
        $('form').attr('autocomplete', 'off');
        $('select').mouseleave(function(event) { event.stopPropagation(); });
        if (typeof console === 'undefined') console = { log: function(){} };
        $('html').removeClass('nojs');
        initPlaceHolders();
    });
    


    // Loading complete

    $(window).on('load', function()
    {
        
    });
    
})(jQuery);
