require('dotenv').config();
const mongoose = require('mongoose');
const User = require('../models/User');
const Plugin = require('../models/Plugin');
const pluginsData = require('../../list.json');

const connectDB = async () => {
  try {
    await mongoose.connect(process.env.MONGODB_URI);
    console.log('MongoDB connected');
  } catch (error) {
    console.error('MongoDB connection error:', error);
    process.exit(1);
  }
};

const seedDatabase = async () => {
  try {
    await connectDB();

    // Clear existing data
    console.log('Clearing existing data...');
    await User.deleteMany({});
    await Plugin.deleteMany({});

    // Create admin user
    console.log('Creating admin user...');
    const admin = new User({
      username: 'admin',
      email: process.env.ADMIN_EMAIL || 'admin@mixlarlabs.com',
      password: process.env.ADMIN_PASSWORD || 'admin123',
      role: 'admin'
    });
    await admin.save();

    // Create a regular user
    const user = new User({
      username: 'demo_user',
      email: 'user@mixlarlabs.com',
      password: 'user123',
      role: 'user'
    });
    await user.save();

    // Import plugins from list.json
    console.log('Importing plugins...');
    const plugins = pluginsData.map(plugin => ({
      ...plugin,
      _id: undefined,
      authorId: admin._id,
      status: 'approved' // Mark all as approved
    }));

    await Plugin.insertMany(plugins);

    console.log('✓ Database seeded successfully!');
    console.log(`✓ Admin created: ${admin.email}`);
    console.log(`✓ ${plugins.length} plugins imported`);
    console.log('\nYou can now login with:');
    console.log(`Email: ${admin.email}`);
    console.log(`Password: ${process.env.ADMIN_PASSWORD || 'admin123'}`);

    process.exit(0);
  } catch (error) {
    console.error('Seed error:', error);
    process.exit(1);
  }
};

seedDatabase();
