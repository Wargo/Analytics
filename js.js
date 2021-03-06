$(document).ready(function() {

	/*
	$('#myModal').modal({
		backdrop: 'static',
		keyboard: false,
		show: false
	});
	*/

	$('.account').click(function() {
		if ($(this).attr('checked')) {
			$('.account_' + $(this).attr('id')).attr('checked', true);
		} else {
			$('.account_' + $(this).attr('id')).attr('checked', false);
		}
	});

	$('#dp1, #dp2, #dp3, #dp4').datepicker({
		format: 'yyyy-mm-dd',
		weekStart: 1
	}).on('changeDate', function() {
		$('#dp1, #dp2, #dp3, #dp4').datepicker('hide');
	});

	$('.remove_checks').click(function() {
		$('input[type="checkbox"]').attr('checked', false);
		return false;
	});

	$('.calculate2').click(function() {
		loading($(this).attr('count'), $(this).attr('multiplier'));
	});

	$('.calculate').click(function() {
		var ret = false;
		var count = 0;
		$.each($('input[type="checkbox"].row_web'), function() {
			if (this.checked) {
				ret = true;
				count ++;
			}
		});

		if (ret) {
			loading(count, $(this).attr('multiplier'));
			return true;
		} else {
			alert('No has seleccionado ninguna web');
			return false;
		}
	});

	$('table.table').tablesorter({sortList: [[1,1]]});
	
	format_tds(true);

	$('.more-less').click(function() {
		if ($(this).html() == '+') {
			$(this).html('-');
		} else {
			$(this).html('+');
		}
		$(this).parent('li').next('ul').toggleClass('hidden');
		return false;
	});

	$('.change-date').click(function() {
		$(this).remove();
		$('.new_form').removeClass('hidden');
	});

	$('.queryType').change(function() {
		if ($(this).val() == 2) {
			$('.calculate').attr('multiplier', 4);
			$('.more_options').show();
			$('form.well').attr('action', '');
		} else if ($(this).val() == 3) {
			$('form.well').attr('action', 'pdf.php');
			$('.calculate').attr('multiplier', 2);
			$('.more_options').hide();
		} else if ($(this).val() == 4) {
			$('form.well').attr('action', 'pdf.php');
			$('.calculate').attr('multiplier', 5);
			$('.more_options').hide();
		} else {
			$('.calculate').attr('multiplier', 2);
			$('.more_options').hide();
			$('form.well').attr('action', '');
		}
	});

	$('.compare').click(function() {
		$(this).hide();
		$('.calculate2').attr('multiplier', 4);
		$('.compare_fields').show();
		$('.compare_fields').find('input').attr('disabled', false);
		return false;
	});

	$('.remove_compare').click(function() {
		$('.compare').show();
		$('.compare_fields').hide();
		$('.calculate2').attr('multiplier', 2);
		$('.compare_fields').find('input').attr('disabled', true);
		return false;
	});

	$('.country').click(function() {
		if ($(this).hasClass('show_country')) {
			$(this).removeClass('show_country');
			var element = this;
			$(this).append('<div class="preloader"><img src="images/preloader.gif" /></div>');
			$.get(this.href, function(data) {
				$('.preloader').remove();
				$(element).addClass('hide_country');
				$(element).closest('tr').after(data);
				delete_countries();
				format_tds(false);
			});
		} else {
			$(this).removeClass('hide_country');
			$(this).addClass('show_country');
			var to_delete = 'countries_' + $(this).attr('var');
			$('.' + to_delete).remove();
		}
		return false;
	});

	$('.close').click(function() {
		$(this).parent('div').fadeOut();
	});

});

function delete_countries() {
	$('.header').click(function() {
		$('.countries').remove();
	});
}

function format(s) {
	s = s.toString();
	s = s.replace('.', ',');
	return s.replace(/(^\d{1,3}|\d{3})(?=(?:\d{3})+(?:$|\.))/g, '$1.');
}

function strpos (haystack, needle, offset) {
	var i = (haystack + '').indexOf(needle, (offset || 0));
	return i === -1 ? false : i;
}

function loading(count, multiplier) {
	var secs = count * multiplier;
	var time = (secs / 100) * 1000;
	$('#myModal').modal({
		backdrop: 'static',
		keyboard: false
		//show: false
	});
	var current = 0;
	setInterval(function() {
		current = current + 1;
		$('.bar').attr('style', 'width: ' + current + '%');
	}, time);
}

function format_tds(checkit) {
	$.each($('td'), function() {
		if (checkit || $(this).parent('tr').hasClass('country_row')) {
			var current_value = parseFloat($(this).text());
			if (!$(this).hasClass('is_time') && !$(this).hasClass('first')) {
				$(this).text(format($(this).text()));
			}

			if ($(this).attr('var')) {
				if (!$(this).hasClass('is_time')) {
					var new_value = format(parseFloat($(this).attr('var')));
					var num = Math.round(100 * (current_value - parseFloat($(this).attr('var')))) / 100;

					if (parseFloat($(this).attr('var')) > current_value) {
						if ($(this).hasClass('inverse')) { // Para % de rebote
							$(this).addClass('green');
						} else {
							$(this).addClass('red');
						}
						$(this).attr('title', '-' + format(Math.abs(num)));
					} else {
						if ($(this).hasClass('inverse')) { // Para % de rebote
							$(this).addClass('red');
						} else {
							$(this).addClass('green');
						}
						$(this).attr('title', '+' + format(num));
					}
					$(this).append(' <span>(' + new_value + ')</span>');
				} else {
					var new_value = $(this).attr('var');

					if (strpos($(this).attr('title'), '-') === 0) {
						$(this).addClass('red');
					} else {
						$(this).addClass('green');
					}
					$(this).append(' <span>(' + new_value + ')</span>');
				}
			}
		}
	});

}
