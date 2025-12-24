// Mixlar Marketplace - Plugin Detail Page JavaScript

let currentPlugin = null;

// Get plugin ID from URL
function getPluginIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load plugin data
async function loadPluginData() {
    try {
        const response = await fetch('list.json');
        const plugins = await response.json();
        const pluginId = getPluginIdFromURL();

        currentPlugin = plugins.find(p => p.id === parseInt(pluginId));

        if (currentPlugin) {
            renderPluginDetails();
        } else {
            showPluginNotFound();
        }
    } catch (error) {
        console.error('Error loading plugin data:', error);
        showError();
    }
}

// Render plugin details
function renderPluginDetails() {
    if (!currentPlugin) return;

    // Update page title
    document.title = `${currentPlugin.name} - Mixlar Marketplace`;

    // Update hero section
    const pluginHero = document.getElementById('pluginHero');
    if (pluginHero) {
        pluginHero.style.background = `linear-gradient(135deg, ${getGradientColors(currentPlugin.imageColor)})`;
        pluginHero.innerHTML = `<i class="${currentPlugin.icon}"></i>`;
    }

    // Update plugin name
    const pluginName = document.getElementById('pluginName');
    if (pluginName) {
        pluginName.textContent = currentPlugin.name;
    }

    // Update tag
    const pluginTag = document.getElementById('pluginTag');
    if (pluginTag) {
        pluginTag.textContent = currentPlugin.tag;
    }

    // Update version
    const pluginVersion = document.getElementById('pluginVersion');
    if (pluginVersion) {
        pluginVersion.textContent = `v${currentPlugin.version}`;
    }

    // Update author
    const pluginAuthor = document.getElementById('pluginAuthor');
    if (pluginAuthor) {
        pluginAuthor.innerHTML = `<i class="fa-solid fa-user"></i> ${currentPlugin.author}`;
    }

    // Update description
    const pluginDescription = document.getElementById('pluginDescription');
    if (pluginDescription) {
        pluginDescription.textContent = currentPlugin.description;
    }

    // Update full description in about section
    const fullDescription = document.getElementById('fullDescription');
    if (fullDescription) {
        fullDescription.textContent = currentPlugin.description;
    }

    // Update action buttons
    renderActionButtons();

    // Update sidebar info
    updateSidebarInfo();

    // Update devices list
    renderDevicesList();
}

// Render action buttons based on plugin status
function renderActionButtons() {
    const actionButtons = document.getElementById('actionButtons');
    if (!actionButtons) return;

    let buttonsHTML = '';

    if (currentPlugin.status === 'download' && currentPlugin.downloadUrl) {
        buttonsHTML += `
            <a href="${currentPlugin.downloadUrl}" target="_blank" class="btn btn-primary">
                <i class="fa-solid fa-download"></i> Download
            </a>
        `;
    }

    if (currentPlugin.instructionUrl) {
        buttonsHTML += `
            <a href="${currentPlugin.instructionUrl}" target="_blank" class="btn btn-${currentPlugin.downloadUrl ? 'secondary' : 'primary'}">
                <i class="fa-solid fa-book"></i> View Instructions
            </a>
        `;
    }

    if (currentPlugin.status === 'installed') {
        buttonsHTML += `
            <button class="btn btn-primary" disabled style="opacity: 0.6; cursor: not-allowed;">
                <i class="fa-solid fa-check"></i> Installed
            </button>
        `;
    }

    if (currentPlugin.socialUrl) {
        buttonsHTML += `
            <a href="${currentPlugin.socialUrl}" target="_blank" class="btn btn-secondary">
                <i class="fa-solid fa-link"></i> Website
            </a>
        `;
    }

    actionButtons.innerHTML = buttonsHTML;
}

// Update sidebar information
function updateSidebarInfo() {
    // Version
    const sidebarVersion = document.getElementById('sidebarVersion');
    if (sidebarVersion) {
        sidebarVersion.textContent = currentPlugin.version;
    }

    // Category
    const sidebarCategory = document.getElementById('sidebarCategory');
    if (sidebarCategory) {
        const categoryMap = {
            'core': 'Core',
            'streaming': 'Streaming',
            'smarthome': 'Smart Home',
            'control': 'Control',
            'creative': 'Creative'
        };
        sidebarCategory.textContent = categoryMap[currentPlugin.category] || currentPlugin.category;
    }

    // Author
    const sidebarAuthor = document.getElementById('sidebarAuthor');
    if (sidebarAuthor) {
        sidebarAuthor.textContent = currentPlugin.author;
    }

    // Social link
    const socialRow = document.getElementById('socialRow');
    const socialLink = document.getElementById('socialLink');
    if (currentPlugin.socialUrl && socialRow && socialLink) {
        socialRow.style.display = 'flex';
        socialLink.href = currentPlugin.socialUrl;

        // Determine icon based on URL
        let icon = 'fa-link';
        let label = 'Website';
        if (currentPlugin.socialUrl.includes('github.com')) {
            icon = 'fa-github';
            label = 'GitHub';
        } else if (currentPlugin.socialUrl.includes('twitter.com')) {
            icon = 'fa-twitter';
            label = 'Twitter';
        }

        socialLink.innerHTML = `<i class="fa-brands ${icon}"></i> ${label}`;
    } else if (socialRow) {
        socialRow.style.display = 'none';
    }
}

// Render devices list
function renderDevicesList() {
    const deviceList = document.getElementById('deviceList');
    if (!deviceList || !currentPlugin.devices) return;

    deviceList.innerHTML = currentPlugin.devices.map(device => `
        <div class="device-item">
            <i class="fa-solid fa-microchip"></i>
            <span>${device}</span>
        </div>
    `).join('');
}

// Convert Tailwind gradient classes to CSS gradient
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

// Show plugin not found
function showPluginNotFound() {
    document.body.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; flex-direction: column; gap: 1rem;">
            <i class="fa-solid fa-exclamation-triangle" style="font-size: 4rem; color: var(--warning-color);"></i>
            <h1 style="font-size: 2rem;">Plugin Not Found</h1>
            <p style="color: var(--text-secondary);">The plugin you're looking for doesn't exist.</p>
            <a href="index.html" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Marketplace
            </a>
        </div>
    `;
}

// Show error
function showError() {
    document.body.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; flex-direction: column; gap: 1rem;">
            <i class="fa-solid fa-exclamation-circle" style="font-size: 4rem; color: var(--warning-color);"></i>
            <h1 style="font-size: 2rem;">Error Loading Plugin</h1>
            <p style="color: var(--text-secondary);">There was an error loading the plugin data.</p>
            <a href="index.html" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Marketplace
            </a>
        </div>
    `;
}

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    loadPluginData();
});
