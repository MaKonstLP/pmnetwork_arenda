'use strict';
import Form from './form';
import Cookies from 'js-cookie';

export default class Filter {
	constructor($filter) {
		let self = this;
		this.$filter = $filter;
		this.form = new Form();
		this.state = {};
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		this.init(this.$filter);

		if (self.mobileMode) {
			$('.popup_filter_container').append($('.filter_mobile'));

			// ОТКРЫТЬ МОБИЛЬНЫЙ ФИЛЬТР
			$('body').find('[data-filter-open]').on('click', (e) => {
				$('body').addClass('_popup_mode');
				$('.popup_wrap').addClass('_active');
				$('.popup_form').addClass('_hidden');
				$('.popup_filter_container').removeClass('_hidden');
			});

			// ЗАКРЫТЬ МОБИЛЬНЫЙ ФИЛЬТР
			this.$filter.find('[data-filter-close]').on('click', (e) => {
				self.closeMobileFilter();
			});

			// КЛИК ПО СТРЕЛОЧКЕ В МОБИЛЬНОМ ФИЛЬТРЕ
			this.$filter.find('.filter_label').on('click', function () {
				let $parent = $(this).closest('.filter_select_block');

				if ($parent.hasClass("_active")) {
					$parent.removeClass("_active");
				} else {
					$parent.addClass("_active");
				}
			});

			// КЛИК ПО КНОПКЕ СБРОСИТЬ В МОБИЛЬНОМ ФИЛЬТРЕ
			this.$filter.find('[data-clean-mobile]').on('click', function () {

				if ($(this).closest('[data-filter-select-block]').length > 0) {
					let $parent = $(this).closest('[data-filter-select-block]');
					$parent.find('[data-filter-select-category]._active').removeClass('_active');
					$parent.find('[data-filter-label]').removeClass('_active');
					$parent.find('[data-filter-select-item]').removeClass('_active');

					delete self.state[$parent.data('type')];

				} else if ($(this).closest('[data-filter-checkbox-block]').length > 0) {
					let $parent = $(this).closest('[data-filter-checkbox-block]');
					$parent.find('.filter_label').removeClass('_active');
					$parent.find('[data-filter-checkbox-item]._checked').removeClass('_checked');

					delete self.state['alko'];
					delete self.state['gift'];
				}
			});

			// КЛИК ПО КНОПКЕ "СБРОСИТЬ ФИЛЬТР" В МОБИЛЬНОМ ФИЛЬТРЕ
			$('body').find('[data-clean]').on('click', (e) => {
				this.$filter.find('.filter_select_item._active').removeClass('_active');
				this.$filter.find('[data-filter-checkbox-item]._checked').removeClass('_checked');
				this.$filter.find('.filter_label._active').removeClass('_active');
				this.state = {};
			});

			// КЛИК ПО СТРОКЕ В СЕЛЕКТЕ В МОБИЛЬНОМ ФИЛЬТРЕ
			this.$filter.find('[data-filter-select-item]').on('click', function () {
				//выбор праздника работает как радиокнопки, а не как чекбокс
				if ($(this).closest('.filter_select_block').data('type') == 'prazdnik') {
					let prazdnikSelects = $(this).closest('.filter_select_block[data-type="prazdnik"]').find('[data-filter-select-item]');
					prazdnikSelects.not(this).removeClass('_active');// Снимаем чекбокс со всех остальных чекбоксов, кроме выбранного
					$(this).toggleClass('_active');
				} else {
					$(this).toggleClass('_active');
				}
				// $(this).toggleClass('_active');
				self.selectStateRefresh($(this).closest('[data-filter-select-block]'));
			});

			// КЛИК ПО СТРОКЕ В СЕЛЕКТЕ, КЛИК ПО ПОДГРУППЕ В МОБИЛЬНОМ ФИЛЬТРЕ
			this.$filter.find('[data-filter-select-category]').on('click', function () {
				$(this).toggleClass('_active');
				self.categoryStateRefresh($(this));
			});

			// КЛИК ПО ЧЕКБОКСУ
			this.$filter.find('[data-filter-checkbox-item]').on('click', function () {
				$(this).toggleClass('_checked');
				self.checkboxStateRefresh($(this));
			});

		} else {

			// КЛИК ПО КНОПКЕ СБРОСИТЬ В БЛОКЕ СЕЛЕКТА
			this.$filter.find('[data-filter-select-current-clean]').on('click', function () {

				if ($(this).closest('[data-filter-select-block]').length > 0) {
					console.log('clean_block');
					let $parent = $(this).closest('[data-filter-select-block]');
					let content = $parent.find('.filter_p').text();
					$parent.find('[data-filter-select-category]._active').removeClass('_active');
					$parent.find('.filter_p').text("");

					self.selectBlockClick($parent);

					if (content != '') {
						$parent.find('[data-filter-label]').removeClass('_active');
						$parent.find('[data-filter-select-current]').removeClass('_xActive');
						$parent.find('[data-quantity]').addClass('_none');
						$parent.find('[data-filter-select-item]').removeClass('_active');
					}
					delete self.state[$parent.data('type')];

				} else if ($(this).closest('[data-filter-checkbox-block]').length > 0) {
					let $parent = $(this).closest('[data-filter-checkbox-block]');

					if ($parent.find('.filter_p').text() !== '') {
						$parent.find('.filter_p').text('');
						$parent.find('.filter_label').removeClass('_active');
						$parent.find('.filter_select_current').removeClass('_xActive');
						$parent.find('[data-quantity]').addClass('_none');
						$parent.find('[data-filter-checkbox-item]._checked').removeClass('_checked');
					}
					delete self.state['alko'];
					delete self.state['gift'];
				}

				$(this).removeClass('_active');
			});

			// КЛИК ПО БЛОКУ СЕЛЕКТОРА С ЧЕКБОКСАМИ
			this.$filter.find('[data-filter-checkbox-wrapper]').on('click', function () {
				let $parent = $(this).closest('[data-filter-checkbox-block]');
				self.selectBlockClick($parent);
				$(this).find('.filter_p').text();
				$(this).find('[data-filter-select-current-clean]').addClass('_active');
			});

			// КЛИК ПО БЛОКУ С СЕЛЕКТОМ
			this.$filter.find('[data-filter-select-current]').on('click', function (e) {
				let block = $(e.target);

				if (!block.hasClass('close_x')) {
					let $parent = $(this).closest('[data-filter-select-block]');
					self.selectBlockClick($parent);
					let content = $(this).find('.filter_p').text();

					if ($parent.find('.filter_select_item._active')) {
						$(this).find('[data-filter-select-current-clean]').addClass('_active');
					}
				}
			});

			// КЛИК ПО КНОПКЕ СБРОСИТЬ
			$('body').find('[data-clean]').on('click', (e) => {
				this.$filter.find('[data-filter-select-current] p').text('');
				this.$filter.find('[data-filter-label]').removeClass('_active');
				this.$filter.find('[data-quantity]').addClass('_none');
				this.$filter.find('[data-filter-select-current]').removeClass('_xActive');
				this.$filter.find('[data-filter-select-category]._active').removeClass('_active');
				this.$filter.find('[data-filter-select-item]._active').removeClass('_active');
				this.$filter.find('[data-filter-checkbox-item]._checked').removeClass('_checked');
				this.$filter.find('[data-filter-checkbox-wrapper]').removeClass('_xActive');
				this.$filter.find('[data-filter-checkbox-block] .filter_label').removeClass('_active');
				this.$filter.find('[data-filter-checkbox-wrapper] p').text('');
				this.state = {};
				// console.log(this.state);
			});

			// КЛИК ПО СТРОКЕ В СЕЛЕКТЕ
			this.$filter.find('[data-filter-select-item]').on('click', function () {

				//выбор праздника работает как радиокнопки, а не как чекбокс
				if ($(this).closest('.filter_select_block').data('type') == 'prazdnik') {
					let prazdnikSelects = $(this).closest('.filter_select_block[data-type="prazdnik"]').find('[data-filter-select-item]');
					prazdnikSelects.not(this).removeClass('_active');// Снимаем чекбокс со всех остальных чекбоксов, кроме выбранного
					$(this).toggleClass('_active');
				} else {
					$(this).toggleClass('_active');
				}

				// $(this).toggleClass('_active');
				// self
				self.selectStateRefresh($(this).closest('[data-filter-select-block]'));
			});

			// КЛИК ПО СТРОКЕ В СЕЛЕКТЕ, КЛИК ПО ПОДГРУППЕ
			this.$filter.find('[data-filter-select-category]').on('click', function () {
				$(this).toggleClass('_active');
				self.categoryStateRefresh($(this));
			});

			// КЛИК ПО ЧЕКБОКСУ
			this.$filter.find('[data-filter-checkbox-item]').on('click', function () {
				$(this).toggleClass('_checked');
				self.checkboxStateRefresh($(this));
			});
		}

		// КЛИК ВНЕ БЛОКА С СЕЛЕКТОМ
		$('body').click(function (e) {
			if (!$(e.target).closest('.filter_select_block').length) {
				self.selectBlockActiveClose();
			}
		});
	}

	init() {
		let self = this;

		if (self.mobileMode) {
			$('[data-filter-wrapper].filter_mobile [data-filter-select-block]').each(function () {
				self.selectStateRefresh($(this));
			});

			$('[data-filter-wrapper].filter_mobile [data-filter-checkbox-item]').each(function () {
				self.checkboxStateRefresh($(this));
			});

		} else {
			$('[data-filter-wrapper].filter [data-filter-select-block]').each(function () {
				self.selectStateRefresh($(this));
			});

			$('[data-filter-wrapper].filter [data-filter-checkbox-item]').each(function () {
				self.checkboxStateRefresh($(this));
			});
		}

		self.refreshCategoryCheckboxes();
	}

	refreshCategoryCheckboxes() {
		var self = this;
		var $filter = null;

		if (self.mobileMode) {
			$filter = $('[data-filter-wrapper].filter_mobile')
		} else {
			$filter = $('[data-filter-wrapper].filter')
		}

		$filter.find('[data-filter-select-category]').each(function (i) {
			if ($filter.find('[data-filter-select-item][data-category=' + $(this).data('value') + ']').length === $filter.find('[data-filter-select-item][data-category=' + $(this).data('value') + ']._active').length) {
				$(this).addClass('_active');
			}
		});
	}

	filterClose() {
		this.$filter.removeClass('_active');
	}

	closeMobileFilter() {
		$('body').removeClass('_popup_mode');
		$('.popup_wrap').removeClass('_active');
		$('.popup_form').removeClass('_hidden');
		$('.popup_filter_container').addClass('_hidden');
	}

	requiredDataCheck() {
		var self = this;
		if (self.state['prazdnik'] !== undefined || self.state['rest_type'] !== undefined) {
			$('[data-type="prazdnik"] [data-filter-select-current]').removeClass('_invalid');
			$('[data-type="rest_type"] [data-filter-select-current]').removeClass('_invalid');
			$('[data-type="prazdnik"] [data-filter-label]').removeClass('_invalid');
			$('[data-type="rest_type"] [data-filter-label]').removeClass('_invalid');

			return true;
		}

		$('[data-type="prazdnik"] [data-filter-select-current]').addClass('_invalid');
		$('[data-type="rest_type"] [data-filter-select-current]').addClass('_invalid');
		$('[data-type="prazdnik"] [data-filter-label]').addClass('_invalid');
		$('[data-type="rest_type"] [data-filter-label]').addClass('_invalid');

		return false;
	}

	blockSubmitButton() {
		$('[data-filter-button]').addClass('_disabled');
	}

	unblockSubmitButton() {
		$('[data-filter-button]').removeClass('_disabled');
	}

	filterListingSubmit(page = 1) {
		let self = this;

		if (self.requiredDataCheck()) {
			self.state.page = page;

			if (self.mobileMode) {
				self.closeMobileFilter();
			}

			// === устанавлием куки для формирования цен по типам мероприятий START ===
			//соотвествие id в таблице filter_items и restaurants_spec
			const prazdnikArr = {
				'1': '9', //День рождения
				'2': '12', //Детский день рождения
				'3': '1', //Свадьба
				'4': '17', //Новый год
				'5': '15', //Корпоратив
				'6': '11', //Выпускной
			}

			if (self.state.prazdnik) {
				Cookies.set('prazdnik_id', self.state.prazdnik, { expires: 30 / 1440 }); //устанавливаем в куки id праздника если есть, сроком на 30минут
				Cookies.set('prazdnik_spec_id', prazdnikArr[self.state.prazdnik], { expires: 30 / 1440 }); //устанавливаем в куки spec_id праздника если есть, сроком на 30минут
				self.form.checkCookiePrazdnikId(prazdnikArr[self.state.prazdnik]);
			} else {
				Cookies.remove('prazdnik_id');
				Cookies.remove('prazdnik_spec_id');
				self.form.checkCookiePrazdnikId('');
			}
			// === устанавлием куки для формирования цен по типам мероприятий END ===

			let data = {
				'filter': JSON.stringify(self.state)
			}

			console.log('data: ', data);

			this.promise = new Promise(function (resolve, reject) {
				console.log('this.promise');
				self.reject = reject;
				self.resolve = resolve;
			});

			$.ajax({
				type: 'get',
				url: '/ajax/filter/',
				data: data,
				success: function (response) {
					console.log('success');
					// response = $.parseJSON(response);
					response = JSON.parse(response);
					ym(74721805, 'reachGoal', 'filter');
					// dataLayer.push({'event': 'filter'});
					gtag('event', 'filter', { 'event_category': 'click', 'event_action': 'filter' });
					// console.log(response.params_filter);
					self.resolve(response);
				},
				error: function (response) {
					console.log('error');
				}
			});
		}
	}

	filterMainSubmit() {
		let self = this;

		if (self.requiredDataCheck()) {

			let data = {
				'filter': JSON.stringify(self.state)
			}

			this.promise = new Promise(function (resolve, reject) {
				self.reject = reject;
				self.resolve = resolve;
			});

			$.ajax({
				type: 'get',
				url: '/ajax/filter-main/',
				data: data,
				success: function (response) {
					if (response) {
						ym(74721805, 'reachGoal', 'filter');
						// dataLayer.push({'event': 'filter'});
						gtag('event', 'filter', { 'event_category': 'click', 'event_action': 'filter' });
						self.resolve('/catalog/' + response);
					}
					else {
						self.resolve(self.filterListingHref());
					}
				},
				error: function (response) {

				}
			});
		}
	}

	selectBlockClick($block) {
		if ($block.hasClass('_active')) {
			this.selectBlockClose($block);
		}
		else {
			this.selectBlockOpen($block);
		}
	}

	selectBlockClose($block) {
		$block.removeClass('_active');
	}

	selectBlockOpen($block) {
		this.selectBlockActiveClose();
		$block.addClass('_active');
	}

	selectBlockActiveClose() {
		this.$filter.find('[data-filter-select-block]._active').each(function () {
			$(this).removeClass('_active');
		});

		this.$filter.find('[data-filter-checkbox-block]._active').each(function () {
			$(this).removeClass('_active');
		});
	}

	selectStateRefresh($block) {
		let self = this;
		let blockType = $block.data('type');
		let $items = $block.find('[data-filter-select-item]._active');
		let selectText = '';

		if ($items.length > 0) {
			self.state[blockType] = '';
			$items.each(function () {
				if (self.state[blockType] !== '') {
					self.state[blockType] += ',' + $(this).data('value');
					// selectText = 'Выбрано ('+$items.length+')';
					selectText += ', ' + $(this).text();
					$block.find('[data-quantity]').removeClass('_none');
					$block.find('[data-quantity] span').text($items.length);
					$block.find('[data-filter-select-current]').addClass('_xActive');
				}
				else {
					self.state[blockType] = $(this).data('value');
					selectText = $(this).text();
					$block.find('[data-filter-label]').addClass('_active');
					$block.find('[data-quantity]').addClass('_none');
					$block.find('[data-filter-select-current]').addClass('_xActive');
				}
			});

			$block.find('.close_x').addClass('_active');
		} else {
			$block.find('.close_x').removeClass('_active');
			delete self.state[blockType];
		}

		if (selectText == "") {
			$block.find('[data-filter-label]').removeClass('_active');
			$block.find('[data-quantity]').addClass('_none');
			$block.find('[data-filter-select-current]').removeClass('_xActive');
		}

		$block.find('[data-filter-select-current] p').text(selectText);
	}

	checkboxStateRefresh($item) {
		let self = this;
		let blockType = $item.closest('[data-type]').data('type');
		let checkboxText = '';

		if ($item.hasClass('_checked')) {

			this.state[blockType] = 1;
		} else {

			delete this.state[blockType];
		}
		let $checkedItems = null;
		if (self.mobileMode) {
			$checkedItems = $('[data-filter-wrapper].filter_mobile [data-filter-checkbox-item]._checked');

		} else {
			$checkedItems = $('[data-filter-wrapper].filter [data-filter-checkbox-item]._checked');
		}

		if ($checkedItems.length > 0) {

			$checkedItems.each(function (i) {
				checkboxText += ', ' + $($checkedItems[i]).find('p').text();
			});

			checkboxText = checkboxText.slice(2);
		}

		$('[data-filter-checkbox-wrapper] p').text(checkboxText);
		if (checkboxText !== '') {
			$item.closest('.filter_select_list').siblings('.filter_label').addClass('_active');
			$item.closest('.filter_select_list').siblings('.filter_select_current').addClass('_xActive');
		} else {
			$item.closest('.filter_select_list').siblings('.filter_label').removeClass('_active');
			$item.closest('.filter_select_list').siblings('.filter_select_current').removeClass('_xActive');
		}

		var $quantityCircle = $item.closest('.filter_select_list').siblings('.filter_select_current').find('[data-quantity]');

		if ($checkedItems.length > 1) {
			$quantityCircle.removeClass('_none');
			$quantityCircle.find('span').text($checkedItems.length);
		} else {
			$quantityCircle.addClass('_none');
		}
	}

	categoryStateRefresh($category) {
		var self = this;
		var $block = $category.closest('[data-filter-select-block]');
		var $currentFilter = $block.closest('[data-filter-wrapper]');
		var blockType = $block.data('type');
		var $activeCategoryItemHeap = $currentFilter.find('[data-category=' + $category.data('value') + ']');
		var selectText = '';
		var state = [];

		if (!self.state[blockType]) {
			self.state[blockType] = '';
		} else if (self.state[blockType]) {
			state = (self.state[blockType] + '').split(',');
		}

		$activeCategoryItemHeap.each(function () {

			if ($category.hasClass('_active')) {
				$block.find('.filter_select_current').addClass('_xActive');
				$block.find('.filter_label').addClass('_active');
				$(this).addClass('_active');

				if (!state.includes($(this).data('value') + '')) {
					state.push($(this).data('value'));
				}

			} else {
				$block.find('.filter_select_current').removeClass('_xActive');
				$(this).removeClass('_active');

				if (state.includes($(this).data('value') + '')) {
					state.splice(state.indexOf($(this).data('value') + ''), 1);
				}
			}
		});

		var $activeItems = $block.find('[data-filter-select-item]._active')
		$activeItems.each(function () {
			selectText += ', ' + $(this).find('p').text();
		});
		$block.find('[data-filter-select-current] .filter_p').text(selectText.slice(2));

		if ($activeItems.length > 1) {
			$block.find('[data-quantity]').removeClass('_none');
			$block.find('[data-quantity] span').text($activeItems.length);
		} else {
			$block.find('[data-quantity]').addClass('_none');
		}

		if (selectText === '') {
			$block.find('.filter_label').removeClass('_active');
		}

		state = state.join();
		if (state !== '') {
			self.state[blockType] = state;
		} else {
			delete self.state[blockType];
		}
	}

	filterListingHref() {
		if (Object.keys(this.state).length > 0) {
			var href = '/catalog/?';
			$.each(this.state, function (key, value) {
				href += '&' + key + '=' + value;
			});
		}
		else {
			var href = '/catalog/';
		}
		return href;
	}

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	};

}