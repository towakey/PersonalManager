<?php

return [
    'title' => 'Settings',
    'description' => 'Manage your account settings and connected services',

    'tabs' => [
        'profile' => 'Profile',
        'accounts' => 'Connected Accounts',
        'sharing' => 'Data Sharing',
        'servers' => 'Server Connections',
    ],

    'profile' => [
        'title' => 'Profile Settings',
        'description' => 'Manage your basic information and password',
        'name' => 'Name',
        'email' => 'Email',
        'language' => 'Language',
        'change_password' => 'Change Password',
        'password_description' => 'Enter your current password to set a new password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'save' => 'Save',
        'updated' => 'Profile updated successfully',
    ],

    'accounts' => [
        'title' => 'Connected Accounts',
        'description' => 'Manage connections to external services',
        'connected' => 'Connected',
        'not_connected' => 'Not Connected',
        'connect' => 'Connect',
        'disconnect' => 'Disconnect',
        'disconnected' => 'Account disconnected successfully',
        'error' => 'Failed to process account connection',
    ],

    'sharing' => [
        'title' => 'Data Sharing Settings',
        'description' => 'Configure sharing settings for your widget data',
        'current_setting' => 'Current Setting',
        'no_widgets' => 'No Widgets',
        'no_widgets_description' => 'Add widgets to your dashboard to configure data sharing settings',
        'go_to_dashboard' => 'Go to Dashboard',
        'updated' => 'Sharing settings updated successfully',
        'rule_added' => 'Sharing rule added successfully',
        'rule_removed' => 'Sharing rule removed successfully',
        'add_rule' => 'Add Sharing Rule',
        'target_type' => 'Target Type',
        'target_id' => 'Target ID',
        'remove_rule' => 'Remove Rule',

        'types' => [
            'private' => 'Private',
            'specific_users' => 'Specific Users',
            'specific_groups' => 'Specific Groups',
            'specific_servers' => 'Specific Servers',
        ],
    ],

    'servers' => [
        'title' => 'Server-to-Server Connections',
        'description' => 'Manage connections to other PersonalManager servers',
        'add_server' => 'Add Server',
        'name' => 'Server Name',
        'url' => 'URL',
        'identifier' => 'Server Identifier',
        'add' => 'Add',
        'remove' => 'Remove',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'no_servers' => 'No Connected Servers',
        'no_servers_description' => 'Add a server to start connecting with other PersonalManager instances',

        'status' => [
            'pending_sent' => 'Invitation Sent',
            'pending_received' => 'Invitation Received',
            'approved' => 'Connected',
            'rejected' => 'Rejected',
        ],

        'invitation_sent' => 'Server invitation sent successfully',
        'removed' => 'Server connection removed successfully',
        'approved' => 'Server connection approved successfully',
        'rejected' => 'Server connection rejected successfully',
        'error' => 'Failed to process server connection',
    ],

    'oauth' => [
        'title' => 'OAuth Service Settings',
        'description' => 'Manage OAuth client settings for GitHub and Google',
        'updated' => 'OAuth settings updated successfully',
        'save' => 'Save OAuth Settings',
    ],
];
