'use strict';
import Cookies from 'js-cookie';

export default class Main {
	constructor() {
		let self = this;
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		// === записываем в куки данные для отправки Calltracking в БД горько START ===
		//запись в куки только внешнего реферера
		let pageReferrer = '';
		//проверяем что это внешний реферер, а не переход внутри страниц сайта
		// if (document.referrer.indexOf(window.location.origin) != -1) { //в этом случае поддомены (например samara.arendazala.net) тоже считаются внешним реферером
		if (document.referrer.indexOf('arendazala.net') == -1) { // отсекаем так же поддомен, как внешний реферер
			console.log("from external site");
			if (document.referrer) {
				pageReferrer = document.referrer;
			}
			if (Cookies.get('a_ref_0')) {
				Cookies.set('a_ref_1', pageReferrer, { expires: 365 });
			} else {
				Cookies.set('a_ref_0', pageReferrer, { expires: 365 });
			}
		}

		//запись в куки utm_details
		let currentUrl = '';
		if (window.location.href) {
			currentUrl = window.location.href;
		}
		let patternUtm = RegExp('utm_details=([^\&]*)', 'g');
		let utmExist = patternUtm.exec(currentUrl);
		let utm = {};
		if (utmExist) {
			let rows = utmExist[1].split('|');

			for (let i = 0; i < rows.length; i++) {
				let a = rows[i].split(':');
				utm[a[0]] = a[1];
			}
		}

		if (Object.keys(utm).length != 0) {
			let utmJson = JSON.stringify(utm);
			if (Cookies.get('a_utm_0') && Cookies.get('a_utm_0') != '{}') {
				Cookies.set('a_utm_1', utmJson, { expires: 365 });
			} else {
				Cookies.set('a_utm_0', utmJson, { expires: 365 });
			}
		}
		// === записываем в куки данные для отправки Calltracking в БД горько END ===

		$('body').on('click', '[data-seo-control]', function () {
			$(this).closest('[data-seo-text]').addClass('_active');
		});

		$('body').on('click', '[data-open-popup-form]', function () {
			$('.popup_wrap').addClass('_active');
			// $('[data-popup-main]').addClass('_active');
			$('body').addClass('_popup_mode');
			$('.header_menu').removeClass('_active');
			$('.header_burger').removeClass('_active');
			ym(74721805, 'reachGoal', 'click_podberite_mne_zal');
			//gtag('event', 'header_button');

			$('.popup_wrap').find('.form_block').attr('data-type', 'popup');
			$('.popup_wrap').find('.form_block').attr('data-rest-name', '');
			$('.popup_wrap').find('.form_block').attr('data-rest-url', '');
			$('.popup_wrap').find('.form_block').attr('data-premium-listing-form-id', 0);
			$('.popup_wrap').find('.form_title_main').html('Помочь с&nbsp;выбором&nbsp;зала?');
			$('.popup_wrap').find('.form_title_text').text('');
		});

		//открытие формы на листинге при клике по кнопке забронировать
		$('body').on('click', '[data-listing-book]', function () {
			let venue_id = $(this).data('listing-book');
			let restName = $(this).closest('.item-block').find('.item_name span').text();
			let restUrl = $(this).closest('.item-block').find('.name a').attr('href');
			if ($(this).closest('.item-block').data('premium-listing-id')) {
				var premium_id = $(this).closest('.item-block').data('premium-listing-id');
			}
			else {
				var premium_id = 0;
			}
			$('.popup_wrap').addClass('_active');
			$('body').addClass('_popup_mode');
			$('.header_menu').removeClass('_active');
			$('.header_burger').removeClass('_active');

			$('.popup_wrap').find('.form_title_main').html('Бесплатно забронировать <br class="mobile_only"> это место');
			$('.popup_wrap').find('.form_title_text').text('Заполните форму и мы свяжемся с вами в ближайшее время.');
			$('.popup_wrap').find('.form_block').attr('data-type', 'listing-book');
			$('.popup_wrap').find('.form_block').attr('data-premium-listing-form-id', premium_id);
			$('.popup_wrap').find('.form_block').attr('data-rest-name', restName);
			$('.popup_wrap').find('.form_block').attr('data-rest-url', 'https://arendazala.net' + restUrl);
			$('.popup_wrap').find('[name="venue_id"]').val(venue_id);

			console.log($('.popup_wrap').find('[name="venue_id"]').val());

			ym(74721805, 'reachGoal', 'click_zabronirovat');
		});

		$('body').on('click', '[data-close-popup]', function () {
			$('.popup_wrap').removeClass('_active');
			$('.popup_wrap__item-mobile').removeClass('_active');
			$('.popup_wrap__item-review').removeClass('_active');
			$('.listing_feedback_form').removeClass('_active');
			$('.listing_feedback_form .comments_textarea').removeClass('_active');
			$('.listing_feedback_form_main').addClass('_active');
			$('body').removeClass('_popup_mode');
			if ($('[data-success]').hasClass('_active')) {
				$('[data-success]').removeClass('_active');
			}
		});

		$('.header_burger').on('click', function () {
			$('.header_menu').toggleClass('_active');
			$('.header_burger').toggleClass('_active');
			$('.city_select_search_wrapper').addClass('_hide');
			$('[data-city-dropdown]').removeClass('_active');
		});

		$('.header_menu_burger').on('click', function () {
			$('.header_menu').toggleClass('_active');
		});

		$('body').on('click', '.city', function () {

			let cityList = $('.city_select_search_wrapper._hide')[0];

			if (cityList != undefined) {

				$('.city_select_search_wrapper').removeClass('_hide');
				$('[data-city-dropdown]').addClass('_active');
				// $('.header_menu').toggleClass('_active');
				// $('.header_burger').toggleClass('_active');
			}
			else {

				$('.city_select_search_wrapper').addClass('_hide');
				$('[data-city-dropdown]').removeClass('_active');
			}
		});

		$('.back_to_header_menu').on('click', function () {
			$('.city_select_search_wrapper').addClass('_hide');
			$('[data-city-dropdown]').removeClass('_active');
			// $('.header_menu').toggleClass('_active');
			// $('.header_burger').toggleClass('_active');

		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest(".city_select_wrapper").length && !$(e.target).closest(".city").length) {
				$('.city_select_search_wrapper').addClass('_hide'); // скрываем его
				$('[data-city-dropdown]').removeClass('_active');

				if (!$(e.target).closest(".header_menu._active").length && !$(e.target).closest(".header_burger._active").length) {
					$('.header_menu').removeClass('_active');
					$('.header_burger').removeClass('_active');
				}
			}

			e.stopPropagation();
		});

		$('[data-layout-phone]').on('click', function () {
			ym(74721805, 'reachGoal', 'click_phone_call_centra');
			dataLayer.push({ 'event': 'phone_call_centra' });
		});

		//клик по кнопке "Позвонить" в листинге
		$('[data-listing-list]').on('click', '.item-info__btn-call', function () {
			// ==== Gorko-calltracking ====
			let phone = $(this).find('.item-info__phone').attr('href');
			if (typeof ym === 'function') {
				self.sendCalltracking(phone);
				ym(74721805, 'reachGoal', 'click_pozvonit_listing');
			} else {
				setTimeout(function () {
					self.sendCalltracking(phone);
					ym(74721805, 'reachGoal', 'click_pozvonit_listing');
				}, 3000);
			}

			if ($(this).closest('.item-block').data('premium-listing-id')) {
				let data = new FormData();
				data.append('gorko_id', $(this).closest('.item-block').data('premium-listing-id'));
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
		})

		//копирование номера телефона в буфер обмена
		if (!self.mobileMode) {
			$('.main_wrap').on('click', '[data-copy-phone]', function (e) {
				e.preventDefault();
				let $tempInput = $('<input>');
				$('body').append($tempInput);
				$tempInput.val($(this).text()).select();
				window.navigator.clipboard.writeText($tempInput.val());
				$tempInput.remove();

				//закрытие открытых попапов
				$('.phone-bufer').removeClass('_active');

				$(this).siblings('.phone-bufer').addClass('_active');
			})
		}
		//закрытие попапа с информацией о скопированном номере в буфер
		$('.main_wrap').on('click', '.phone-bufer__close', function () {
			$(this).closest('.phone-bufer').removeClass('_active');
		})

		//скролл по якорю
		$('.main_wrap').on('click', 'a.scroll-to', function (e) {
			e.preventDefault();
			let target = e.target;
			self.scrollToElement(target);
		});

		$('[data-listing-list]').on('click', '[data-need-help]', function (e) {
			ym(74721805, 'reachGoal', 'click_ne_nashel');
			e.preventDefault();
			let target = e.target;
			self.scrollToElement(target);
		})

		$('[data-listing-list]').on('click', '[data-feedback-button]', function () {
			$('.listing_feedback_form').addClass('_active');
		})


		if (self.getScrollWidth() > 1440) {
			$('.header_menu__item').hover(function () {
				$(this).addClass('_active');
			}, function () {
				$(this).removeClass('_active');
			});
		} else {
			$('.header_menu__item').on('click', (e) => {
				// e.preventDefault();
				e.target.classList.toggle('_active');
			})
		}


		var fired = false;

		window.addEventListener('click', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('scroll', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('mousemove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('touchmove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		function load_other() {
			setTimeout(function () {
				self.init();
			}, 100);
		}
	}

	init() {
		setTimeout(function () {
			(function (w, d, s, l, i) {
				w[l] = w[l] || []; w[l].push({
					'gtm.start':
						new Date().getTime(), event: 'gtm.js'
				}); var f = d.getElementsByTagName(s)[0],
					j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
						'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
			})(window, document, 'script', 'dataLayer', 'GTM-PQ92WXZ');
		}, 100);

		//клик по кнопке в виджете на поддомене челябинска (https://chelyabinsk.arendazala.net/) (кнопка находится в теневом дереве (#shadow-root))
		const subdomain = window.location.hostname.split('.')[0];
		if (subdomain.includes('chelyabinsk')) {
			const intervalId = setInterval(function () {
				//отслеживаем что виджет загрузился
				const element = $('pf-widget');

				if (element.length > 0) {
					// Элемент загружен, присваиваем его значение переменной
					const buttonWhatsApp = document.querySelector('body > pf-widget').shadowRoot.querySelector('pf-legacy').shadowRoot.querySelector('.pfModalContinueBtnWhatsapp')
					const buttonTelegram = document.querySelector('body > pf-widget').shadowRoot.querySelector('pf-legacy').shadowRoot.querySelector('.pfModalContinueBtnTelegram')
					buttonWhatsApp.addEventListener('click', function () {
						ym(74721805, 'reachGoal', 'widget_chelyabinsk_wa');
					});
					buttonTelegram.addEventListener('click', function () {
						ym(74721805, 'reachGoal', 'widget_chelyabinsk_tg');
					});
					clearInterval(intervalId); // Останавливаем периодическую проверку
				}
			}, 1000);
		}
	}

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	};

	scrollToElement(elem) {
		var anchor = $(elem).attr('href');
		$('html, body').stop().animate({
			scrollTop: $(anchor).offset().top - 60,
		}, 800);
	}

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
	}
}