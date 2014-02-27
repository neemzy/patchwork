(function () {
    'use strict';

    module.exports = function(selector) {
        var nodes = document.querySelectorAll(selector);

        try {
            return [].slice.call(nodes);
        } catch (e) {
            var elements = [];

            for (var i = 0, n = nodes.length; i < n; i++) {
                elements.push(nodes[i]);
            }

            return elements;
        }
    };
})();