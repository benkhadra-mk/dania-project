/**
 * WellCare Application JavaScript
 * Handles forms, AJAX requests, and user interactions
 */

// API base URL
const API_BASE = '/dania-project/api';

// Show loading spinner
function showLoading() {
    // You can implement a loading spinner here
}

// Hide loading spinner
function hideLoading() {
    // You can hide the loading spinner here
}

// Show message to user
function showMessage(message, type = 'success') {
    alert(message); // Simple alert for now, you can improve this with better UI
}

// Make AJAX request
async function apiRequest(url, method = 'GET', data = null) {
    try {
        showLoading();

        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        };

        if (data && method === 'POST') {
            options.body = new URLSearchParams(data);
        }

        const response = await fetch(url, options);
        const result = await response.json();

        hideLoading();
        return result;

    } catch (error) {
        hideLoading();
        console.error('API request failed:', error);
        return { success: false, message: 'حدث خطأ في الاتصال' };
    }
}

// Register user
async function registerUser(formData) {
    const result = await apiRequest(`${API_BASE}/auth/register.php`, 'POST', formData);
    return result;
}

// Login user
async function loginUser(formData) {
    const result = await apiRequest(`${API_BASE}/auth/login.php`, 'POST', formData);
    return result;
}

// Logout user
async function logoutUser() {
    const result = await apiRequest(`${API_BASE}/auth/logout.php`, 'POST');
    return result;
}

// Check authentication status
async function checkAuth() {
    const result = await apiRequest(`${API_BASE}/auth/check-auth.php`);
    return result;
}

// Get content
async function getContent(type = null, slug = null) {
    let url = `${API_BASE}/content/get-content.php`;
    const params = new URLSearchParams();
    if (type) params.append('type', type);
    if (slug) params.append('slug', slug);
    if (params.toString()) url += '?' + params.toString();

    const result = await apiRequest(url);
    return result;
}

// Add comment
async function addComment(contentId, commentText) {
    const result = await apiRequest(`${API_BASE}/comments/add-comment.php`, 'POST', {
        content_id: contentId,
        comment_text: commentText
    });
    return result;
}

// Get comments
async function getComments(contentId) {
    const result = await apiRequest(`${API_BASE}/comments/get-comments.php?content_id=${contentId}`);
    return result;
}

// Toggle like
async function toggleLike(contentId) {
    const result = await apiRequest(`${API_BASE}/likes/toggle-like.php`, 'POST', {
        content_id: contentId
    });
    return result;
}

// Validate email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate password
function isValidPassword(password) {
    return password.length >= 6;
}

// Form validation helper
function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#FF6B6B';
        } else {
            input.style.borderColor = '';
        }
    });

    return isValid;
}

// Initialize theme from localStorage or session
function initializeTheme() {
    const savedTheme = localStorage.getItem('wellcare-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        document.documentElement.classList.add('dark-theme');
    }
}

// Toggle theme
function toggleTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
        document.documentElement.classList.add('dark-theme');
        localStorage.setItem('wellcare-theme', 'dark');
    } else {
        document.body.classList.remove('dark-theme');
        document.documentElement.classList.remove('dark-theme');
        localStorage.setItem('wellcare-theme', 'light');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeTheme();
});
