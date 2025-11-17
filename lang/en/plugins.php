<?php

return [
    // Plugin management page
    'title' => 'Plugin Management',
    'description' => 'Manage available services and widgets, and configure external service integrations.',
    
    'tabs' => [
        'services' => 'Services',
        'widgets' => 'Widgets',
    ],
    
    'no_services' => 'No available services.',
    'no_widgets' => 'No available widgets.',
    
    'dependencies' => 'Dependencies',
    
    'status' => [
        'connected' => 'Connected',
        'not_connected' => 'Not Connected',
        'not_configured' => 'Not Configured',
        'unmet' => 'Unmet',
    ],
    
    'actions' => [
        'connect' => 'Connect',
        'disconnect' => 'Disconnect',
        'details' => 'Details',
    ],
    
    'service' => [
        'identifier' => 'Service Identifier',
        'description' => 'Description',
        'status' => 'Status',
    ],
    
    'widget' => [
        'identifier' => 'Widget Identifier',
    ],
    
    'widgets' => [
        'add_to_dashboard' => 'Add to Dashboard',
    ],
    
    'service' => [
        'disconnected' => 'Service has been disconnected.',
        'disconnect_error' => 'Failed to disconnect service.',
    ],
    
    'configuration' => [
        'required' => 'OAuth credentials required',
        'setup_instructions' => 'Please add the following settings to your .env file:',
        'github_credentials' => 'GITHUB_CLIENT_ID and GITHUB_CLIENT_SECRET',
        'google_credentials' => 'GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET',
    ],

    // GitHub Plugin
    'github' => [
        'name' => 'GitHub',
        'description' => 'Manage GitHub repositories, issues, pull requests, and notifications',
    ],
    
    // Google Plugin
    'google' => [
        'name' => 'Google',
        'description' => 'Integrate with Google services like Gmail and Google Calendar',
    ],
    
    // Twitter Plugin (for future addition)
    'twitter' => [
        'name' => 'Twitter (X)',
        'description' => 'Display Twitter timeline and notifications',
    ],
];
