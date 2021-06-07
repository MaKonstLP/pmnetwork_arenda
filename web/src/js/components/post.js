'use strict';
import Swiper from 'swiper';

export default class Post
{
	constructor()
  {
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

		$('.post_item_gallery_wrap').each(function(iter,object){
			let postGalleryThumbs = new Swiper($(this).find('.post-item-gallery-thumbs'), {
		        spaceBetween: 5,
		        slidesPerView: 4,
		        slidesPerColumn: 1,
		        freeMode: true,
		        watchSlidesVisibility: true,
		        watchSlidesProgress: true,

		        breakpoints: {
		            1440: {
		              	slidesPerView: 3,
		            },

		            767: {
		              	slidesPerView: 2,
		            }
		        }
		     });
			let postGalleryTop = new Swiper($(this).find('.post-item-gallery-top'), {
				spaceBetween: 0,
				thumbs: {
					swiper: postGalleryThumbs
				}
			});

			self.swipers_rest.push({
				postGalleryThumbs,
				postGalleryTop
			});
		});
  }
}