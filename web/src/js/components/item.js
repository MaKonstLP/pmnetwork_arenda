'use strict';
import Swiper, { Navigation, Pagination, Thumbs } from 'swiper';
import 'slick-carousel';
import * as Lightbox from '../../../node_modules/lightbox2/dist/js/lightbox.js';

export default class Item {
	constructor($item) {
		var self = this;
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		$('[data-action="show_phone"]').on('click', function () {
			$('.object_book_hidden').addClass('_active');
			ym(74721805, 'reachGoal', 'show_phone_zala');
			// dataLayer.push({'event': 'show_phone_zala'});
			gtag('event', 'show_phone_zala', { 'event_category': 'click', 'event_action': 'show_phone_zala' });
			// if($(this).data('commission')){
			//     ym(64598434,'reachGoal','show_phone_comm');
			//     gtag('event', 'show_phone_comm');
			// }

			// ==== Gorko-calltracking ====
			let phone = $(this).closest('.object_book_hidden').find('.object_real_phone').text();
			// self.sendCalltracking(phone);
			if (typeof ym === 'function') {
				self.sendCalltracking(phone);
				ym(74721805, 'reachGoal', 'click_pozvonit_listing');
			} else {
				setTimeout(function () {
					self.sendCalltracking(phone);
					ym(74721805, 'reachGoal', 'click_pozvonit_listing');
				}, 3000);
			}
		});

		Lightbox.option({
			'resizeDuration': 300,
			'fadeDuration': 100,
			'imageFadeDuration': 100,
		})

		$('[data-title-address]').on('click', function () {
			let map_offset_top = $('#map').offset().top;
			let map_height = $('#map').height();
			let header_height = $('header').height();
			let window_height = $(window).height();
			let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 2;
			$('html,body').animate({ scrollTop: scroll_length }, 400);
		});

		$('[data-book-button]').on('click', function () {
			let form = $('[data-type="item"]').closest('.form_wrapper');
			let form_offset_top = form.offset().top;
			let header_height = $('header').height();
			let scroll_length = form_offset_top - header_height - 50;
			$('html,body').animate({ scrollTop: scroll_length }, 400);
			ym(64598434, 'reachGoal', 'scroll_form');
			gtag('event', 'scroll_form');
			console.log('scroll_form');
		});

		$('[data-item-mobile-popup]').on('click', function () {
			$('.popup_wrap__item-mobile').addClass('_active');
			$('body').addClass('_popup_mode');
			$('.header_menu').removeClass('_active');
			$('.header_burger').removeClass('_active');
		});

		$('[data-item-mobile-call]').on('click', function () {
			ym(74721805, 'reachGoal', 'pozvonit');

			// ==== Gorko-calltracking ====
			let phone = $(this).attr('href');
			// self.sendCalltracking(phone);
			if (typeof ym === 'function') {
				self.sendCalltracking(phone);
				ym(74721805, 'reachGoal', 'click_pozvonit_listing');
			} else {
				setTimeout(function () {
					self.sendCalltracking(phone);
					ym(74721805, 'reachGoal', 'click_pozvonit_listing');
				}, 3000);
			}
		})

		var galleryThumbs = new Swiper('.item_thumb_slider', {
			modules: [Navigation, Thumbs],
			spaceBetween: 5,
			slidesPerView: 5,
			freeMode: true,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
			watchOverflow: true,
			breakpoints: {
				767: {
					slidesPerView: 0,
					spaceBetween: 5
				}
			}
		});
		var galleryTop = new Swiper('.item_top_slider', {
			modules: [Navigation, Thumbs],
			spaceBetween: 0,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			thumbs: {
				swiper: galleryThumbs
			},
			breakpoints: {
				767: {
					slidesPerView: 1,
					spaceBetween: 5
				}
			}
		});

		let galleryOtherRoooms = new Swiper('.item_other_slider', {
			modules: [Navigation],
			slidesPerView: 1,
			spaceBetween: 16,
			watchOverflow: true,
			navigation: {
				nextEl: '._next',
				prevEl: '._prev',
			},
			breakpoints: {
				// when window width is >= 767px
				767: {
					slidesPerView: 3,
				},
				// when window width is >= 1440px
				1440: {
					slidesPerView: 4,
				},
			}
		});

		/* 	let showAllImages = false;
			var galleryList = new Swiper('.listing_slider', {
				modules: [Navigation, Pagination],
				spaceBetween: 1,
				slidesPerView: 1,
				navigation: {
					nextEl: '._listing_next',
					prevEl: '._listing_prev',
				},
				pagination: {
					el: '.listing_slider_pagination',
				},
				on: {
					afterInit: () => {
						this.slideTo(0);//убирает баг с тем, что после инициализации стрелки скрыты и имеют класс "swiper-button-lock"
					},
					slideChange: function () {
						// показать все картинки реста
						if (!showAllImages) {
							showAllImages = true;
	
							$('.listing_slider').find('.item-img').show();
							let sliderImages = $('.listing_slider').find('.item-img img');
							sliderImages.each(function () {
								let imageSrc = $(this).data('src');
								$(this).attr('src', imageSrc);
							})
						}
					},
				},
			});
			$('.listing_slider').find('._listing_next').removeClass('swiper-button-disabled'); */
		let showAllImages = false;
		document.querySelectorAll('.object_gallery').forEach(n => {

			var listinImagesSlider = new Swiper(n.querySelector('.listing_slider'), {
				modules: [Navigation, Pagination],
				spaceBetween: 1,
				slidesPerView: 1,
				navigation: {
					nextEl: '._listing_next',
					prevEl: '._listing_prev',
				},
				pagination: {
					el: '.listing_slider_pagination',
				},
				on: {
					slideChange: function () {
						// показать все картинки реста
						if (!showAllImages) {
							showAllImages = true;

							$('.listing_slider').find('.item-img').show();
							let sliderImages = $('.listing_slider').find('.item-img img');
							sliderImages.each(function () {
								let imageSrc = $(this).data('src');
								$(this).attr('src', imageSrc);
							})
						}
					},
				},
			});
		});
		// $('.listing_slider').find('._listing_next').removeClass('swiper-button-disabled');

		if ($('[data-seo-description] p').length > 0 && $('[data-seo-description] p')[0].offsetHeight > 68) {
			var $seoText = $('[data-seo-description] ._seo_text');
			$seoText.addClass('_collapse');
			$seoText.siblings('.collapse_button').removeClass('_hidden');

			$('.collapse_button').on('click', function () {
				if ($seoText.hasClass('_collapse')) {
					$seoText.removeClass('_collapse');
					$(this).text('Скрыть текст')
				} else {
					$seoText.addClass('_collapse');
					$(this).text('Подробнее о площадке')
				}
			});
		}

		$('.object_real_phone').on('click', function () {
			ym(74721805, 'reachGoal', 'click_phone_zala');
			// dataLayer.push({'event': 'phone_zala'});
			gtag('event', 'phone_zala', { 'event_category': 'click', 'event_action': 'phone_zala' });

			// ==== Gorko-calltracking ====
			let phone = $(this).text();
			// self.sendCalltracking(phone);
			if (typeof ym === 'function') {
				self.sendCalltracking(phone);
				ym(74721805, 'reachGoal', 'click_pozvonit_listing');
			} else {
				setTimeout(function () {
					self.sendCalltracking(phone);
					ym(74721805, 'reachGoal', 'click_pozvonit_listing');
				}, 3000);
			}
		});

		//вызов галерии lightbox при клике по кнопке "Посмотреть фотографии"
		$('[data-pseudo-lightbox]').on('click', function (e) {
			e.preventDefault();
			$('#first_img').click();
		})

		//скролл по якорю
		$('a._scroll-to').on('click', function (e) {
			e.preventDefault();
			let target = e.target;
			scrollToElement(target);
		});

		function scrollToElement(elem) {
			let anchor = $(elem).attr('href');
			$('html, body').stop().animate({
				scrollTop: $(anchor).offset().top - 120
			}, 400);
		}
	}

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	};

	sendCalltracking(phone) {
		let clientId = '';
		ga.getAll().forEach((tracker) => {
			clientId = tracker.get('clientId');
		})

		let yaClientId = '';
		ym(74721805, 'getClientID', function (id) {
			yaClientId = id;
		});

		const data = new FormData();

		if (this.mobileMode) {
			data.append('isMobile', 1);
		}

		data.append('phone', phone);
		data.append('clientId', clientId);
		data.append('yaClientId', yaClientId);

		$.ajax({
			type: 'post',
			url: '/ajax/send-calltracking/',
			data: data,
			processData: false,
			contentType: false,
			success: function (response) {
				// response = $.parseJSON(response);
				// response = JSON.parse(response);
				// self.resolve(response);
				console.log('calltracking sent');
			},
			error: function (response) {
				console.log('calltracking ERROR');
			}
		});

		if ($('[data-premium-rest]').length > 0) {
			let data = new FormData();
			data.append('gorko_id', $('[data-premium-rest]').data('premium-rest'));
			data.append('channel', $('[data-channel-id]').data('channel-id'));
			fetch('/premium/premium-click/', {
				method: 'POST',
				body: data,
			})
				.then((response) => response.json())
				.then((data) => {
					console.log(data);
				})
				.catch((error) => {
					console.error('Error:', error);
				});
		}
	}
}
