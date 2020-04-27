jQuery(document).ready(function() {
	var $ = jQuery;
	$("#registration_form_email").bind("change", validateFields);
    $('#form-submit').on('click', function(e) {
    	e.preventDefault();
        validateFields('submit');
    })
	function validateFields(type='') {
		var email = $("#registration_form_email").val();
	    $.post('/validate', {'email': email}, function(data) {
	      var globalErrors = 0;
	      for (var [key, value] of Object.entries(data)) {
	    	if (Object.values(value).length > 0) {
    	      for (var [propertyKey, properyErrors] of Object.entries(value)) {
    	      	  var errors = '';
		    	  properyErrors.forEach(val => {
		    	    errors += '<li>' + val + '</li>';
		    	    globalErrors += 1;
		    	  });
		    	  $('#email-group > small.text-danger').html('<ul>'+errors+'</ul>');
		          $('#email-group > .label').addClass('text-danger');
		          $('#email-group > .form-control').addClass('is-invalid');
    	      }
	       } else {
             $('#email-group > small.text-danger').html('');
             $('#email-group > .label').removeClass('text-danger');
             $('#email-group > .form-control').removeClass('is-invalid');
	       }
	     }
	     if (globalErrors === 0 && type === 'submit') $('#registration-form').submit();
	   });
	}
})