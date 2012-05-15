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
		if (!$(this).hasClass('is_time')) {
			$(this).text(format($(this).text()));
		}

		if ($(this).attr('var')) {
			if (!$(this).hasClass('is_time')) {
				var new_value = format(parseFloat($(this).attr('var')));

				if (parseFloat($(this).attr('var')) > current_value) {
					if ($(this).hasClass('inverse')) { // Para % de rebote
						$(this).addClass('green');
					} else {
						$(this).addClass('red');
					}
				} else {
					if ($(this).hasClass('inverse')) { // Para % de rebote
						$(this).addClass('red');
					} else {
						$(this).addClass('green');
					}
				}
				$(this).append(' <span>(' + new_value + ')</span>');

				var num = Math.round(100 * (current_value - parseFloat($(this).attr('var')))) / 100;

				if (num >= 0) {
					$(this).attr('title', '+' + format(num));
				} else {
					$(this).attr('title', format(num));
				}
			} else {
				var new_value = $(this).attr('var');

				if ($(this).attr('title') >= 0) {
					$(this).addClass('green');
				} else {
					$(this).addClass('red');
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
		$(this).remove();
		$('.compare_fields').show();
		return false;
	});

});

function format(s) {
	s = s.toString();
	s = s.replace('.', ',');
	//tsep = '.';
	//dsep = ',';
	//var rx = new RegExp("^(\\d{1,3}(\\"+tsep+"\\d{3})*(\\"+dsep+"\\d+)?|(\\d+))(\\"+dsep+"\\d+)?$");
	//return rx.test(s.replace(/(^\s*|\s*$/,""));
	return s.replace(/(^\d{1,3}|\d{3})(?=(?:\d{3})+(?:$|\.))/g, '$1.');
}
