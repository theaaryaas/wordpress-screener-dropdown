# Quick Installation Guide

## Step 1: Install LocalWP
1. Download LocalWP from https://localwp.com/
2. Install and create a new WordPress site
3. Note the site directory path

## Step 2: Install the Plugin
1. Navigate to your WordPress site directory
2. Go to `wp-content/plugins/`
3. Create a new folder called `screener-dropdown`
4. Copy all files from this project into that folder:
   - `screener-dropdown.php`
   - `data/` folder (with both CSV files)
   - `README.md`

## Step 3: Activate the Plugin
1. Open your WordPress admin panel
2. Go to Plugins → Installed Plugins
3. Find "Screener Dropdown" and click "Activate"

## Step 4: Test the Plugin
1. Create a new page or post
2. Add the shortcode: `[screener-dropdown]`
3. Publish and view the page
4. You should see the screener interface with:
   - Add Filter button
   - Dropdown for field selection
   - Results table

## Troubleshooting
- If data doesn't load, check browser console for errors
- Ensure CSV files are in the `data/` folder
- Verify plugin is activated in WordPress admin

## Video Recording Tips
For your video walkthrough:
1. Show LocalWP installation and WordPress site creation
2. Demonstrate plugin installation process
3. Show plugin activation in WordPress admin
4. Create a page with the shortcode
5. Demonstrate the filtering functionality:
   - Add multiple filters
   - Show different operators
   - Delete filters in any order
   - Show table sorting and export features
6. Emphasize that it's a WordPress plugin with infinite filtering capability 