(function($)
{
    function initPlaceHolders()
    {
        if (! ('placeholder' in document.createElement('input'))) {
            $('[type=password][placeholder]').attr('placeholder', $('[type=password][placeholder]').eq(0).attr('placeholder'));
            var items = '[placeholder]', $doc = $(document);

            $(items).each(function() {
                var $this = $(this);

                if (($this.val() == '') && ($this.attr('placeholder'))) {
                    $this.val($this.attr('placeholder')).addClass('placeholder');
                } else if ($this.val() == $this.attr('placeholder')) {
                    $this.addClass('placeholder');
                }
            });

            $doc.on('focus', items, function() {
                var $this = $(this);

                if ($this.val() == $this.attr('placeholder')) {
                    $this.val('').removeClass('placeholder');
                }
            });

            $doc.on('blur', items, function() {
                var $this = $(this);

                if (($this.val() == '') && ($this.attr('placeholder'))) {
                    $this.val($this.attr('placeholder')).addClass('placeholder');
                }
            });

            $('form').on('submit', function() {
                var $this = $(this);

                $this.find('[placeholder]').each(function() {
                    if ($this.val() == $this.attr('placeholder')) {
                        $this.val('').removeClass('placeholder');
                    }
                });
            });
        }
    }
    


    $('form').attr('autocomplete', 'off');

    $('select').mouseleave(function(event) {
        event.stopPropagation();
    });

    if (typeof console === 'undefined') {
        console = { log: function() {} };
    }
    
    $('html').removeClass('nojs');
    initPlaceHolders();
})
(jQuery);