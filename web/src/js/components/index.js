'use strict';
import Filter from './filter';
import Swiper, { Navigation, Pagination } from 'swiper';

export default class Index {
	constructor($block) {
		var self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function () {
			self.redirectToListing();
		});

		$('.mobile_button_text').on('click', () => {
			this.openText();
		});

		$('.mobil_but_off').on('click', () => {
			this.closeText();
		});

		// this.splitInTwoRows();

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
	}

	redirectToListing() {
		let self = this;

		if (!self.filter.requiredDataCheck()) {
			self.filter.blockSubmitButton();
			return;
		}

		this.filter.filterMainSubmit();
		this.filter.promise.then(
			response => {
				//ym(64598434,'reachGoal','filter');
				//gtag('event', 'filter');
				window.location.href = response;
			}
		);
	}

	openText() {
		document.getElementById('mobile_but_all').style.height = "auto";
		document.getElementById('mobile_but_all').style.overflow = "visible";
		document.getElementById('butt_on').style.display = "none";
		document.getElementById('butt_off').style.display = "block";
	}

	closeText() {
		document.getElementById('mobile_but_all').style.height = "120px";
		document.getElementById('mobile_but_all').style.overflow = "hidden";
		document.getElementsByClassName('mobile_button_text')[0].style.display = "";
		document.getElementById('butt_off').style.display = "none";
	}

	// splitInTwoRows() {
	// 	if (this.mobileMode) {
	// 		let tags = $('.tag');
	// 		let tagsLength = tags.length;
	// 		console.log(tagsLength);
	// 		if (tagsLength <= 2) {
	// 			console.log(32423);
	// 			$('.tags__arrow').css('display', 'none');
	// 		} else {
	// 			let halfLength = Math.ceil(tagsLength / 2); // Определяем половину количества тегов
	// 			// Разделяем теги на две части и оборачиваем их в блоки tags_row
	// 			tags.slice(0, halfLength).wrapAll('<div class="tags__row"></div>');
	// 			tags.slice(halfLength).wrapAll('<div class="tags__row"></div>');
	// 		}
	// 	}
	// }

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	}
}