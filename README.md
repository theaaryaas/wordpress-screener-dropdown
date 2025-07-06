# Screener Dropdown WordPress Plugin

A WordPress plugin that provides a powerful stock screener with infinite filtering capabilities using Select2 dropdowns and DataTables.

## Features

- **Infinite Filtering**: Add unlimited filter criteria
- **Select2 Dropdowns**: Beautiful, searchable dropdowns for field selection
- **DataTables**: Advanced table display with sorting, searching, and pagination
- **Multiple Operators**: Equals, Not Equals, Contains, Not Contains, Greater Than, Less Than, etc.
- **Responsive Design**: Works on desktop and mobile devices
- **Export Options**: Copy, CSV, and Excel export functionality

## Installation

### Prerequisites
- WordPress 5.0 or higher
- LocalWP (for local development)

### Steps

1. **Install LocalWP**
   - Download from [https://localwp.com/](https://localwp.com/)
   - Install and create a new WordPress site

2. **Install the Plugin**
   - Copy the entire `screener-dropdown` folder to your WordPress site's `wp-content/plugins/` directory
   - The folder structure should be:
     ```
     wp-content/plugins/screener-dropdown/
     ├── screener-dropdown.php
     ├── data/
     │   ├── screener_list.csv
     │   └── screener_data.csv
     └── README.md
     ```

3. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Screener Dropdown" and click "Activate"

4. **Verify Data Files**
   - Ensure `screener_list.csv` and `screener_data.csv` are in the `data/` folder
   - These files contain the field definitions and company data respectively

## Usage

### Shortcode
Use the shortcode `[screener-dropdown]` on any page or post to display the screener.

### Example
```
This is a regular page content.

[screener-dropdown]

More content here.
```

### How to Use the Screener

1. **Add Filters**: Click the "+ Add Filter" button to add new filter criteria
2. **Select Field**: Choose from the dropdown populated with fields from `screener_list.csv`
3. **Choose Operator**: Select an operator (equals, contains, greater than, etc.)
4. **Enter Value**: Type the value to filter by
5. **Remove Filters**: Click "Remove" on any filter row to delete it
6. **View Results**: The table automatically updates to show filtered results

### Available Operators

- **Equals**: Exact match
- **Not Equals**: Exclude exact matches
- **Contains**: Text contains the value
- **Not Contains**: Text does not contain the value
- **Greater Than**: Numeric value is greater than
- **Less Than**: Numeric value is less than
- **Greater or Equal**: Numeric value is greater than or equal to
- **Less or Equal**: Numeric value is less than or equal to

## Data Structure

### screener_list.csv
Contains field definitions with columns:
- `metric`: Field name
- `datatype`: Data type (int, %, string, date)
- `statement`: Category (Balance Sheet, Income Statement, etc.)

### screener_data.csv
Contains company data with columns including:
- Ticker, Name, Company Name, Industry, Sector
- All metrics from screener_list as additional columns

## Customization

### Styling
The plugin includes built-in CSS for a modern, responsive design. You can customize the appearance by:

1. Adding custom CSS to your theme
2. Modifying the styles in the `screener-dropdown.php` file

### Adding New Fields
To add new fields:
1. Update `screener_list.csv` with new field definitions
2. Update `screener_data.csv` with corresponding data columns

## Troubleshooting

### Common Issues

1. **Data not loading**
   - Check that CSV files are in the `data/` folder
   - Verify file permissions (should be readable by web server)
   - Check browser console for JavaScript errors

2. **Filters not working**
   - Ensure field names in filters match exactly with CSV column headers
   - Check that data types match (numeric vs text)

3. **Plugin not appearing**
   - Verify plugin is activated in WordPress admin
   - Check that shortcode `[screener-dropdown]` is used correctly

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Technical Details

### Dependencies
- jQuery (included with WordPress)
- Select2 4.1.0 (loaded from CDN)
- DataTables 1.13.7 (loaded from CDN)

### Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

### Performance
- Data is loaded once on page load
- Filtering is performed client-side for fast response
- Large datasets (>10,000 rows) may experience slower initial load

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review browser console for JavaScript errors
3. Verify data file formats and content

## License

This plugin is provided as-is for educational and demonstration purposes. 