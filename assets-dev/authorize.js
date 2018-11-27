/* global jQuery, wwAppPass */
jQuery(function($) {
    var auth_form   = $('#authform');
    var reject_btn  = $('#reject');
    var redirect    = $('#redirect_uri').val();
    var appname     = $('#appname').val();
    var form        = $('#redirect-form');

    reject_btn.click(function(e) {
        e.preventDefault();
        if (redirect) {
            var error = $('<input type="hidden" name="error" value="rejected"/>');
            form.attr('action', redirect);
            form.append(error);
            form.submit();
            return;
        }

        window.location.href = wwAppPass.admin_url;
        return;
    });

    auth_form.submit(function(e) {
        e.preventDefault();
    });
});
