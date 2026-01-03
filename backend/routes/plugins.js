const express = require('express');
const router = express.Router();
const { body, validationResult } = require('express-validator');
const Plugin = require('../models/Plugin');
const { verifyToken } = require('../middleware/auth');

// @route   GET /api/plugins
// @desc    Get all approved plugins
// @access  Public
router.get('/', async (req, res) => {
  try {
    const { category, search, featured } = req.query;

    let query = { status: { $in: ['approved', 'instruction', 'download', 'installed'] } };

    if (category && category !== 'all') {
      query.category = category;
    }

    if (featured === 'true') {
      query.featured = true;
    }

    if (search) {
      query.$or = [
        { name: { $regex: search, $options: 'i' } },
        { description: { $regex: search, $options: 'i' } },
        { tag: { $regex: search, $options: 'i' } }
      ];
    }

    const plugins = await Plugin.find(query).sort({ featured: -1, downloads: -1, createdAt: -1 });
    res.json(plugins);
  } catch (error) {
    console.error('Get plugins error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   GET /api/plugins/:id
// @desc    Get single plugin
// @access  Public
router.get('/:id', async (req, res) => {
  try {
    const plugin = await Plugin.findById(req.params.id);

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    res.json(plugin);
  } catch (error) {
    console.error('Get plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   POST /api/plugins
// @desc    Submit new plugin
// @access  Private
router.post('/', verifyToken, [
  body('name').trim().notEmpty().withMessage('Name is required'),
  body('category').isIn(['core', 'streaming', 'smarthome', 'control', 'creative']).withMessage('Invalid category'),
  body('description').trim().notEmpty().withMessage('Description is required'),
  body('tag').trim().notEmpty().withMessage('Tag is required')
], async (req, res) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }

  try {
    const pluginData = {
      ...req.body,
      authorId: req.user._id,
      author: req.body.author || req.user.username,
      status: 'pending'
    };

    const plugin = new Plugin(pluginData);
    await plugin.save();

    res.status(201).json({ message: 'Plugin submitted for review', plugin });
  } catch (error) {
    console.error('Create plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   PUT /api/plugins/:id
// @desc    Update plugin
// @access  Private (owner or admin)
router.put('/:id', verifyToken, async (req, res) => {
  try {
    const plugin = await Plugin.findById(req.params.id);

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    // Check if user is owner or admin
    if (plugin.authorId.toString() !== req.user._id.toString() && req.user.role !== 'admin') {
      return res.status(403).json({ message: 'Access denied' });
    }

    const updatedPlugin = await Plugin.findByIdAndUpdate(
      req.params.id,
      { ...req.body, updatedAt: Date.now() },
      { new: true }
    );

    res.json(updatedPlugin);
  } catch (error) {
    console.error('Update plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   DELETE /api/plugins/:id
// @desc    Delete plugin
// @access  Private (owner or admin)
router.delete('/:id', verifyToken, async (req, res) => {
  try {
    const plugin = await Plugin.findById(req.params.id);

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    // Check if user is owner or admin
    if (plugin.authorId.toString() !== req.user._id.toString() && req.user.role !== 'admin') {
      return res.status(403).json({ message: 'Access denied' });
    }

    await Plugin.findByIdAndDelete(req.params.id);
    res.json({ message: 'Plugin deleted' });
  } catch (error) {
    console.error('Delete plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   POST /api/plugins/:id/download
// @desc    Increment download count
// @access  Public
router.post('/:id/download', async (req, res) => {
  try {
    const plugin = await Plugin.findByIdAndUpdate(
      req.params.id,
      { $inc: { downloads: 1 } },
      { new: true }
    );

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    res.json({ downloads: plugin.downloads });
  } catch (error) {
    console.error('Download increment error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

module.exports = router;
