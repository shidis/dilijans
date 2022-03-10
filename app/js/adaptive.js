(function ($) {
	$(function () {
		'use strict';
		$('.js-burger-trigger').on('click', function () {
			$('body').removeClass('mobile-info-open').toggleClass('mobile-navigation-open');
			$(this).toggleClass('burger_type_close');
			return false;
		});
				
		$('.js-info-trigger').on('click', function () {
			$('body').removeClass('mobile-navigation-open').toggleClass('mobile-info-open');
			$('.burger').removeClass('burger_type_close');
			return false;
		});
		
		$('.js-nav-trigger').on('click', function () {
			$(this).closest('.mb-nav__item, .mb-sub-nav__item').find(' > .mb-nav__subnav').toggleClass('mb-nav__subnav_state_open');
			$(this).toggleClass('mb-nav__trigger_state_open');
		});
		
		$('.mobile-header__basket .basket').on('click', function () {
			window.location.href = '/cart.html';
			return false;
		});
		
		var basketCount = $('.mobile-header__basket .basket i').length;
		
		if(basketCount) {
			$('.mobile-header__panel').addClass('mobile-header__panel_type_full');
		}
		
		$('.mp-toggle__trigger').on('click', function () {
			var text = $(this).text(),
			nText = $(this).attr('data-text');
			if(nText) {
				$(this).text(nText).attr('data-text', text);
			}
			$(this).toggleClass('mp-toggle__trigger_state_active').closest('.mp-toggle').find('.mp-toggle__content').toggleClass('mp-toggle__content_state_active');
			return false;
		});
	});
})(jQuery);