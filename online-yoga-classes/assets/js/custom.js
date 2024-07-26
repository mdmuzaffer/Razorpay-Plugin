jQuery(document).ready(function($) {


    /*$.validator.setDefaults({
        submitHandler: function() {
            alert("submitted!");
        }
    });*/




        $.validator.addMethod("noSpaces", function(value, element) {
            return value.trim().length > 0; 
        }, "This field cannot contain only spaces.");

        $("#registration-form-yoga").validate({

            errorClass: 'has-error',
            validClass: 'has-success',
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('help-block');
                element.parents('.form-group').addClass('has-error');
                if (element.prop('type') === 'radio') {
                    error.insertAfter(element.closest('.col-sm-10'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass(errorClass).addClass(validClass);
            },



            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    noSpaces: true
                },
                email: {
                    required: true,
                    email: true
                },
                age: {
                    required: true,
                    number: true,
                    min: 1,
                    max: 120
                },
                gender: {
                    required: true
                },
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },
                address: {
                    required: true
                },
                state: {
                    required: true
                },
                department: {
                    required: true
                },
                month: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Please enter your name",
                    minlength: "Your name must be at least 2 characters long",
                    noSpaces: "This field cannot contain only spaces."
                },
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                age: {
                    required: "Please enter your age",
                    number: "Please enter a valid number",
                    min: "Age must be at least 1",
                    max: "Age must be less than or equal to 120"
                },
                gender: {
                    required: "Please select your gender"
                },
                phone: {
                    required: "Please enter your phone number",
                    minlength: "Phone number must be 10 digits",
                    maxlength: "Phone number must be 10 digits",
                    digits: "Please enter only digits"
                },
                address: {
                    required: "Please enter your address"
                },
                state: {
                    required: "Please select your state"
                },
                department: {
                    required: "Please select your department"
                },
                month: {
                    required: "Please select a month"
                }
            }
        });





   $('#numeric-only').on('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Disable keyboard keys other than numeric
    $('#numeric-only').on('keydown', function(e) {
        var key = e.which || e.keyCode;
        if (!((key >= 48 && key <= 57) || // Allow number keys 0-9
              (key >= 96 && key <= 105) || // Allow numpad number keys 0-9
              key == 8 || // Allow backspace
              key == 9 || // Allow tab
              key == 37 || // Allow left arrow
              key == 39 || // Allow right arrow
              key == 46)) { // Allow delete
            e.preventDefault();
        }
    });

    $('#numericAge').on('keydown', function(e) {
        var key = e.which || e.keyCode;
        if (!((key >= 48 && key <= 57) || // Allow number keys 0-9
              (key >= 96 && key <= 105) || // Allow numpad number keys 0-9
              key == 8 || // Allow backspace
              key == 9 || // Allow tab
              key == 37 || // Allow left arrow
              key == 39 || // Allow right arrow
              key == 46)) { // Allow delete
            e.preventDefault();
        }
    });


    $('#numericAge').on('input', function() {
        var inputValue = $(this).val();
        
        // Remove non-numeric characters
        var numericValue = inputValue.replace(/\D/g, '');
        
        // Limit the input to exactly 2 digits
        if (numericValue.length > 2) {
            numericValue = numericValue.slice(0, 2);
        }
        
        // Update the input value
        $(this).val(numericValue);
    });



});
