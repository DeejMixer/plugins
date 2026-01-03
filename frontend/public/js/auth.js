// Check if user is authenticated
function isAuthenticated() {
  const token = localStorage.getItem('token');
  return !!token;
}

// Get current user
function getCurrentUser() {
  const userStr = localStorage.getItem('user');
  return userStr ? JSON.parse(userStr) : null;
}

// Logout
function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.href = '/login.html';
}

// Make authenticated API requests
async function authenticatedFetch(url, options = {}) {
  const token = localStorage.getItem('token');

  if (!token) {
    window.location.href = '/login.html';
    return;
  }

  const headers = {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`,
    ...options.headers,
  };

  const response = await fetch(url, {
    ...options,
    headers,
  });

  // If unauthorized, redirect to login
  if (response.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login.html';
    return;
  }

  return response;
}

// Protect admin pages
function requireAuth() {
  if (!isAuthenticated()) {
    window.location.href = '/login.html';
  }
}

function requireAdmin() {
  const user = getCurrentUser();
  if (!user || user.role !== 'admin') {
    window.location.href = '/';
  }
}
