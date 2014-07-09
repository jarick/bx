(function ($, window, document, undefined) {
	var pluginName = "metisMenu",
	defaults = {
		toggle: true
	};
	function Plugin(element, options) {
		this.element = element;
		this.settings = $.extend({}, defaults, options);
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}
	Plugin.prototype = {
		init: function () {
			var $this = $(this.element),
			$toggle = this.settings.toggle;
			$this.find('li.active').has('ul').children('ul').addClass('collapse in');
			$this.find('li').not('.active').has('ul').children('ul').addClass('collapse');
			$this.find('li').has('ul').children('a').on('click', function (e) {
				e.preventDefault();
				$(this).parent('li').toggleClass('active').children('ul').collapse('toggle');
				if ($toggle) {
					$(this).parent('li').siblings().removeClass('active').children('ul.in').collapse('hide');
				}
			});
		}
	};
	$.fn[ pluginName ] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			}
		});
	};
})(jQuery, window, document); 

$(function() {
	$('#side-menu').metisMenu();
});

$(function() {
	$(window).bind("load resize", function() {
		width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
		if (width < 768) {
			$('div.sidebar-collapse').addClass('collapse')
		} else {
			$('div.sidebar-collapse').removeClass('collapse')
		}
	})
});

$(function(){
	$('.add-input').click(function(){
		$(this).parent().find('.input-box').append('<input type="text" value="" class="multi-input form-control" name="FORM[REGEX][]">');
	});
});