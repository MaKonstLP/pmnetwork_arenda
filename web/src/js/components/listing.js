'use strict';
import Filter from './filter';
import Swiper, { Navigation, Pagination } from 'swiper';
import YaMapAll from './map_all';
import 'slick-carousel';
import 'lazysizes';

export default class Listing {
	constructor($block) {
		self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function () {
			self.reloadListing();
		});

		//КЛИК ПО ПАГИНАЦИИ
		$('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function () {
			self.reloadListing($(this).data('page-id'));
			// self.getSwiper();

		});

		//КЛИК ПО КНОПКЕ "ПОКАЗАТЬ ЕЩЕ"
		$('body').on('click', '[data-loadmore] [data-listing-pagitem]', function (e) {
			e.preventDefault();
			self.loadMoreListing($(this).data('page-id'));
			// setTimeout(catalogSliderInit, 500);

			// self.reloadListing($(this).data('page-id'));
			// self.getSwiper();
		});

		/* let showAllImages = false;
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
		}); */
		let showAllImages = false;
		document.querySelectorAll('.object_gallery').forEach(n => {

			var listinImagesSlider = new Swiper(n.querySelector('.listing_slider'), {
				modules: [Navigation, Pagination],
				spaceBetween: 1,
				slidesPerView: 1,
				loop: true,
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

		$('div.btn_list._left').on('click', () => {
			this.viewListing('left');
		});

		$('div.btn_list._right').on('click', () => {
			this.viewListing('right');
		});

		$('body').on('click', '.address_map', () => {
			this.viewListing('right');
		});
	}

	initMainSlider() {
		let showAllImages = false;
		document.querySelectorAll('.object_gallery').forEach(n => {

			var listinImagesSlider = new Swiper(n.querySelector('.listing_slider'), {
				modules: [Navigation, Pagination],
				spaceBetween: 1,
				slidesPerView: 1,
				loop: true,
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
	}

	/* getSwiper() {
		let showAllImages = false;
		var galleryList = new Swiper('.listing_slider', {
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
		$('.listing_slider').find('._listing_next').removeClass('swiper-button-disabled');
	} */

	viewListing(id) {
		if (id == "left") {
			document.getElementById('left').className += " active";
			document.getElementById('right').classList.remove("active");
			document.getElementById('listing_on').className += " active";
			document.getElementById('map_on').classList.remove("active");
			// document.getElementsByClassName('items_pagination')[0].style.display = "flex";
			document.getElementById('pag').style.display = "";
			document.getElementById('loadmore').style.display = "";
			document.getElementsByClassName('head_block')[0].style.marginBottom = '46px';

		}
		if (id == "right") {
			document.getElementById('right').className += " active";
			document.getElementById('left').classList.remove("active");
			document.getElementById('map_on').className += " active";
			document.getElementById('listing_on').classList.remove("active");
			// document.getElementsByClassName('items_pagination')[0].style.display = "none";
			document.getElementById('pag').style.display = "none";
			document.getElementById('loadmore').style.display = "none";
			document.getElementsByClassName('head_block')[0].style.marginBottom = '46px';
			$("html,body").animate({ scrollTop: $('.view_position').offset().top - 100 }, 600);
		}
	}

	reloadListing(page = 1) {
		let self = this;
		self.filter.filterClose();

		if (!self.filter.requiredDataCheck()) {
			self.filter.blockSubmitButton();
			return;
		}

		self.filter.unblockSubmitButton();
		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				$('[data-listing-list]').html(response.listing);

				// self.getSwiper();
				self.initMainSlider();

				$('[data-listing-title]').html(response.title);
				$('[data-listing-breadcrumbs]').html(response.crumbs);
				$('[data-tags]').html(response.tags);
				$('[data-listing-text-top]').html(response.text_top);
				$('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-pagination-wrapper]').html(response.pagination);
				document.title = response.seo_title;

				if (response.mapPageExistFlag) {
					let isButtonActive = $('.btn_list._right').hasClass('active');
					$('.btn_list._right p').unwrap().wrap('<a id="right" href="/catalog/' + response.url + 'map/" class="btn_list _right' + (isButtonActive ? ' active' : '') + '"></a>');
				} else {
					$('.btn_list._right p').unwrap().wrap('<div class="btn_list _right" id="right"></div>');
					$('div.btn_list._right').on('click', () => {
						self.viewListing('right');
					});
				}

				$('body').removeClass();
				$('body').addClass(response.prazdnik_type);
				self.block.removeClass('_loading');
				$('html,body').animate({ scrollTop: 0 }, 400);
				history.pushState({}, '', '/catalog/' + response.url);
				self.yaMap.refresh(self.filter);
			}
		);
	}

	loadMoreListing(page = 1) {
		let self = this;

		self.filter.filterClose();

		if (!self.filter.requiredDataCheck()) {
			self.filter.blockSubmitButton();
			return;
		}

		self.filter.unblockSubmitButton();
		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				// $('[data-listing-list]').append(response.listing);

				let listingList = document.querySelector('[data-listing-list]');
				let listingTitle = document.querySelector('[data-listing-title]');
				let listingBreadcrumbs = document.querySelector('[data-listing-breadcrumbs]');
				// let listingTextTop = document.querySelector('[data-listing-text-top]');
				// let listingTextBottom = document.querySelector('[data-listing-text-bottom]');
				let paginationWrapper = document.querySelector('[data-pagination-wrapper]');
				let loadmoreBtn = document.querySelector('[data-loadmore]');

				listingList.insertAdjacentHTML('beforeend', response.listing);
				self.initMainSlider();
				listingTitle.innerHTML = response.title;
				listingBreadcrumbs.innerHTML = response.crumbs;
				// listingTextTop.innerHTML = response.text_top;
				// listingTextBottom.innerHTML = response.text_bottom;
				paginationWrapper.innerHTML = response.pagination;
				loadmoreBtn.innerHTML = response.loadMore;
				document.title = response.seo_title;

				

				// $('[data-listing-title]').html(response.title);
				// $('[data-listing-breadcrumbs]').html(response.crumbs);
				// $('[data-listing-text-top]').html(response.text_top);
				// $('[data-listing-text-bottom]').html(response.text_bottom);
				// $('[data-pagination-wrapper]').html(response.pagination);
				// document.title = response.seo_title;

				// if (response.mapPageExistFlag) {
				// 	let isButtonActive = $('.btn_list._right').hasClass('active');
				// 	$('.btn_list._right p').unwrap().wrap('<a id="right" href="/catalog/' + response.url + 'map/" class="btn_list _right' + (isButtonActive ? ' active' : '') + '"></a>');
				// } else {
				// 	$('.btn_list._right p').unwrap().wrap('<div class="btn_list _right" id="right"></div>');
				// 	$('div.btn_list._right').on('click', () => {
				// 		self.viewListing('right');
				// 	});
				// }

				// $('[data-loadmore]').html(response.loadMore);
				self.block.removeClass('_loading');

				history.pushState({}, '', '/catalog/' + response.url);
				self.yaMap.refresh(self.filter);
			}
		);
	}
}