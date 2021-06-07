'use strict';

export default class Main{
	constructor(){

		$('body').on('click', '[data-seo-control]', function(){
			$(this).closest('[data-seo-text]').addClass('_active');
		});

		$('body').on('click', '[data-open-popup-form]', function(){
			$('.popup_wrap').addClass('_active');
			ym(74721805,'reachGoal','click_podberite_mne_zal');
			//gtag('event', 'header_button');
		});

		$('body').on('click', '[data-close-popup]', function(){
			$('.popup_wrap').removeClass('_active');
		});

		$('.header_burger').on('click', function(){
			$('.header_menu').toggleClass('_active');
			$('.header_burger').toggleClass('_active');
			// $('.city_mobile').toggleClass('_active');
		});

		$('body').on('click', '.city', function(){
			
			let cityList = $('.city_select_search_wrapper._hide')[0];
			
			if(cityList!=undefined){
				
				$('.city_select_search_wrapper').removeClass('_hide');
				$('[data-city-dropdown]').addClass('_active');
				$('.header_menu').toggleClass('_active');
				$('.header_burger').toggleClass('_active');
			}
			else {
				
				$('.city_select_search_wrapper').addClass('_hide');
				$('[data-city-dropdown]').removeClass('_active');
			}
		});

		$('.back_to_header_menu').on('click', function(){
			$('.city_select_search_wrapper').addClass('_hide');
			$('[data-city-dropdown]').removeClass('_active');
			$('.header_menu').toggleClass('_active');
			$('.header_burger').toggleClass('_active');

		});

		$(document).on('click', function(e) {
			if (!$(e.target).closest(".city_select_wrapper").length && !$(e.target).closest(".city").length) {
				$('.city_select_search_wrapper').addClass('_hide'); // скрываем его
				$('[data-city-dropdown]').removeClass('_active');
			}
			e.stopPropagation();
		});

		$('[data-layout-phone]').on('click', function(){
			ym(74721805,'reachGoal','click_phone_call_centra');
		});
	}
}