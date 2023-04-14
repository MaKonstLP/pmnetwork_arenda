import Animation from './animation.js';
//import modal from './modal';
import { status, json } from './utilities';
import Inputmask from 'inputmask';
import Cookies from 'js-cookie';

var animation = new Animation;

export default class Form {
	constructor(form) {
		let self = this;
		this.$form = $(form);
		this.$formWrap = this.$form.parents('.form_wrapper');
		this.$submitButton = this.$form.find('input[type="submit"]');
		this.$policy = this.$form.find('[name="policy"]');
		this.to = (this.$form.attr('action') == undefined || this.$form.attr('action') == '') ? this.to : this.$form.attr('action');
		let im_phone = new Inputmask('+7 (999) 999-99-99', {
			clearIncomplete: true,
		});
		im_phone.mask($(this.$form).find('[name="phone"]'));

		this.$form.find('[data-form-select-current]').on('click', function () {
			var $parent = $(this).closest('[data-form-select-block]');
			self.selectBlockClick($parent);
		});

		//задаем в форме тип события, который выбран в фильтре
		if (Cookies.get('prazdnik_spec_id')) {
			let prazdnikSpecId = Cookies.get('prazdnik_spec_id');
			self.checkCookiePrazdnikId(prazdnikSpecId);
		}

		$('body').click(function (e) {
			if (!$(e.target).closest('.form_select_block').length) {
				self.selectBlockActiveClose();
			}

			if ($(e.target).closest('.form_select_list').length) {
				self.selectBlockActiveClose();
			}
		});

		this.$form.find('[data-form-select-item]').on('click', function () {
			$(this).siblings('[data-form-select-item]._active').removeClass('_active');
			$(this).addClass('_active');
			self.selectStateRefresh($(this).closest('[data-form-select-block]'));
		});

		if (this.$form.closest('[data-side-callback-form]').length > 0) {
			this.$form.closest('[data-side-callback-form]').on('click', function (e) {

				if ($(e.target).hasClass('callMe')) {
					console.log('callMe');
					showSecondWindow();
					ym(74721805, 'reachGoal', 'click_zakazat_zvonok');

					return 1;
				}

				if ($(e.target).hasClass('arrow_open') && $(e.target).hasClass('_right')) {
					console.log('arrow_open._right');
					showFirstWindow();

					return 1;
				}

				if ($(e.target).hasClass('arrow_open')) {
					console.log('arrow_open');
					showSecondWindow();

					return 1;
				}
			});

			$('.object_book_hidden_button._callback').on('click', () => {
				openPhone();
				$('.object_book_hidden_button._callback').addClass('_hidden');
				$('.object_book_hidden_button._callback').closest('.object_book_hidden').addClass('_new_height');

				// ==== Gorko-calltracking ====
				let phone = document.querySelector('.object_real_phone').innerHTML;
				self.sendCalltracking(phone);
			})

			if ($('[data-page-type="listing"]').length > 0) {
				$('.callback._first_form .phone_number').on('click', function () {
					ym(74721805, 'reachGoal', 'click_phone_call_centra');
					gtag('event', 'phone_call_centra', { 'event_category': 'click', 'event_action': 'phone_call_centra' });
				});
			}

			function showFirstWindow() {
				document.getElementById('firstWindow').style.display = "flex";
				document.getElementById('secondWindow').style.display = "none";
				document.getElementById('theirdWindow').style.display = "none";
			}

			function showSecondWindow() {
				document.getElementById('firstWindow').style.display = "none";
				document.getElementById('secondWindow').style.display = "flex";
				document.getElementById('theirdWindow').style.display = "none";
			}

			function openPhone() {
				var phone = document.getElementsByClassName('object_real_phone')[0].outerHTML;
				phone += '<div class="phone-bufer"><div class="phone-bufer__close"></div>Номер скопирован в буфер обмена</div>';
				document.getElementById('callback_object_phone').innerHTML = phone;
				ym(74721805, 'reachGoal', 'show_phone_zala_sboky');
				gtag('event', 'show_phone_zala_sboky', { 'event_category': 'click', 'event_action': 'show_phone_zala_sboky' });

				if ($('[data-page-type="item"]').length > 0) {
					$('.callback._first_form .object_real_phone').on('click', function () {
						ym(74721805, 'reachGoal', 'click_phone_zala');

						// ==== Gorko-calltracking ====
						let phone = $(this).text();
						self.sendCalltracking(phone);
					});
				}
			}
		}

		$('[data-page-type="listing"]').on('click', $('input[name="find"]'), function (e) {
			let allTextAreaWrap = $('.listing_feedback_form .comments_textarea');
			let allTextArea = $('.listing_feedback_form .comments_textarea').find('textarea');
			let checkedRadioData = $('input[name="find"]:checked').data('textarea');
			let selectedTextAreaWrap = $(`.comments_textarea[data-textarea="${checkedRadioData}"]`);
			let selectedTextArea = selectedTextAreaWrap.find('textarea');

			allTextAreaWrap.removeClass('_active');

			allTextArea.removeAttr('data-required');
			allTextArea.removeClass('_invalid');
			if (selectedTextAreaWrap.length > 0) {
				selectedTextAreaWrap.addClass('_active');
				selectedTextArea.attr('data-required', '');
				self.checkField(selectedTextArea);
				allTextArea.not(selectedTextArea).val('');
			}

			self.checkValid();
		})

		this.bind();
	}

	bind() {

		this.$form.find('[data-dynamic-placeholder]').each(function () {
			$(this).on('blur', function () {
				if ($(this).val() == '')
					$(this).removeClass('form_input_filled');
				else
					$(this).addClass('form_input_filled');
			})
		})

		this.$form.find('[data-required]').each((i, el) => {
			$(el).on('blur', (e) => {
				this.checkField($(e.currentTarget));
				this.checkValid();
			});

			$(el).on('change', (e) => {
				this.checkValid();
				// this.checkField($(e.currentTarget));
				// this.checkValid();
			});
		});

		this.$form.on('submit', (e) => {
			this.sendIfValid(e);
		});

		this.$form.on('click', 'button.disabled', function (e) {
			e.preventDefault();
			return false;
		})

		this.$policy.on('click', (e) => {
			var $el = $(e.currentTarget);

			if ($el.prop('checked'))
				$el.removeClass('_invalid');
			else
				$el.addClass('_invalid');

			this.checkValid();
		})

		this.$form.find('[data-action="form_checkbox"]').on('click', (e) => {
			let $el = $(e.currentTarget);
			let $input = $el.siblings('input');

			$input.prop("checked", !$input.prop("checked"));
			$el.closest('.checkbox_item').toggleClass('_active');
		})

		this.$formWrap.find('[data-success] [data-success-close]').on('click', (e) => {
			this.$formWrap.find('[data-success]').removeClass('_active');
			document.getElementById('secondWindow').style.display = "flex";
			document.getElementById('theirdWindow').style.display = "none";
		});

		this.$form.find('[data-form-privacy]').on('click', (e) => {
			let $el = $(e.currentTarget);

			if (!$(e.target).hasClass('_link')) {
				$el.toggleClass('_active');

				if ($el.hasClass('_active')) {
					this.$submitButton.removeClass('disabled');
				}
				else {
					this.$submitButton.addClass('disabled');
				}
			}
		});
	}

	selectStateRefresh($block) {
		var self = this;
		var blockType = $block.data('type');
		var $item = $block.find('[data-form-select-item]._active');
		var selectText = '-';

		if ($item.length > 0) {

			$block.find('[data-form-select-current] input').prop('value', $($item[0]).data('value'));
			$block.find('[data-form-select-current] input').val($($item[0]).data('value'));
			selectText = $($item[0]).find('p').text();
		}

		$block.find('[data-form-select-current] p').text(selectText);
	}

	selectBlockClick($block) {
		if ($block.hasClass('_active')) {
			$block.removeClass('_active');
			$block.find('.form_select_list').addClass('_hidden');
		}
		else {
			this.selectBlockActiveClose();
			$block.addClass('_active');
			$block.find('.form_select_list').removeClass('_hidden');
		}
	}

	selectBlockActiveClose() {
		this.$form.find('[data-form-select-block]._active').each(function () {
			$(this).removeClass('_active');
			$(this).find('.form_select_list').addClass('_hidden');
		});
	}

	checkValid() {
		this.$submitButton.removeClass('disabled');
		if (this.$form.find('[data-required]._invalid').length > 0) {
			this.$submitButton.addClass('disabled');
		}
	}

	checkField($field) {
		var valid = true;
		var name = $field.attr('name');
		var pattern_email = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;

		if ($field.val() == '') {
			valid = false;
		} else {
			if (name === 'phone' && $field.val().indexOf('_') >= 0) {
				valid = false;
				var custom_error = 'Неверный формат телефона';
			}

			if (name === 'email' && !(pattern_email.test($field.val()))) {
				valid = false;
				var custom_error = 'Неверный формат электронной почты';
			}

			// if (name === 'guests' && (typeof +$field.val() !== 'number' || +$field.val() !== Math.floor(+$field.val()) || +$field.val() <= 0)) {
			// 	valid = false;
			// 	var custom_error = 'Введите целое положительное число';
			// }

			if (name === 'policy' && $field.prop('checked')) {
				valid = true;
			}
		}
		if (valid) {
			$field.removeClass('_invalid');

			if ($field.parent().find('.form_input_error').length > 0)
				$field.parent().find('.form_input_error').html('');

		} else {
			$field.addClass('_invalid');
			var form_error = $field.data('error') || 'Заполните поле';
			var error_message = custom_error || form_error;

			if ($field.siblings('.form_input_error').length == 0) {
				$field.parent('.elementWrap').append('<div class="form_input_error">' + error_message + '</div>');
			} else {
				$field.siblings('.form_input_error').html(error_message);
			}
		}
	}

	checkFields() {
		var valid = true;

		let antispamInput = this.$form.find('input[name="first-name"]');
		if (antispamInput.length > 0) {
			let antispamInputValue = antispamInput.val();
			if (antispamInputValue != '') {
				valid = false;
			}
		}

		this.$form.find('[data-required]').each((i, el) => {
			this.checkField($(el));
			if ($(el).hasClass('_invalid'))
				valid = false;
		});

		if (valid) {
			this.$submitButton.removeClass('disabled');
		} else {
			this.$form.find('._invalid')[0].focus();
			this.$submitButton.addClass('disabled');
		}

		return valid;
	}

	reset() {
		this.$form[0].reset();
		this.$form.find('input').removeClass('form_input_valid form_input_filled');
	}

	beforeSend() {
		this.$submitButton.addClass('disabled');
	}

	success(data, formType) {
		switch (formType) {
			case 'index':
				ym(74721805, 'reachGoal', 'trudnosti_s_vyborom_zala');
				gtag('event', 'trudnosti_s_vyborom_zala', { 'event_category': 'click', 'event_action': 'trudnosti_s_vyborom_zala' });
				break;
			case 'item':
				ym(74721805, 'reachGoal', 'zabronirovat_eto_mesto');
				gtag('event', 'zabronirovat_eto_mesto', { 'event_category': 'click', 'event_action': 'zabronirovat_eto_mesto' });
				if ($('[data-premium-rest]').length > 0) {
					let data_premium = new FormData();
					data_premium.append('gorko_id', $('[data-premium-rest]').data('premium-rest'));
					data_premium.append('channel', $('[data-channel-id]').data('channel-id'));
					data_premium.append('response', JSON.stringify(data.response));
					fetch('/premium/premium-callback/', {
						method: 'POST',
						body: data_premium,
					})
						.then((response) => response.json())
						.then((data) => {
							console.log(data);
						})
						.catch((error) => {
							console.error('Error:', error);
						});
				}
				break;
			case 'sideListingForm':
				document.getElementById('secondWindow').style.display = "none";
				document.getElementById('theirdWindow').style.display = "flex";
				ym(74721805, 'reachGoal', 'click_otpravit_zvonok');
				gtag('event', 'otpravit_zvonok', { 'event_category': 'click', 'event_action': 'otpravit_zvonok' });
				break;
			case 'sideItemForm':
				document.getElementById('secondWindow').style.display = "none";
				document.getElementById('theirdWindow').style.display = "flex";
				ym(74721805, 'reachGoal', 'click_otpravit_zvonok');
				gtag('event', 'otpravit_zvonok', { 'event_category': 'click', 'event_action': 'otpravit_zvonok' });
				if ($('[data-premium-rest]').length > 0) {
					let data_premium = new FormData();
					data_premium.append('gorko_id', $('[data-premium-rest]').data('premium-rest'));
					data_premium.append('channel', $('[data-channel-id]').data('channel-id'));
					data_premium.append('response', JSON.stringify(data.response));
					fetch('/premium/premium-callback/', {
						method: 'POST',
						body: data_premium,
					})
						.then((response) => response.json())
						.then((data) => {
							console.log(data);
						})
						.catch((error) => {
							console.error('Error:', error);
						});
				}
				break;
			case 'popup':
				ym(74721805, 'reachGoal', 'click_otpravit_podberite_mne_zal');
				gtag('event', 'otpravit_podberite_mne_zal', { 'event_category': 'click', 'event_action': 'otpravit_podberite_mne_zal' });
				break;
			case 'listing-book':
				ym(74721805, 'reachGoal', 'click_otrpavit_zabronirovat');
				if (this.$form.data('premium-listing-form-id')) {
					let data_premium = new FormData();
					data_premium.append('gorko_id', this.$form.data('premium-listing-form-id'));
					data_premium.append('channel', $('[data-channel-id]').data('channel-id'));
					data_premium.append('response', JSON.stringify(data.response));
					fetch('/premium/premium-callback/', {
						method: 'POST',
						body: data_premium,
					})
						.then((response) => response.json())
						.then((data) => {
							console.log(data);
						})
						.catch((error) => {
							console.error('Error:', error);
						});
				}
				// gtag('event', 'otpravit_podberite_mne_zal', {'event_category': 'click', 'event_action': 'otpravit_podberite_mne_zal'});
				break;
			case 'item-popup-mobile':
				ym(74721805, 'reachGoal', 'zayavka');
				// gtag('event', 'otpravit_podberite_mne_zal', {'event_category': 'click', 'event_action': 'otpravit_podberite_mne_zal'});
				if ($('[data-premium-rest]').length > 0) {
					let data_premium = new FormData();
					data_premium.append('gorko_id', $('[data-premium-rest]').data('premium-rest'));
					data_premium.append('channel', $('[data-channel-id]').data('channel-id'));
					data_premium.append('response', JSON.stringify(data.response));
					fetch('/premium/premium-callback/', {
						method: 'POST',
						body: data_premium,
					})
						.then((response) => response.json())
						.then((data) => {
							console.log(data);
						})
						.catch((error) => {
							console.error('Error:', error);
						});
				}
				break;
			case 'feedback':
				ym(74721805, 'reachGoal', 'click_otpravit_ne_nashel');
				// gtag('event', 'otpravit_podberite_mne_zal', {'event_category': 'click', 'event_action': 'otpravit_podberite_mne_zal'});
				break;
		}

		if (formType != 'feedback') {
			this.$formWrap.find('[data-success] [data-success-name]').text(data.payload.name);
		} else {
			$('.listing_feedback_form_main').removeClass('_active');
		}

		this.$formWrap.find('[data-success]').addClass('_active');
		this.reset();
	}

	error() {
		// this.$submitButton.removeClass('button__pending');
		//modal.showError();
	}

	sendIfValid(e) {
		var self = this;
		e.preventDefault();
		if (!this.checkFields()) return;
		if (this.disabled) return;

		var formData = new FormData(this.$form[0]);

		var formType = this.$form.data('type');
		formData.append('type', formType);
		var formUrl = window.location.href;
		formData.append('url', formUrl);
		var cityID = $('[data-city-id]').data('city-id');
		formData.append('cityID', cityID);

		if (formType == 'listing-book') {
			var restName = this.$form.data('rest-name');
			formData.append('restName', restName);
			var restUrl = this.$form.data('rest-url');
			formData.append('restUrl', restUrl);
		}

		for (var pair of formData.entries()) {
			console.log(pair[0] + ', ' + pair[1]);
		}

		$.ajax({
			beforeSend: function () {
				self.disabled = true;
				self.beforeSend();
			},
			type: "POST",
			url: self.to,
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				self.$submitButton.removeClass('disabled');
				self.success(response, formType);
				self.disabled = false;
			},
			error: function (response) {
				self.$submitButton.removeClass('disabled');
				self.error(response, formType);
				self.disabled = false;
			}
		});
	}

	sendCalltracking(phone) {
		let clientId = '';
		ga.getAll().forEach((tracker) => {
			clientId = tracker.get('clientId');
		})

		/* let pageReferrer = '';
		if (document.referrer) {
			pageReferrer = document.referrer;
		}

		if (Cookies.get('a_ref_0')) {
			Cookies.set('a_ref_1', pageReferrer, { expires: 365 });
		} else {
			Cookies.set('a_ref_0', pageReferrer, { expires: 365 });
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
		let utmJson = JSON.stringify(utm);

		if (Cookies.get('a_utm_0')) {
			Cookies.set('a_utm_1', utmJson, { expires: 365 });
		} else {
			Cookies.set('a_utm_0', utmJson, { expires: 365 });
		} */

		const data = new FormData();

		data.append('phone', phone);
		data.append('clientId', clientId);

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

	checkCookiePrazdnikId($prazdnikId) {
		$('[data-type="event_type"]').find('[data-form-select-item]._active').removeClass('_active');

		if ($(`[data-prazdnik-spec-id="${$prazdnikId}"]`).length != 0) {
			$('[data-type="event_type"]').find(`[data-form-select-item][data-prazdnik-spec-id="${$prazdnikId}"]`).addClass('_active');
		} else {
			$('[data-type="event_type"]').find('[data-value="Other"]').addClass('_active');
		}
		this.selectStateRefresh($('[data-form-select-block]'));
	}
}
