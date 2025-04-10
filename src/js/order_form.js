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

      // Get the total price and discount
      const totalPrice = document.getElementById('total-price').innerText;
      const discount = document.getElementById('discount').value || 0;

      // Collect product quantities and ids
      const productQuantities = [];
      document.querySelectorAll('.quantity-input').forEach(input => {
          const productId = input.getAttribute('data-product-id');
          const quantity = input.value || 0;
          if (quantity > 0) {
              productQuantities.push({ productId, quantity });
          }
      });

      // Append totalPrice, discount, and product quantities to formData
      const formData = $('#order-form').serialize() + `&total_price=${totalPrice}&discount=${discount}&products=${JSON.stringify(productQuantities)}`;

      // Initialize the products array
      let products = [];

      // Loop through all the product quantity inputs to collect selected quantities
      document.querySelectorAll('.quantity-input').forEach(input => {
        const productId = input.getAttribute('data-product-id'); // Get the product ID from the data attribute
        const quantity = parseInt(input.value) || 0; // Get the quantity from the input value
        const price = parseFloat(input.getAttribute('data-price')); // Get the price from the data attribute
        const discount = parseFloat(document.getElementById('discount').value) || 0; // Get the discount from the input field

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

      // Log the products array to ensure it is populated correctly
      console.log('Products:', products);

      $.ajax({
        url: '../../src/php/orderform.php', 
        type: 'POST',
        data: formData, products: JSON.stringify(products), // Send the serialized form data
        dataType: 'json', // Expecting a JSON response
        success: function(response) {
            console.log('Raw response:', response); // Log the raw response
    
            if (response.success) {
                displaySuccessMessage('Your order has been placed successfully!');
                $('#order-form')[0].reset(); // Reset form
            } else {
                displayErrors(response.errors); // Display validation errors
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            alert('An error occurred while submitting the form. Please check the console for more details.');
            console.log(xhr.responseText);  // Log the raw response from the server
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

// Call calculateTotal whenever there's a change in quantity or discount
document.querySelectorAll('.quantity-input').forEach(input => {
  input.addEventListener('change', calculateTotal);
});

document.getElementById('discount').addEventListener('input', calculateTotal);