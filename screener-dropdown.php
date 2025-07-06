<?php
/*
Plugin Name: Screener Dropdown
Description: Filter screener_data using dropdowns populated from screener_list with infinite filtering capability
Version: 1.0
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin activation hook
register_activation_hook(__FILE__, 'screener_dropdown_activate');

function screener_dropdown_activate() {
    // Create plugin directory if it doesn't exist
    $plugin_dir = plugin_dir_path(__FILE__);
    if (!file_exists($plugin_dir . 'data')) {
        mkdir($plugin_dir . 'data', 0755, true);
    }
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'screener_dropdown_enqueue_scripts');

function screener_dropdown_enqueue_scripts() {
    // Only enqueue if shortcode is present
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'screener-dropdown')) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js', array('jquery'), '4.1.0', true);
        wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), '1.13.7', true);
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css', array(), '4.1.0');
        wp_enqueue_style('datatables', 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css', array(), '1.13.7');
    }
}

// Add shortcode
add_shortcode('screener-dropdown', 'screener_dropdown_shortcode');

function screener_dropdown_shortcode() {
    ob_start();
    ?>
    <div id="screener-app" class="screener-container">
        <div class="screener-header">
            <h2>Stock Screener</h2>
            <p>Filter stocks using multiple criteria</p>
        </div>
        
        <div class="filters-container">
            <div id="filters-list">
                <!-- Filters will be added here dynamically -->
            </div>
            <button type="button" id="add-filter" class="btn btn-primary">+ Add Filter</button>
        </div>
        
        <div class="results-container">
            <div class="results-header">
                <h3>Results</h3>
                <span id="results-count">0 stocks found</span>
            </div>
            <div id="results-table-container">
                <table id="results-table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Ticker</th>
                            <th>Name</th>
                            <th>Company Name</th>
                            <th>Industry</th>
                            <th>Sector</th>
                            <th>Date of Screening</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .screener-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .screener-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .screener-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .filters-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .filter-row select, .filter-row input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filter-row select {
            min-width: 200px;
        }
        
        .filter-row input {
            min-width: 150px;
        }
        
        .remove-filter {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .remove-filter:hover {
            background: #c82333;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .results-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .results-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .results-header h3 {
            margin: 0;
            color: #333;
        }
        
        #results-count {
            color: #6c757d;
            font-size: 14px;
        }
        
        #results-table-container {
            padding: 20px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            let screenerData = [];
            let screenerList = [];
            let dataTable = null;
            let filterCounter = 0;
            
            // Load data on page load
            loadData();
            
            function loadData() {
                // Load screener_list.csv
                $.ajax({
                    url: '<?php echo plugin_dir_url(__FILE__); ?>data/screener_list.csv',
                    type: 'GET',
                    dataType: 'text',
                    success: function(data) {
                        screenerList = parseCSV(data);
                        console.log('Loaded screener list:', screenerList.length, 'items');
                    },
                    error: function() {
                        showError('Failed to load screener list data');
                    }
                });
                
                // Load screener_data.csv
                $.ajax({
                    url: '<?php echo plugin_dir_url(__FILE__); ?>data/screener_data.csv',
                    type: 'GET',
                    dataType: 'text',
                    success: function(data) {
                        screenerData = parseCSV(data);
                        console.log('Loaded screener data:', screenerData.length, 'items');
                        initializeDataTable();
                    },
                    error: function() {
                        showError('Failed to load screener data');
                    }
                });
            }
            
            function parseCSV(csv) {
                const lines = csv.split('\n');
                const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
                const result = [];
                
                for (let i = 1; i < lines.length; i++) {
                    if (lines[i].trim() === '') continue;
                    
                    const values = lines[i].split(',').map(v => v.trim().replace(/"/g, ''));
                    const obj = {};
                    
                    headers.forEach((header, index) => {
                        obj[header] = values[index] || '';
                    });
                    
                    result.push(obj);
                }
                
                return result;
            }
            
            function initializeDataTable() {
                if (dataTable) {
                    dataTable.destroy();
                }
                
                dataTable = $('#results-table').DataTable({
                    data: screenerData,
                    columns: [
                        { data: 'Ticker' },
                        { data: 'Name' },
                        { data: 'Company Name' },
                        { data: 'Industry' },
                        { data: 'Sector' },
                        { data: 'Date of Screening' }
                    ],
                    pageLength: 25,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel'],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)"
                    }
                });
                
                updateResultsCount();
            }
            
            function updateResultsCount() {
                const count = dataTable ? dataTable.data().count() : 0;
                $('#results-count').text(count + ' stocks found');
            }
            
            function showError(message) {
                const errorHtml = '<div class="error">' + message + '</div>';
                $('#screener-app').prepend(errorHtml);
            }
            
            // Add filter functionality
            $('#add-filter').click(function() {
                addFilterRow();
            });
            
            function addFilterRow() {
                filterCounter++;
                const filterHtml = `
                    <div class="filter-row" data-filter-id="${filterCounter}">
                        <select class="filter-field" style="min-width: 250px;">
                            <option value="">Select Field</option>
                            ${generateFieldOptions()}
                        </select>
                        <select class="filter-operator" style="min-width: 120px;">
                            <option value="">Operator</option>
                            <option value="equals">Equals</option>
                            <option value="not_equals">Not Equals</option>
                            <option value="contains">Contains</option>
                            <option value="not_contains">Not Contains</option>
                            <option value="greater_than">Greater Than</option>
                            <option value="less_than">Less Than</option>
                            <option value="greater_equal">Greater or Equal</option>
                            <option value="less_equal">Less or Equal</option>
                        </select>
                        <input type="text" class="filter-value" placeholder="Value">
                        <button type="button" class="remove-filter">Remove</button>
                    </div>
                `;
                
                $('#filters-list').append(filterHtml);
                
                // Initialize Select2 for the new filter
                $(`[data-filter-id="${filterCounter}"] .filter-field`).select2({
                    placeholder: 'Select Field',
                    allowClear: true
                });
                
                // Add event listeners
                $(`[data-filter-id="${filterCounter}"] .filter-field, [data-filter-id="${filterCounter}"] .filter-operator, [data-filter-id="${filterCounter}"] .filter-value`).on('change keyup', applyFilters);
                $(`[data-filter-id="${filterCounter}"] .remove-filter`).click(function() {
                    $(this).closest('.filter-row').remove();
                    applyFilters();
                });
            }
            
            function generateFieldOptions() {
                if (!screenerList.length) return '';
                
                const categories = {};
                screenerList.forEach(item => {
                    if (!categories[item.statement]) {
                        categories[item.statement] = [];
                    }
                    categories[item.statement].push(item);
                });
                
                let options = '';
                Object.keys(categories).forEach(category => {
                    options += `<optgroup label="${category}">`;
                    categories[category].forEach(item => {
                        options += `<option value="${item.metric}">${item.metric}</option>`;
                    });
                    options += '</optgroup>';
                });
                
                return options;
            }
            
            function applyFilters() {
                if (!dataTable || !screenerData.length) return;
                
                const filters = [];
                $('.filter-row').each(function() {
                    const field = $(this).find('.filter-field').val();
                    const operator = $(this).find('.filter-operator').val();
                    const value = $(this).find('.filter-value').val();
                    
                    if (field && operator && value !== '') {
                        filters.push({ field, operator, value });
                    }
                });
                
                if (filters.length === 0) {
                    // No filters, show all data
                    dataTable.clear().rows.add(screenerData).draw();
                } else {
                    // Apply filters
                    const filteredData = screenerData.filter(row => {
                        return filters.every(filter => {
                            return applyFilter(row, filter);
                        });
                    });
                    
                    dataTable.clear().rows.add(filteredData).draw();
                }
                
                updateResultsCount();
            }
            
            function applyFilter(row, filter) {
                const fieldValue = row[filter.field];
                const filterValue = filter.value;
                
                if (fieldValue === undefined || fieldValue === null || fieldValue === '') {
                    return false;
                }
                
                switch (filter.operator) {
                    case 'equals':
                        return fieldValue.toString().toLowerCase() === filterValue.toLowerCase();
                    case 'not_equals':
                        return fieldValue.toString().toLowerCase() !== filterValue.toLowerCase();
                    case 'contains':
                        return fieldValue.toString().toLowerCase().includes(filterValue.toLowerCase());
                    case 'not_contains':
                        return !fieldValue.toString().toLowerCase().includes(filterValue.toLowerCase());
                    case 'greater_than':
                        return parseFloat(fieldValue) > parseFloat(filterValue);
                    case 'less_than':
                        return parseFloat(fieldValue) < parseFloat(filterValue);
                    case 'greater_equal':
                        return parseFloat(fieldValue) >= parseFloat(filterValue);
                    case 'less_equal':
                        return parseFloat(fieldValue) <= parseFloat(filterValue);
                    default:
                        return true;
                }
            }
            
            // Add initial filter row
            addFilterRow();
        });
    </script>
    <?php
    return ob_get_clean();
}

// Add admin menu
add_action('admin_menu', 'screener_dropdown_admin_menu');

function screener_dropdown_admin_menu() {
    add_menu_page(
        'Screener Dropdown',
        'Screener Dropdown',
        'manage_options',
        'screener-dropdown',
        'screener_dropdown_admin_page',
        'dashicons-filter',
        30
    );
}

function screener_dropdown_admin_page() {
    ?>
    <div class="wrap">
        <h1>Screener Dropdown Plugin</h1>
        <div class="card">
            <h2>Usage</h2>
            <p>Use the shortcode <code>[screener-dropdown]</code> on any page or post to display the screener.</p>
            
            <h3>Features:</h3>
            <ul>
                <li>Infinite filtering capability</li>
                <li>Select2 dropdowns for field selection</li>
                <li>DataTables for results display</li>
                <li>Multiple operators (equals, contains, greater than, etc.)</li>
                <li>Responsive design</li>
            </ul>
            
            <h3>Data Files:</h3>
            <p>Make sure the following files are placed in the <code>data</code> folder:</p>
            <ul>
                <li><code>screener_list.csv</code> - Contains field definitions</li>
                <li><code>screener_data.csv</code> - Contains company data</li>
            </ul>
        </div>
    </div>
    <?php
} 