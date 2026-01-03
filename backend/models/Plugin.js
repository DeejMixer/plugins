const mongoose = require('mongoose');

const pluginSchema = new mongoose.Schema({
  name: {
    type: String,
    required: true,
    trim: true
  },
  category: {
    type: String,
    required: true,
    enum: ['core', 'streaming', 'smarthome', 'control', 'creative']
  },
  tag: {
    type: String,
    required: true
  },
  status: {
    type: String,
    enum: ['instruction', 'download', 'installed', 'pending', 'approved', 'rejected'],
    default: 'pending'
  },
  author: {
    type: String,
    required: true
  },
  authorId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User'
  },
  socialUrl: {
    type: String,
    default: null
  },
  description: {
    type: String,
    required: true
  },
  imageColor: {
    type: String,
    default: 'from-blue-600 to-indigo-600'
  },
  icon: {
    type: String,
    default: 'fa-puzzle-piece'
  },
  downloadUrl: {
    type: String,
    default: null
  },
  instructionUrl: {
    type: String,
    default: null
  },
  devices: {
    type: [String],
    default: ['Mixlar Mix']
  },
  version: {
    type: String,
    default: '1.0.0'
  },
  downloads: {
    type: Number,
    default: 0
  },
  featured: {
    type: Boolean,
    default: false
  },
  createdAt: {
    type: Date,
    default: Date.now
  },
  updatedAt: {
    type: Date,
    default: Date.now
  }
});

// Update timestamp on save
pluginSchema.pre('save', function(next) {
  this.updatedAt = Date.now();
  next();
});

module.exports = mongoose.model('Plugin', pluginSchema);
