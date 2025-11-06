/**
 * Password Toggle Script
 * Shows/hides password input when user clicks the eye icon
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all password input fields with class 'password-input'
    const passwordInputs = document.querySelectorAll('.password-input');

    passwordInputs.forEach(function(input) {
        // Create container for input and toggle button
        const container = input.parentElement;

        // Create the toggle button (eye icon)
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn btn-outline-secondary password-toggle-btn';
        toggleBtn.style.position = 'absolute';
        toggleBtn.style.right = '10px';
        toggleBtn.style.top = '50%';
        toggleBtn.style.transform = 'translateY(-50%)';
        toggleBtn.style.border = 'none';
        toggleBtn.style.background = 'none';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';

        // Wrap input in position relative container
        container.style.position = 'relative';
        container.appendChild(toggleBtn);

        // Toggle password visibility on button click
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const icon = toggleBtn.querySelector('i');

            if (input.type === 'password') {
                // Show password
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                toggleBtn.title = 'Hide password';
            } else {
                // Hide password
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                toggleBtn.title = 'Show password';
            }
        });
    });
});
