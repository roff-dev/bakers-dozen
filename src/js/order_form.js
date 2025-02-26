const $form_TelNo = $('#telephone'); 
const $form_EmailAddress = $('#email');

// Checks if fields are empty in form elements
function validateForm() {
   let isValid = true;

   // Get the form fields
   let firstnameForm = document.forms["contactForm"]["firstname"].value;
   let lastnameForm = document.forms["contactForm"]["lastname"].value;
   let phonenumberForm = document.forms["contactForm"]["telephone"].value;
   let emailaddressForm = document.forms["contactForm"]["email"].value;
   let subjectForm = document.forms["contactForm"]["subject"].value;
   let messageForm = document.forms["contactForm"]["message"].value;


   // Reset all previous errors
   $('.form-control').removeClass('has-error');

   // Validate First Name
   if (firstnameForm.value == "") {
     $(firstnameForm).addClass('has-error');
     isValid = false;
   }

    // Validate Last Name
   if (lastnameForm.value == "") {
     $(lastnameForm).addClass('has-error');
     isValid = false;
   }

   // Validate Phone Number
   if (phonenumberForm.value == "") {
     $(phonenumberForm).addClass('has-error');
     isValid = false;
   }

   // Validate Email Address
   if (emailaddressForm.value == "") {
     $(emailaddressForm).addClass('has-error');
     isValid = false;
   }

  // Validate Email Address
   if (subjectForm.value == "") {
     $(subjectForm).addClass('has-error');
     isValid = false;
   }

   // Validate Message
   if (messageForm.value == "") {
     $(messageForm).addClass('has-error');
     isValid = false;
   }

   // Return if the form is valid or not
   return isValid;
 }

 // Listen for the submit button click
 const $submitBtn = $('#submitBtn');

 $submitBtn.on('click', function(event) {
   // First, run JavaScript form validation
   if (validateForm()) {
     // If the form is valid, prevent the default form submission
     event.preventDefault();

     // Gather the form data
     const formData = $('#contact-form').serialize(); // Serializes the form fields into a query string

     $.ajax({
         url: 'inc/contactform.php', // Make sure this path is correct
         type: 'POST',
         data: formData, // Send the serialized form data
        success: function(response) {
          // Log the response for debugging
          console.log(response);
      
          // Check if the response was successful (PHP validation passed)
           if (response.success) {
             // Display a success message in the alert container
             displaySuccessMessage('Your message has been sent successfully!');

             // Reset the form fields
             $('#contact-form')[0].reset(); 
           } else {
             // If there are errors, display them using the displayErrors function
             displayErrors(response.errors);
           }
         },
         error: function(xhr, status, error) {
           // Handle any errors that occur during the AJAX request
           console.error('Error occurred:', error);
           alert('An error occurred while submitting the form.');
         }
       });
   } else {
     // If JS validation fails, prevent form submission
     event.preventDefault();
   }

 });

 // Function to display errors returned by PHP
  function displayErrors(errors) {
   $('#alert').hide(); // Hide previous error messages

   $('.form-control').removeClass('has-error'); // Reset previous errors

   // Create HTML to display the error messages
   let errorHtml = '';
   for (const field in errors) {
     errorHtml += `<p class="alert-danger-active">${errors[field]}</p>`;
   }

   // Display the errors in the alert-danger div
   $('#alert').html(errorHtml).show();
 }

 // Function to display a success message
 function displaySuccessMessage(message) {
   // Hide any previous messages
   $('.alert.alert-danger-hidden').hide();

   // Show the success message in the alert-danger div
   $('.alert.alert-danger-hidden').removeClass('alert-danger-hidden').addClass('alert alert-success').html(message).show();
 }