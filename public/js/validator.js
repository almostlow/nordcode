jQuery(document).ready(function() {
    var $ = jQuery;
    $("#registration_form_email").bind("change", function(e) {
        removeErrors();
        e.preventDefault();
        $(this).siblings('.spinner-border').attr('style', 'display: inline-block;');
        validateFields('', $(this).parent());
    });
    $('#form-submit').on('click', function(e) {
        removeErrors();
        e.preventDefault();
        $(this).prop('disabled', true);
        $(this).find('.spinner-border').attr('style', 'display: inline-block;');
        validateFields('submit', $(this));
    })

    function validateFields(type = '', el = null) {
        var email = $("#registration_form_email").val();
        $.post('/validate', {
            'email': email
        }, function(data) {
            var globalErrors = 0;
            for (var [key, value] of Object.entries(data)) {
                if (Object.values(value).length > 0) {
                    for (var [propertyKey, properyErrors] of Object.entries(value)) {
                        var errors = '';
                        properyErrors.forEach(val => {
                            errors += '<li>' + val + '</li>';
                            globalErrors += 1;
                        });
                        $('#email-group > small.text-danger').html('<ul>' + errors + '</ul>');
                        $('#email-group > .label').addClass('text-danger');
                        $('#email-group > .form-control').addClass('is-invalid');
                    }
                } else {
                    $('#email-group > small.text-danger').html('');
                    $('#email-group > .label').removeClass('text-danger');
                    $('#email-group > .form-control').removeClass('is-invalid');
                }
            }
            if (globalErrors === 0 && type === 'submit') {
                $('#registration-form').submit();
            } else if (el && el.length) {
                el.prop('disabled', false);
                el.find('.spinner-border').attr('style', 'display: none;');
            }
        });
    }

    function removeErrors() {
        $('small.text-danger').html('');
        $('.label').removeClass('text-danger');
        $('.form-control').removeClass('is-invalid');
    }
})