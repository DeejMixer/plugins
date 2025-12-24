# Mixlar Plugin Marketplace

A modern, elegant marketplace website for browsing and discovering Mixlar plugins and integrations.

## Features

- **Modern Design**: Clean, professional interface inspired by the Elgato marketplace
- **Category Filtering**: Filter plugins by category (Core, Streaming, Smart Home, Control, Creative)
- **Search Functionality**: Real-time search across plugin names, descriptions, and tags
- **Detailed Plugin Pages**: Comprehensive information pages for each plugin with installation instructions
- **Responsive Layout**: Fully responsive design that works on desktop, tablet, and mobile devices
- **Dynamic Content**: All plugin data loaded from `list.json` for easy updates

## File Structure

```
/plugins
├── index.html           # Main marketplace page
├── plugin.html          # Plugin detail page template
├── styles.css           # All styles and responsive design
├── app.js              # Main marketplace functionality
├── plugin-detail.js    # Plugin detail page functionality
├── list.json           # Plugin data source
└── README.md           # This file
```

## Usage

### Viewing the Marketplace

Simply open `index.html` in a web browser to view the marketplace.

### Adding New Plugins

1. Edit `list.json` to add your new plugin data
2. Follow the existing JSON structure:

```json
{
  "id": 8,
  "name": "Your Plugin Name",
  "category": "core|streaming|smarthome|control|creative",
  "tag": "Your Tag",
  "status": "instruction|download|installed",
  "author": "Author Name",
  "socialUrl": "https://github.com/author",
  "description": "Plugin description",
  "imageColor": "from-color-600 to-color-700",
  "icon": "fa-icon-name",
  "downloadUrl": "https://download-url.com",
  "instructionUrl": "https://docs-url.com",
  "devices": ["Mixlar Mix"],
  "version": "1.0.0"
}
```

### Icon Options

Uses Font Awesome 6.4.0 icons. Available icons include:
- `fa-server`, `fa-desktop`, `fa-video`, `fa-house-signal`
- `fa-sliders`, `fa-pen-ruler`, `fa-headset`
- And many more from Font Awesome library

### Gradient Colors

Supported gradient color combinations:
- `from-slate-700 to-slate-900` - Dark gray
- `from-blue-600 to-indigo-600` - Blue to indigo
- `from-gray-800 to-gray-950` - Very dark gray
- `from-cyan-600 to-blue-700` - Cyan to blue
- `from-emerald-600 to-teal-700` - Green to teal
- `from-fuchsia-700 to-purple-800` - Purple gradient
- `from-orange-600 to-amber-700` - Orange gradient

## Categories

- **core**: Essential plugins for core functionality
- **streaming**: Plugins for streaming and broadcasting
- **smarthome**: Smart home integration plugins
- **control**: Control and automation plugins
- **creative**: Creative workflow and productivity plugins

## Status Types

- **instruction**: Requires setup instructions (shows "Instruction" badge)
- **download**: Available for download (shows "Download" badge)
- **installed**: Already installed (shows "Installed" badge)

## Deployment

To deploy the marketplace:

1. Upload all files to your web server
2. Ensure `list.json` is accessible
3. The marketplace will work with any static file hosting (GitHub Pages, Netlify, Vercel, etc.)

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Technologies Used

- HTML5
- CSS3 (with CSS Grid and Flexbox)
- Vanilla JavaScript (ES6+)
- Font Awesome 6.4.0

## License

Copyright © 2024 MixlarLabs. All rights reserved.
