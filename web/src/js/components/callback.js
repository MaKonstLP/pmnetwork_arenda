export default class Callback{

	constructor(){

		$('.arrow_open').on('click', () => {
			this.callbackOpenForm();
		})

		$('.callMe').on('click', () => {
			this.callbackOpenForm();
		})

		$('.arrow_open._right').on('click', () => {
			this.closePhone();
		})

		$('.object_book_hidden_button._callback').on('click', () => {
			this.openPhone();
			$('.object_book_hidden_button._callback').addClass('_hidden');
			$('.object_book_hidden_button._callback').closest('.object_book_hidden').addClass('_new_height');
		})

		$('.go').on('click', () => {
			this.closeFormSecond();
		})

		$('.form_success_close._link').on('click', () => {
			this.closeFormSecond();
		})

		$('.form_success_close._link').on('click', () => {
			this.closePhone();
		})
	}

	callbackOpenForm() {
		document.getElementById('firstWindow').style.display = "none";
		document.getElementById('secondWindow').style.display = "flex";
	}

	closePhone() {
		document.getElementById('firstWindow').style.display = "flex";
		document.getElementById('secondWindow').style.display = "none";
		document.getElementById('theirdWindow').style.display = "none";
	}

	closeFormSecond() {
		document.getElementById('secondWindow').style.display = "none";
	}

	openPhone() {
		var phone = document.getElementsByClassName('object_real_phone')[0].outerHTML;
		document.getElementById('callback_object_phone').innerHTML = phone;
	}

}