'use strict';
import Swiper, { Navigation, Pagination } from 'swiper';

export default class Post {
	constructor() {
		var self = this;
		this.swipers_gal = new Array();
		this.swipers_rest = new Array();

		/* $('.post_gallery_wrap').each(function (iter, object) {
			let postGalleryThumbs = new Swiper($(this).find('.post-gallery-thumbs'), {
				spaceBetween: 5,
				slidesPerView: 7,
				slidesPerColumn: 1,
				freeMode: true,
				watchSlidesVisibility: true,
				watchSlidesProgress: true,

				breakpoints: {
					1440: {
						slidesPerView: 5,
					},

					767: {
						slidesPerView: 4,
					}
				}
			});
			let postGalleryTop = new Swiper($(this).find('.post-gallery-top'), {
				spaceBetween: 0,
				thumbs: {
					swiper: postGalleryThumbs
				}
			});

			self.swipers_gal.push({
				postGalleryThumbs,
				postGalleryTop
			});
		});

		$('[data-adv-gallery-wrapper]').each(function (iter, object) {
			console.log('hi');
			let postAdv = new Swiper($(this).find('.listing_slider'), {
				spaceBetween: 0,
				slidesPerView: 1,
				navigation: {
					nextEl: '._listing_next',
					prevEl: '._listing_prev',
				},
			});

			self.swipers_rest.push({
				postAdv
			});
		}); */

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
}