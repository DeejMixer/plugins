// Protect admin page
document.addEventListener('DOMContentLoaded', () => {
  requireAdmin();
  loadStats();
  loadPlugins();
  loadUsers();
  setupTabs();
  setupFilters();
  displayUserInfo();
});

// Display user info
function displayUserInfo() {
  const user = getCurrentUser();
  if (user) {
    document.getElementById('userAvatar').textContent = user.username.charAt(0).toUpperCase();
    document.getElementById('userName').textContent = user.username;
  }
}

// Load dashboard stats
async function loadStats() {
  try {
    const response = await authenticatedFetch('/api/admin/stats.php');
    const stats = await response.json();

    document.getElementById('statTotalPlugins').textContent = stats.totalPlugins;
    document.getElementById('statApprovedPlugins').textContent = stats.approvedPlugins;
    document.getElementById('statPendingPlugins').textContent = stats.pendingPlugins;
    document.getElementById('statTotalUsers').textContent = stats.totalUsers;
    document.getElementById('statTotalDownloads').textContent = stats.totalDownloads.toLocaleString();
  } catch (error) {
    console.error('Error loading stats:', error);
  }
}

// Load plugins
async function loadPlugins() {
  try {
    const status = document.getElementById('statusFilter').value;
    const category = document.getElementById('categoryFilter').value;

    let url = '/api/admin/plugins.php?';
    if (status !== 'all') url += `status=${status}&`;
    if (category !== 'all') url += `category=${category}`;

    const response = await authenticatedFetch(url);
    const plugins = await response.json();

    renderPluginsTable(plugins);
  } catch (error) {
    console.error('Error loading plugins:', error);
  }
}

// Render plugins table
function renderPluginsTable(plugins) {
  const tbody = document.getElementById('pluginsTableBody');

  if (plugins.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
          No plugins found
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = plugins.map(plugin => `
    <tr>
      <td>
        <strong>${plugin.name}</strong><br>
        <small style="color: var(--text-muted);">v${plugin.version}</small>
      </td>
      <td><span class="tag">${plugin.category}</span></td>
      <td>
        ${plugin.author}<br>
        <small style="color: var(--text-muted);">${plugin.authorId?.email || 'N/A'}</small>
      </td>
      <td><span class="status-${plugin.status}">${plugin.status}</span></td>
      <td>${plugin.downloads || 0}</td>
      <td>
        <div class="actions">
          ${plugin.status === 'pending' ? `
            <button class="btn btn-success btn-sm" onclick="approvePlugin('${plugin.id}')">
              <i class="fa fa-check"></i> Approve
            </button>
            <button class="btn btn-danger btn-sm" onclick="rejectPlugin('${plugin.id}')">
              <i class="fa fa-times"></i> Reject
            </button>
          ` : ''}
          <button class="btn btn-secondary btn-sm" onclick="toggleFeature('${plugin.id}', ${plugin.featured})">
            <i class="fa fa-star"></i> ${plugin.featured ? 'Unfeature' : 'Feature'}
          </button>
          <button class="btn btn-danger btn-sm" onclick="deletePlugin('${plugin.id}')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

// Load users
async function loadUsers() {
  try {
    const response = await authenticatedFetch('/api/admin/users.php');
    const users = await response.json();
    renderUsersTable(users);
  } catch (error) {
    console.error('Error loading users:', error);
  }
}

// Render users table
function renderUsersTable(users) {
  const tbody = document.getElementById('usersTableBody');

  if (users.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
          No users found
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = users.map(user => `
    <tr>
      <td>
        <strong>${user.username}</strong>
      </td>
      <td>${user.email}</td>
      <td>
        <span class="tag" style="background: ${user.role === 'admin' ? 'var(--danger)' : 'var(--primary)'}">
          ${user.role}
        </span>
      </td>
      <td>${new Date(user.created_at).toLocaleDateString()}</td>
      <td>
        <div class="actions">
          <button class="btn btn-secondary btn-sm" onclick="toggleUserRole('${user.id}', '${user.role}')">
            <i class="fa fa-user-shield"></i>
            ${user.role === 'admin' ? 'Make User' : 'Make Admin'}
          </button>
          <button class="btn btn-danger btn-sm" onclick="deleteUser('${user.id}')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

// Approve plugin
async function approvePlugin(pluginId) {
  if (!confirm('Approve this plugin?')) return;

  try {
    await authenticatedFetch(`/api/admin/approve.php?id=${pluginId}`, {
      method: 'PUT',
    });
    loadPlugins();
    loadStats();
  } catch (error) {
    console.error('Error approving plugin:', error);
    alert('Failed to approve plugin');
  }
}

// Reject plugin
async function rejectPlugin(pluginId) {
  if (!confirm('Reject this plugin?')) return;

  try {
    await authenticatedFetch(`/api/admin/reject.php?id=${pluginId}`, {
      method: 'PUT',
    });
    loadPlugins();
    loadStats();
  } catch (error) {
    console.error('Error rejecting plugin:', error);
    alert('Failed to reject plugin');
  }
}

// Toggle feature status
async function toggleFeature(pluginId, currentStatus) {
  try {
    await authenticatedFetch(`/api/admin/feature.php?id=${pluginId}`, {
      method: 'PUT',
    });
    loadPlugins();
  } catch (error) {
    console.error('Error toggling feature:', error);
    alert('Failed to update plugin');
  }
}

// Delete plugin
async function deletePlugin(pluginId) {
  if (!confirm('Are you sure you want to delete this plugin? This action cannot be undone.')) return;

  try {
    await authenticatedFetch(`/api/admin/delete-plugin.php?id=${pluginId}`, {
      method: 'DELETE',
    });
    loadPlugins();
    loadStats();
  } catch (error) {
    console.error('Error deleting plugin:', error);
    alert('Failed to delete plugin');
  }
}

// Toggle user role
async function toggleUserRole(userId, currentRole) {
  const newRole = currentRole === 'admin' ? 'user' : 'admin';

  if (!confirm(`Change user role to ${newRole}?`)) return;

  try {
    await authenticatedFetch(`/api/admin/change-role.php?id=${userId}`, {
      method: 'PUT',
      body: JSON.stringify({ role: newRole }),
    });
    loadUsers();
    loadStats();
  } catch (error) {
    console.error('Error updating user role:', error);
    alert('Failed to update user role');
  }
}

// Delete user
async function deleteUser(userId) {
  if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;

  try {
    await authenticatedFetch(`/api/admin/delete-user.php?id=${userId}`, {
      method: 'DELETE',
    });
    loadUsers();
    loadStats();
  } catch (error) {
    console.error('Error deleting user:', error);
    alert('Failed to delete user');
  }
}

// Setup tabs
function setupTabs() {
  const tabs = document.querySelectorAll('.tab-btn');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const tabName = tab.dataset.tab;

      // Update active tab
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      // Show/hide content
      document.getElementById('pluginsTab').classList.toggle('hidden', tabName !== 'plugins');
      document.getElementById('usersTab').classList.toggle('hidden', tabName !== 'users');
    });
  });
}

// Setup filters
function setupFilters() {
  document.getElementById('statusFilter').addEventListener('change', loadPlugins);
  document.getElementById('categoryFilter').addEventListener('change', loadPlugins);
}

// Helper functions
function getCurrentUser() {
  const userStr = localStorage.getItem('user');
  return userStr ? JSON.parse(userStr) : null;
}

function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.href = '/login.html';
}

function requireAdmin() {
  const user = getCurrentUser();
  if (!user || user.role !== 'admin') {
    alert('Access denied. Admin only.');
    window.location.href = '/';
  }
}

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

  if (response.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login.html';
    return;
  }

  return response;
}
