let allPlugins = [];
let currentFilter = 'all';
let searchQuery = '';

// Initialize the marketplace
document.addEventListener('DOMContentLoaded', () => {
  setupAuth();
  loadPlugins();
  initializeFilters();
  initializeSearch();
});

// Setup authentication UI
function setupAuth() {
  const navAuth = document.querySelector('.nav-auth');
  const user = getCurrentUser();

  if (user) {
    navAuth.innerHTML = `
      <div class="user-menu">
        <div class="user-info">
          <div class="user-avatar">${user.username.charAt(0).toUpperCase()}</div>
          <span>${user.username}</span>
        </div>
      </div>
      ${user.role === 'admin' ? '<a href="/frontend/public/admin.html" class="btn btn-secondary">Admin</a>' : ''}
      <button class="btn btn-secondary" onclick="logout()">Logout</button>
    `;
  } else {
    navAuth.innerHTML = `
      <a href="/frontend/public/login.html" class="btn btn-secondary">Login</a>
      <a href="/frontend/public/signup.html" class="btn btn-primary">Sign Up</a>
    `;
  }
}

// Load plugins from API
async function loadPlugins() {
  try {
    const response = await fetch('/api/plugins/list.php');
    allPlugins = await response.json();
    updateStats();
    renderPlugins();
  } catch (error) {
    console.error('Error loading plugins:', error);
    showError();
  }
}

// Update statistics
function updateStats() {
  const totalElement = document.getElementById('totalPlugins');
  const downloadsElement = document.getElementById('totalDownloads');

  if (totalElement) {
    totalElement.textContent = allPlugins.length;
  }

  if (downloadsElement) {
    const totalDownloads = allPlugins.reduce((sum, plugin) => sum + (plugin.downloads || 0), 0);
    downloadsElement.textContent = totalDownloads.toLocaleString();
  }
}

// Render plugins to the grid
function renderPlugins() {
  const grid = document.getElementById('pluginsGrid');
  if (!grid) return;

  const filteredPlugins = filterPlugins();

  if (filteredPlugins.length === 0) {
    grid.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--text-secondary);">
        <i class="fa-solid fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
        <p style="font-size: 1.25rem;">No plugins found</p>
        <p>Try adjusting your filters or search query</p>
      </div>
    `;
    return;
  }

  grid.innerHTML = filteredPlugins.map(plugin => createPluginCard(plugin)).join('');

  // Add click handlers
  document.querySelectorAll('.plugin-card').forEach(card => {
    card.addEventListener('click', () => {
      const pluginId = card.dataset.pluginId;
      showPluginDetail(pluginId);
    });
  });
}

// Filter plugins
function filterPlugins() {
  return allPlugins.filter(plugin => {
    const matchesCategory = currentFilter === 'all' || plugin.category === currentFilter;
    const matchesSearch = searchQuery === '' ||
      plugin.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      plugin.description.toLowerCase().includes(searchQuery.toLowerCase()) ||
      plugin.tag.toLowerCase().includes(searchQuery.toLowerCase());

    return matchesCategory && matchesSearch;
  });
}

// Create plugin card HTML
function createPluginCard(plugin) {
  const statusClass = `status-${plugin.status}`;
  const statusLabel = plugin.status.charAt(0).toUpperCase() + plugin.status.slice(1);

  return `
    <div class="plugin-card" data-plugin-id="${plugin.id}">
      <div class="plugin-image" style="background: linear-gradient(135deg, ${getGradientColors(plugin.image_color || plugin.imageColor)});">
        <i class="${plugin.icon}"></i>
        <span class="plugin-status ${statusClass}">${statusLabel}</span>
      </div>
      <div class="plugin-body">
        <div class="plugin-header-info">
          <h3 class="plugin-title">${plugin.name}</h3>
          <div class="plugin-meta">
            <span class="tag">${plugin.tag}</span>
            <span class="version">v${plugin.version}</span>
          </div>
        </div>
        <p class="plugin-description">${plugin.description}</p>
        <div class="plugin-footer">
          <span class="author">
            <i class="fa-solid fa-user"></i>
            ${plugin.author}
          </span>
          <span class="view-details">
            View Details <i class="fa-solid fa-arrow-right"></i>
          </span>
        </div>
      </div>
    </div>
  `;
}

// Convert gradient class to CSS
function getGradientColors(tailwindClass) {
  const gradientMap = {
    'from-slate-700 to-slate-900': 'rgb(51, 65, 85), rgb(15, 23, 42)',
    'from-blue-600 to-indigo-600': 'rgb(37, 99, 235), rgb(79, 70, 229)',
    'from-gray-800 to-gray-950': 'rgb(31, 41, 55), rgb(3, 7, 18)',
    'from-cyan-600 to-blue-700': 'rgb(8, 145, 178), rgb(29, 78, 216)',
    'from-emerald-600 to-teal-700': 'rgb(5, 150, 105), rgb(15, 118, 110)',
    'from-fuchsia-700 to-purple-800': 'rgb(162, 28, 175), rgb(107, 33, 168)',
    'from-orange-600 to-amber-700': 'rgb(234, 88, 12), rgb(180, 83, 9)',
  };

  return gradientMap[tailwindClass] || 'rgb(99, 102, 241), rgb(139, 92, 246)';
}

// Show plugin detail (simple alert for now)
function showPluginDetail(pluginId) {
  const plugin = allPlugins.find(p => p.id == pluginId);
  if (!plugin) return;

  let detailHtml = `
    <div style="padding: 1rem;">
      <h2>${plugin.name}</h2>
      <p><strong>Category:</strong> ${plugin.category}</p>
      <p><strong>Version:</strong> ${plugin.version}</p>
      <p><strong>Author:</strong> ${plugin.author}</p>
      <p><strong>Description:</strong> ${plugin.description}</p>
      <p><strong>Downloads:</strong> ${plugin.downloads || 0}</p>
  `;

  if (plugin.download_url || plugin.downloadUrl) {
    detailHtml += `<p><a href="${plugin.download_url || plugin.downloadUrl}" target="_blank" class="btn btn-primary">Download</a></p>`;
  }

  if (plugin.instruction_url || plugin.instructionUrl) {
    detailHtml += `<p><a href="${plugin.instruction_url || plugin.instructionUrl}" target="_blank" class="btn btn-secondary">View Instructions</a></p>`;
  }

  detailHtml += '</div>';

  alert(`Plugin: ${plugin.name}\n\nCategory: ${plugin.category}\nVersion: ${plugin.version}\nAuthor: ${plugin.author}\n\nDescription: ${plugin.description}`);
}

// Show error
function showError() {
  const grid = document.getElementById('pluginsGrid');
  if (grid) {
    grid.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--text-secondary);">
        <i class="fa-solid fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning-color);"></i>
        <p style="font-size: 1.25rem;">Error loading plugins</p>
        <p>Please try refreshing the page</p>
      </div>
    `;
  }
}

// Initialize filters
function initializeFilters() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(button => {
    button.addEventListener('click', () => {
      filterButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      currentFilter = button.dataset.category;
      renderPlugins();
    });
  });
}

// Initialize search
function initializeSearch() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      renderPlugins();
    });
  }
}

// Helper functions from auth.js
function getCurrentUser() {
  const userStr = localStorage.getItem('user');
  return userStr ? JSON.parse(userStr) : null;
}

function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.href = '/frontend/public/login.html';
}
