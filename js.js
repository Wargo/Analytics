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
		if ($(this).text() >= 1000) {
			$(this).text($(this).text().replace(/(^\d{1,3}|\d{3})(?=(?:\d{3})+(?:$|\.))/g, '$1.'));
		} else if($(this).text() < 1000) { // Al menos sea número
			$(this).text($(this).text().replace('.', ','));
		}

		if ($(this).attr('var')) {
			if ($(this).attr('var') > $(this).text()) {
				if ($(this).hasClass('inverse')) { // Para % de rebote
					$(this).append(' <pan class="green">(' + $(this).attr('var') + ')</span>');
				} else {
					$(this).append(' <pan class="red">(' + $(this).attr('var') + ')</span>');
				}
			} else {
				if ($(this).hasClass('inverse')) { // Para % de rebote
					$(this).append(' <pan class="red">(' + $(this).attr('var') + ')</span>');
				} else {
					$(this).append(' <pan class="green">(' + $(this).attr('var') + ')</span>');
				}
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
