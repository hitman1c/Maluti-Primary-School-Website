function validateForm() {
    const password = document.querySelector('input[name="password"]').value;
    const email = document.querySelector('input[name="email"]').value;
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long');
        return false;
    }
    
    if (!/[A-Z]/.test(password)) {
        alert('Password must contain at least one uppercase letter');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    return true;
}
