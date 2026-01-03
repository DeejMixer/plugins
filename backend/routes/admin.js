const express = require('express');
const router = express.Router();
const Plugin = require('../models/Plugin');
const User = require('../models/User');
const { verifyToken, isAdmin } = require('../middleware/auth');

// All routes require authentication and admin role
router.use(verifyToken, isAdmin);

// @route   GET /api/admin/plugins
// @desc    Get all plugins (including pending)
// @access  Admin
router.get('/plugins', async (req, res) => {
  try {
    const { status, category } = req.query;

    let query = {};

    if (status && status !== 'all') {
      query.status = status;
    }

    if (category && category !== 'all') {
      query.category = category;
    }

    const plugins = await Plugin.find(query)
      .populate('authorId', 'username email')
      .sort({ createdAt: -1 });

    res.json(plugins);
  } catch (error) {
    console.error('Admin get plugins error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   PUT /api/admin/plugins/:id/approve
// @desc    Approve plugin
// @access  Admin
router.put('/plugins/:id/approve', async (req, res) => {
  try {
    const plugin = await Plugin.findByIdAndUpdate(
      req.params.id,
      { status: 'approved' },
      { new: true }
    );

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    res.json({ message: 'Plugin approved', plugin });
  } catch (error) {
    console.error('Approve plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   PUT /api/admin/plugins/:id/reject
// @desc    Reject plugin
// @access  Admin
router.put('/plugins/:id/reject', async (req, res) => {
  try {
    const plugin = await Plugin.findByIdAndUpdate(
      req.params.id,
      { status: 'rejected' },
      { new: true }
    );

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    res.json({ message: 'Plugin rejected', plugin });
  } catch (error) {
    console.error('Reject plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   PUT /api/admin/plugins/:id/feature
// @desc    Toggle plugin featured status
// @access  Admin
router.put('/plugins/:id/feature', async (req, res) => {
  try {
    const plugin = await Plugin.findById(req.params.id);

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    plugin.featured = !plugin.featured;
    await plugin.save();

    res.json({ message: `Plugin ${plugin.featured ? 'featured' : 'unfeatured'}`, plugin });
  } catch (error) {
    console.error('Feature plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   DELETE /api/admin/plugins/:id
// @desc    Delete any plugin
// @access  Admin
router.delete('/plugins/:id', async (req, res) => {
  try {
    const plugin = await Plugin.findByIdAndDelete(req.params.id);

    if (!plugin) {
      return res.status(404).json({ message: 'Plugin not found' });
    }

    res.json({ message: 'Plugin deleted' });
  } catch (error) {
    console.error('Delete plugin error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   GET /api/admin/users
// @desc    Get all users
// @access  Admin
router.get('/users', async (req, res) => {
  try {
    const users = await User.find().select('-password').sort({ createdAt: -1 });
    res.json(users);
  } catch (error) {
    console.error('Get users error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   PUT /api/admin/users/:id/role
// @desc    Change user role
// @access  Admin
router.put('/users/:id/role', async (req, res) => {
  try {
    const { role } = req.body;

    if (!['user', 'admin'].includes(role)) {
      return res.status(400).json({ message: 'Invalid role' });
    }

    const user = await User.findByIdAndUpdate(
      req.params.id,
      { role },
      { new: true }
    ).select('-password');

    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    res.json({ message: 'User role updated', user });
  } catch (error) {
    console.error('Update user role error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   DELETE /api/admin/users/:id
// @desc    Delete user
// @access  Admin
router.delete('/users/:id', async (req, res) => {
  try {
    // Don't allow admin to delete themselves
    if (req.params.id === req.user._id.toString()) {
      return res.status(400).json({ message: 'Cannot delete your own account' });
    }

    const user = await User.findByIdAndDelete(req.params.id);

    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    res.json({ message: 'User deleted' });
  } catch (error) {
    console.error('Delete user error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

// @route   GET /api/admin/stats
// @desc    Get dashboard statistics
// @access  Admin
router.get('/stats', async (req, res) => {
  try {
    const totalPlugins = await Plugin.countDocuments();
    const approvedPlugins = await Plugin.countDocuments({ status: 'approved' });
    const pendingPlugins = await Plugin.countDocuments({ status: 'pending' });
    const totalUsers = await User.countDocuments();
    const totalDownloads = await Plugin.aggregate([
      { $group: { _id: null, total: { $sum: '$downloads' } } }
    ]);

    res.json({
      totalPlugins,
      approvedPlugins,
      pendingPlugins,
      totalUsers,
      totalDownloads: totalDownloads[0]?.total || 0
    });
  } catch (error) {
    console.error('Get stats error:', error);
    res.status(500).json({ message: 'Server error' });
  }
});

module.exports = router;
