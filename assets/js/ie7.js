(function($){
	$.fn.getStyleObject = function(){
		var dom = this.get(0);
		var style;
		var returns = {};
		if(window.getComputedStyle){
			var camelize = function(a,b){
				return b.toUpperCase();
			}
			style = window.getComputedStyle(dom, null);
			for(var i=0;i<style.length;i++){
				var prop = style[i];
				var camel = prop.replace(/\-([a-z])/g, camelize);
				var val = style.getPropertyValue(prop);
				returns[camel] = val;
			}
			return returns;
		}
		if(dom.currentStyle){
			style = dom.currentStyle;
			for(var prop in style){
				returns[prop] = style[prop];
			}
			return returns;
		}
		return this.css();
	}
	
	$(function() {
		$('hr:visible').each(function() {
			var div = $('<div></div>');
			div.css($(this).getStyleObject());
			div.css('display', 'block');
			div.attr('class', $(this).attr('class'));
			$(this).after(div).remove();
		});
		$('.gt').prepend('<span>&gt; </span>');
        $('.required').append('<span> *</span>');
	});
})(jQuery);
