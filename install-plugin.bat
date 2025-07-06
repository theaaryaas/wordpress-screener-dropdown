@echo off
echo WordPress Screener Plugin Installer
echo ===================================
echo.
echo This script will help you install the plugin to your WordPress site.
echo.
echo Please enter the path to your WordPress site's wp-content/plugins/ directory.
echo Example: C:\Users\YourName\Local Sites\MySite\app\public\wp-content\plugins\
echo.
set /p plugin_path="Enter the plugins directory path: "

if not exist "%plugin_path%" (
    echo Error: Directory does not exist!
    pause
    exit /b 1
)

echo.
echo Creating screener-dropdown directory...
if not exist "%plugin_path%\screener-dropdown" mkdir "%plugin_path%\screener-dropdown"

echo Copying plugin files...
copy "screener-dropdown.php" "%plugin_path%\screener-dropdown\"
copy "README.md" "%plugin_path%\screener-dropdown\"

echo Creating data directory...
if not exist "%plugin_path%\screener-dropdown\data" mkdir "%plugin_path%\screener-dropdown\data"

echo Copying data files...
copy "data\screener_list.csv" "%plugin_path%\screener-dropdown\data\"
copy "data\screener_data.csv" "%plugin_path%\screener-dropdown\data\"

echo.
echo Plugin installation complete!
echo.
echo Next steps:
echo 1. Open your WordPress admin panel
echo 2. Go to Plugins ^> Installed Plugins
echo 3. Find "Screener Dropdown" and click "Activate"
echo 4. Create a page and add the shortcode: [screener-dropdown]
echo.
pause 