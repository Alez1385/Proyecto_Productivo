// auth-debug.js
function logAuthInfo(info) {
    console.log('[Auth Debug]', info);
}

// Function to get and log cookie values
function logCookies() {
    const cookies = document.cookie.split(';').reduce((acc, cookie) => {
        const [name, value] = cookie.trim().split('=');
        acc[name] = value;
        return acc;
    }, {});
    console.log('[Auth Debug] Cookies:', cookies);
}

// Log session storage
function logSessionStorage() {
    console.log('[Auth Debug] SessionStorage:', { ...sessionStorage });
}

// Log when page loads
window.addEventListener('load', () => {
    logAuthInfo('Page loaded');
    logCookies();
    logSessionStorage();
});