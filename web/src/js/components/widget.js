'use strict';
import Swiper, { Navigation, Pagination } from 'swiper';

export default class Widget {
	constructor() {
		self = this;
		this.swiperArr = [];

		if ($(window).width() <= 1920) {
			$('[data-widget-wrapper]').each(function () {
				self.initSwiper($(this).find('[data-listing-wrapper]'));
			});
		}

		$(window).on('resize', function () {
			console.log(self.swiperArr.length);
			if ($(window).width() <= 1920) {
				if (self.swiperArr.length == 0) {
					$('[data-widget-wrapper]').each(function () {
						self.initSwiper($(this).find('[data-listing-wrapper]'));
					});
				}
			}
			else {
				$.each(self.swiperArr, function () {
					this.destroy(true, true);
				});
				self.swiperArr = [];
			}
		});

		let showAllImages = false;
		/* var galleryList = new Swiper('.listing_slider', {
			modules: [Navigation, Pagination],
			spaceBetween: 0,
			slidesPerView: 1,
			navigation: {
				nextEl: '._listing_next',
				prevEl: '._listing_prev',
			},
			pagination: {
				el: '.listing_slider_pagination',
			},
			on: {
				// afterInit: () => {
				// 	this.slideTo(0);//убирает баг с тем, что после инициализации стрелки скрыты и имеют класс "swiper-button-lock"
				// },
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
		}); */

		// $('.listing_slider').find('._listing_next').removeClass('swiper-button-disabled');
	}

	initSwiper($container) {
		/* let swiper = new Swiper($container, {
			modules: [Navigation, Pagination],
			slidesPerView: 4,
			spaceBetween: 16,
			watchOverflow: true,
			navigation: {
				nextEl: '._next',
				prevEl: '._prev',
			},
			pagination: {
				el: '.listing_slider_pagination',
			},

			on: {
				// afterInit: () => {
				// 	this.slideTo(0);//убирает баг с тем, что после инициализации стрелки скрыты и имеют класс "swiper-button-lock"
				// },
			},
			breakpoints: {
				1440: {
					slidesPerView: 3,
				},
				767: {
					slidesPerView: 1,
				}
			}
		});

		$('.listing_slider').find('._listing_next').removeClass('swiper-button-disabled');


		let swiper_var = $container.swiper;
		this.swiperArr.push(swiper); */
	}
}