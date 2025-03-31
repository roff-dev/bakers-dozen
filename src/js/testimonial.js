document.addEventListener('DOMContentLoaded', function() {
    // storing the form in the form variable
    const form = document.getElementById('testimonial-form');
    
    // check if the form exists
    if (form) {
        // listens for the submit button to be pressed with the e event listner in function(e)
        form.addEventListener('submit', function(e) {
            // stops the form from reloading the page
            e.preventDefault();
            
            // reset previous errors and clears old validation messages
            resetErrors();
            
            // collects all info input values from the form as key value pairs
            const formData = new FormData(form);
            
            // Submit form data to the server using fetch API
            // form.action sends to the submit_testimonial.php
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            // converts the json format to a javaScript object
            .then(response => response.json())
            .then(data => {
                // if success = true after php validations continue
                if (data.success) {
                    // show success message
                    showSuccessMessage('Thank you for giving your feedback!');
                    
                    // update reviews container with new random set
                    updateReviews(data.reviews);
                    
                    // Clear form
                    form.reset(); 
                } else {
                    // show validation errors
                    showErrors(data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    // function to reset all error messages
    function resetErrors() {
        document.querySelectorAll('.error-input').forEach(el => {
            el.classList.remove('error-input');
        });
        
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }
    
    // function to show errors
    function showErrors(errors) {
        if (errors.name) {
            document.getElementById('name').classList.add('error-input');
            const nameError = document.getElementById('name-error');
            nameError.textContent = errors.name;
            nameError.classList.remove('hidden');
        }
        
        if (errors.testimonial) {
            document.getElementById('testimonial-textarea').classList.add('error-input');
            const testimonialError = document.getElementById('testimonial-error');
            testimonialError.textContent = errors.testimonial;
            testimonialError.classList.remove('hidden');
        }
    }
    
    // function to show success message
    function showSuccessMessage(message) {
        const container = document.querySelector('.testimonial-error-container');
        container.innerHTML = `<div class="message success-message">${message}</div>`;
        container.style.display = 'block';
        
        // hide message after 5 seconds
        setTimeout(() => {
            container.style.display = 'none';
        }, 5000);
    }
    
    // function to update reviews on the page
    function updateReviews(reviews) {
        const reviewsContainer = document.querySelector('.testimonials');
        
        // clear existing reviews
        reviewsContainer.innerHTML = '';
        
        if (reviews.length === 0) {
            // show "No testimonials" message if no reviews
            reviewsContainer.innerHTML = '<p>No testimonials yet</p>';
        } else {
            // add new reviews by creating the element. The php restricts the page to 5 random reviews from the database
            reviews.forEach(review => {
                const newReview = document.createElement('div');
                newReview.className = 'review';
                newReview.innerHTML = `
                    <p>"${escapeHtml(review.content)}"</p>
                    <h4>- ${escapeHtml(review.name)}</h4>
                `;
                reviewsContainer.appendChild(newReview);
            });
        }
    }
    
    // helper function to escape HTML and prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});