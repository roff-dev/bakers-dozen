document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('testimonial-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous errors
            resetErrors();
            
            // Get form data
            const formData = new FormData(form);
            
            // Submit form via fetch API
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showSuccessMessage('Thank you for giving your feedback!');
                    form.reset(); // Clear form
                } else {
                    // Show validation errors
                    showErrors(data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    // Function to reset all error messages
    function resetErrors() {
        document.querySelectorAll('.error-input').forEach(el => {
            el.classList.remove('error-input');
        });
        
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }
    
    // Function to show errors
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
    
    // Function to show success message
    function showSuccessMessage(message) {
        const container = document.querySelector('.testimonial-error-container');
        container.innerHTML = `<div class="message success-message">${message}</div>`;
        container.style.display = 'block';
        
        // Hide message after 5 seconds
        setTimeout(() => {
            container.style.display = 'none';
        }, 5000);
    }
});