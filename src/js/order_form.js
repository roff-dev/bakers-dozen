// Function to calculate the total price based on selected quantities and apply discount
function calculateTotal() {
  let totalPrice = 0;
  
  // Loop through all the quantity inputs
  document.querySelectorAll('.quantity-input').forEach(input => {
    const price = parseFloat(input.getAttribute('data-price')); // Retrieve price from data-price attribute
    const quantity = parseInt(input.value) || 0; // Default to 0 if not a number

    // Debugging: Log the price and quantity values
    //console.log(`Price: ${price}, Quantity: ${quantity}`);
    
    if (!isNaN(price) && !isNaN(quantity)) {
        totalPrice += price * quantity;  // Only add if both price and quantity are valid numbers
    }
  });

  // Get the discount value (percentage)
  let discount = parseFloat(document.getElementById('discount').value) || 0;

  // Calculate the discount amount
  let discountAmount = (totalPrice * discount) / 100;

  // Subtract the discount from the total price
  let finalPrice = totalPrice - discountAmount;

  // Update the total price on the page
  document.getElementById('total-price').innerText = finalPrice.toFixed(2);

  // Update the hidden input that PHP will use
  document.getElementById('total-price-input').value = finalPrice.toFixed(2);
}

// Validate discount to ensure it's between 0 and 100
function validateDiscount() {
  let discount = parseFloat(document.getElementById('discount').value) || 0;
  if (discount < 0 || discount > 100) {
      alert("Discount must be between 0 and 100.");
      return false;
  }
  return true;
}

// Validate form to ensure products are selected and other fields are filled out
function validateForm() {
  let isValid = true;

  // Get the form fields
  let nameForm = document.forms["orderForm"]["name"];
  let addressForm = document.forms["orderForm"]["address"];
  let phonenumberForm = document.forms["orderForm"]["telephone"];
  let emailaddressForm = document.forms["orderForm"]["email"];
  let notesForm = document.forms["orderForm"]["notes"];
  let totalPriceForm = document.getElementById("total-price").innerText;

  // Reset all previous errors
  $('.form-control').removeClass('has-error');

  // Validate Name
  if (nameForm.value == "") {
    $(nameForm).addClass('has-error');
    isValid = false;
  }

  // Validate Address
  if (addressForm.value == "") {
    $(addressForm).addClass('has-error');
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

  // Validate Notes
  if (notesForm.value == "") {
    $(notesForm).addClass('has-error');
    isValid = false;
  }

  // Validate Total Price (Ensure total price is greater than 0)
  if (parseFloat(totalPriceForm) <= 0) {
    $('#total-price').addClass('has-error');
    isValid = false;
  }

  // Ensure at least one product is selected
  let productSelected = false;
  document.querySelectorAll('.quantity-input').forEach(input => {
    const quantity = parseInt(input.value) || 0;
    if (quantity > 0) {
      productSelected = true;
    }
  });

  if (!productSelected) {
    alert("Please select at least one product.");
    isValid = false;
  }

  return isValid;
}

// Correctly define the submit button
const $submitBtn = $('#submitBtn');

// Listen for the submit button click
$submitBtn.on('click', function(event) {
  // First, run JavaScript form validation
  if (validateForm() && validateDiscount()) {
      // If the form is valid, prevent the default form submission
      event.preventDefault();

      // Get form values
      const name = $('#name').val();
      const address = $('#address').val();
      const email = $('#email').val();
      const telephone = $('#telephone').val();
      const notes = $('#notes').val();
      const totalPrice = document.getElementById('total-price').innerText;
      const discount = document.getElementById('discount').value || 0;

      // Initialize the products array
      let products = [];

      // Collect product data
      document.querySelectorAll('.quantity-input').forEach(input => {
        const productId = input.getAttribute('data-product-id');
        const quantity = parseInt(input.value) || 0;
        const price = parseFloat(input.getAttribute('data-price'));

        // Only include products with a quantity greater than 0
        if (quantity > 0) {
          products.push({
            product_id: productId,  
            quantity: quantity,
            price: price,
            discount: discount
          });
        }
      });

      // Create the form data object
      const formData = new FormData();
      formData.append('name', name);
      formData.append('address', address);
      formData.append('email', email);
      formData.append('telephone', telephone);
      formData.append('notes', notes);
      formData.append('total_price', totalPrice);
      formData.append('discount', discount);
      formData.append('products', JSON.stringify(products));

      // Log what we're about to send for debugging
      console.log('Sending order with products:', products);

      // Send the AJAX request
      $.ajax({
        url: '../src/php/orderform.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Server response:', response);
            
            if (response.success) {
                displaySuccessMessage('Order placed successfully! Order ID: ' + response.order_id);
                $('#order-form')[0].reset();
                calculateTotal(); // Reset the total display
            } else {
                if (response.errors) {
                    displayErrors(response.errors);
                } else if (response.error) {
                    displayErrors({'general': response.error});
                } else {
                    displayErrors({'general': 'An unknown error occurred'});
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            console.log('Response text:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    displayErrors({'general': response.error});
                } else {
                    displayErrors({'general': 'Server error: ' + error});
                }
            } catch (e) {
                displayErrors({'general': 'Could not process server response: ' + error});
            }
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
        if (field === 'general') {
            // Display general errors at the top
            errorHtml += `<p class="alert-danger-active">${errors[field]}</p>`;
        } else {
            // Highlight specific form fields with errors
            errorHtml += `<p class="alert-danger-active">${errors[field]}</p>`;
            $(`#${field}`).addClass('has-error');
        }
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

// Call calculateTotal whenever there's a change in quantity or discount
document.querySelectorAll('.quantity-input').forEach(input => {
  input.addEventListener('change', calculateTotal);
});

document.getElementById('discount').addEventListener('input', calculateTotal);