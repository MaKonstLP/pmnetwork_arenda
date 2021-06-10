'use strict';
import Swiper from 'swiper';

export default class Post
{
	constructor()
  {
		var self = this;
		this.swipers_gal = new Array();
		this.swipers_rest = new Array();

    $('.post_gallery_wrap').each(function(iter,object){
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

		$('[data-adv-gallery-wrapper]').each(function(iter,object){
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
		});
  }
}