import Animation from './animation.js';
//import modal from './modal';
import {status, json} from './utilities';
import Inputmask from 'inputmask';

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

		this.bind();	

		// this.$form.find('[data-calendar-input-wrapper]').on('click' , function(){
		// 	console.log(1221);
		// 	let parent = $(this).closest('.input_wrapper');

		// 	parent.find('.qs-num').on('click' , function(){

		// 		setTimeout(function() {
		// 			let valueVisible = parent.find('.addCalendar').val();
		// 			let valueHidInp = parent.find('[data-form-hidden-input]').val();
		// 			let valueHidInpTwo = parent.find('[data-form-hidden-input-two]').val();

		// 			if (valueVisible == ''){
		// 				parent.find('.addCalendar').val(valueHidInp);
		// 			}	else if (valueHidInp != '' && valueHidInpTwo != '' ){
		// 				parent.find('.addCalendar').val(valueHidInp + '-' + valueHidInpTwo);
		// 				parent.find('[data-form-hidden-input]').addClass('_hide');
		// 			}	else if (valueHidInp != '' && valueHidInpTwo ==''){
		// 				parent.find('.addCalendar').val(valueHidInp);
		// 			}	else {
		// 				parent.find('.addCalendar').val('');
		// 			}
		// 		},50);
		// 	});
		// });

		// this.$form.find('[data-calendar-input-wrapper]').on('click' , function(){
		// 	console.log('open calendar');

		// });

		// this.$form.find('[data-form-hidden-input]').on('click' , function(){
		// 	let parent = $(this).closest('.input_wrapper');
		// 	$(this).removeClass('_active');
		// 	parent.find('[data-form-hidden-input-two]').addClass('_active');

		// })

		// this.$form.find('[data-form-hidden-input-two]').on('click' , function(){
		// 	let parent = $(this).closest('.input_wrapper');
		// 	$(this).removeClass('_active');
		// 	parent.find('[data-form-hidden-input]').addClass('_active');
		// 	parent.find('[data-form-hidden-input]').addClass('_hide');
		// })


		// this.$form.find('[data-form-hidden-input]').on('click' , function(){
		// 	let parent = $(this).closest('.input_wrapper');
		// 	let hiddenValue = parent.find('[data-form-hidden-input]').val();
		// 	console.log(hiddenValue);
		// 	let visibleValue = parent.find('.addCalendar').val();
		// 	parent.find('.addCalendar').val(visibleValue + "-" + hiddenValue);

		// 	parent.find('[data-form-hidden-input]').removeClass('_active');
		// })	
	}

	bind() {

		this.$form.find('[data-dynamic-placeholder]').each(function () {
			$(this).on('blur',function () {
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
			  // console.log('input change');
			  this.checkValid();
			  // this.checkField($(e.currentTarget));
			  // this.checkValid();
			});
		});

		this.$form.on('submit', (e) => {
			this.sendIfValid(e);
		});

		this.$form.on('click', 'button.disabled', function(e) {
			e.preventDefault();
			return false;
		})

		this.$policy.on('click',(e) => {
			var $el = $(e.currentTarget);

			if ($el.prop('checked'))
			$el.removeClass('_invalid');
				else
			$el.addClass('_invalid');

			this.checkValid();
		})

		this.$form.find('[data-action="form_checkbox"]').on('click',(e) => {
			let $el = $(e.currentTarget);
			let $input = $el.siblings('input');

			$input.prop("checked", !$input.prop("checked"));
			$el.closest('.checkbox_item').toggleClass('_active');
		})

		this.$formWrap.find('[data-success] [data-success-close]').on('click', (e) => {
			this.$formWrap.find('[data-success]').removeClass('_active');
		});

		this.$form.find('[data-form-privacy]').on('click', (e) => {
			let $el = $(e.currentTarget);

			if(!$(e.target).hasClass('_link')){
				$el.toggleClass('_active');

				if($el.hasClass('_active')){
					this.$submitButton.removeClass('disabled');
				}
				else{
					this.$submitButton.addClass('disabled');
				}
			}
		});
	}

	checkValid() {
		this.$submitButton.removeClass('disabled');
		if (this.$form.find('.form_input_invalid').length > 0) {
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

		        if (name === 'policy' && $field.prop('checked'))
		          valid = true;
			}
			if (valid) {
				$field.removeClass('_invalid');

        		if ($field.parent().find('.form_input_error').length > 0)
					$field.parent().find('.form_input_error').html('');

			} else {
				$field.addClass('_invalid');
				var form_error = $field.data('error') || 'Заполните поле';
				var error_message = custom_error || form_error;

				if ($field.siblings('.form_input_error').length  == 0) {
					$field.parent('.elementWrap').append('<div class="form_input_error">' + error_message + '</div>');
				} else {
					$field.siblings('.form_input_error').html(error_message);
				}
			}
	}

	checkFields() {
		var valid = true;

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
		//modal.append(data);
		//modal.show();
		switch(formType) {
		  case 'index':
		    ym(74721805,'reachGoal','trudnosti_s_vyborom_zala');
				// console.log(data);
		    break;
		  case 'item':
		    ym(74721805,'reachGoal','zabronirovat_eto_mesto');
				// console.log(data);
		    break;
		  case 'sideListingForm':
				ym(74721805,'reachGoal','click_otpravit_zvonok');
				// console.log(data);
		    break;
		  case 'sideItemForm':
				ym(74721805,'reachGoal','click_otpravit_zvonok');
				// console.log(data);
		    break;
		  case 'popup':
				ym(74721805,'reachGoal','click_otpravit_podberite_mne_zal');
				// console.log(data);
		    break;
		}

		this.$formWrap.find('[data-success] [data-success-name]').text(data.payload.name);
		this.$formWrap.find('[data-success] [data-success-phone]').text(data.phone);
		this.$formWrap.find('[data-success]').addClass('_active');

		this.reset();
		// this.$submitButton.removeClass('button__pending');
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

	    for (var pair of formData.entries()) {
		    console.log(pair[0]+ ', ' + pair[1]); 
		}

	    $.ajax({
            beforeSend: function() {
            	self.disabled = true;
                self.beforeSend();
            },
            type: "POST",
            url: self.to,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
            	self.$submitButton.removeClass('disabled');
            	self.success(response, formType);
            	self.disabled = false;
            },
            error: function(response) {
            	self.$submitButton.removeClass('disabled');
                self.error(response, formType);
                self.disabled = false;
            }
        });
	}
}
