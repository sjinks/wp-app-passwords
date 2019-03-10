/* global doT, jQuery, wwAppPass */
jQuery(
	function($)
	{
		var context           = $('#ww-app-passwords');
		var app_name          = context.find('#ww-app-pass-name');
		var add_pass_btn      = context.find('#ww-add-new-pass-btn');
		var add_pass_form     = context.find('#create-new-app-pass');
		var app_pass_tbl      = context.find('#ww-app-passwords-table');
		var revoke_all        = app_pass_tbl.find('#ww-app-pass-revoke-all');
		var tbody             = app_pass_tbl.find('tbody');
		var no_items          = tbody.find('tr.no-items');
		var user_id           = $('#user_id').val();

		var tmpl_new_app_pass = doT.template($('#tmpl-ww-new-app-pass').text());
		var tmpl_row          = doT.template($('#tmpl-ww-app-pass-row').text());
		var tmpl_msg          = doT.template($('#tmpl-ww-app-pass-msg').text());

		function addNotice(type, msg)
		{
			context.find('div.notice').remove();
			add_pass_form.before(tmpl_msg({ type: type, message: msg }));
		}

		function fail(xhr, textStatus, errorThrown)
		{
			var msg = errorThrown ? (wwAppPass.ajaxError2 + errorThrown) : wwAppPass.ajaxError;
			addNotice('error', msg);
		}

		add_pass_btn.click(
			function(e)
			{
				e.preventDefault();
				var name = app_name.val();

				if (!name.length){
					app_name.focus();
					return;
				}

				app_name.prop('disabled', true);
				add_pass_btn.prop('disabled', true);

				$.ajax({
					url:        wwAppPass.rest_base + user_id,
					method:     'POST',
					beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', wwAppPass.nonce); },
					data:       { name : name }
				}).done(function(response, status, xhr) {
					wwAppPass.nonce = xhr.getResponseHeader('X-WP-Nonce');
					app_name.prop('disabled', false);
					add_pass_btn.prop('disabled', false);

					add_pass_form.after(tmpl_new_app_pass({ name: name, password: response.password }));
					tbody.prepend(tmpl_row(response.row));

					app_name.val('');
					no_items.remove();
					app_pass_tbl.show();
				}).fail(fail);
			}
		);

		tbody.on('click', '.revoke button', function(e) {
			e.preventDefault();

			if (confirm(wwAppPass.revoke)) {
				var target = $(e.target);
				var tr     = target.closest('tr');
				var slug   = target.data('slug');

				$.ajax({
					url:        wwAppPass.rest_base + user_id + '/' + slug,
					method:     'DELETE',
					beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', wwAppPass.nonce); }
				}).done(function(response, status, xhr) {
					wwAppPass.nonce = xhr.getResponseHeader('X-WP-Nonce');
					if (response) {
						if (!tr.siblings().length) {
							app_pass_tbl.hide();
						}

						tr.remove();
						addNotice('success', wwAppPass.revokeOK);
					}
					else {
						addNotice('error', wwAppPass.revokeError);
					}
				}).fail(fail);
			}
		});

		revoke_all.click(function(e) {
			e.preventDefault();

			if (confirm(wwAppPass.revokeAll)) {
				$.ajax({
					url:        wwAppPass.rest_base + user_id,
					method:     'DELETE',
					beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', wwAppPass.nonce); }
				}).done(function(response, status, xhr) {
					wwAppPass.nonce = xhr.getResponseHeader('X-WP-Nonce');
					if (parseInt(response, 10) > 0) {
						tbody.children().remove();
						app_pass_tbl.hide();
						addNotice('success', wwAppPass.revokeAllOK);
					}
					else {
						addNotice('error', wwAppPass.revokeAllError);
					}
				}).fail(fail);
			}
		});

		$(document).on('click', '.ww-app-pass-modal-dismiss', function(e) {
			e.preventDefault();
			$('.new-application-password.notification-dialog-wrap').hide();
		});

		if (no_items.length > 0) {
			app_pass_tbl.hide();
		}
	}
);
