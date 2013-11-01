requirejs.config({
    paths: {
        'jquery.min': 'http://code.jquery.com/jquery.min',
        'vendor/core': '/vendor/neemzy/patchwork-core/assets/js'
    }
});

requirejs(
    ['jquery.min', 'vendor/core/jquery.placeholders'],

    function() {
        $('form').attr('autocomplete', 'off');

        $('select').on('mouseleave', function(e) {
            e.stopPropagation();
        });
        
        $('html').removeClass('nojs').placeholders();
    }
);