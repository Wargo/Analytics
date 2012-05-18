$(document).ready(function() {

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

	$('.calculate').click(function() {
		var ret = false;
		$.each($('input[type="checkbox"]'), function() {
			if (this.checked) {
				ret = true;
			}
		});

		if (ret) {
			return true;
		} else {
			alert('No has seleccionado ninguna web');
			return false;
		}
	});

	$('table.table').tablesorter({sortList: [[1,1]]});

	$.each($('td'), function() {
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
	});

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
			$('.more_options').show();
		} else {
			$('.more_options').hide();
		}
	});

	$('.compare').click(function() {
		$(this).hide();
		$('.compare_fields').show();
		$('.compare_fields').find('input').attr('disabled', false);
		return false;
	});

	$('.remove_compare').click(function() {
		$('.compare').show();
		$('.compare_fields').hide();
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
