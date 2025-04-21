document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const formMessages = document.getElementById('form-messages');

    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Stop the default form submission

            formMessages.innerHTML = '<div class="alert alert-info">Sending...</div>'; // Provide feedback
            formMessages.className = 'mb-3'; // Reset classes

            const formData = new FormData(contactForm);
            const action = contactForm.getAttribute('action');

            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                   'Accept': 'application/json' // Expect JSON response from PHP
                }
            })
            .then(response => response.json()) // Parse JSON response from PHP
            .then(data => {
                if (data.success) {
                    formMessages.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    contactForm.reset(); // Clear the form
                } else {
                    formMessages.innerHTML = `<div class="alert alert-danger">${data.message || 'An error occurred.'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                formMessages.innerHTML = '<div class="alert alert-danger">Could not connect to the server. Please try again later or call us.</div>';
            });
        });
    }
});