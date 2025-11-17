<?php

return [
    // プラグイン管理ページ
    'title' => 'プラグイン管理',
    'description' => '利用可能なサービスとウィジェットを管理し、外部サービスとの連携を設定します。',
    
    'tabs' => [
        'services' => 'サービス',
        'widgets' => 'ウィジェット',
    ],
    
    'no_services' => '利用可能なサービスがありません。',
    'no_widgets' => '利用可能なウィジェットがありません。',
    
    'dependencies' => '依存関係',
    
    'status' => [
        'connected' => '接続済み',
        'not_connected' => '未接続',
        'not_configured' => '未設定',
        'unmet' => '未満た',
    ],
    
    'actions' => [
        'connect' => '接続',
        'disconnect' => '接続解除',
        'details' => '詳細',
    ],
    
    'service' => [
        'identifier' => 'サービス識別子',
        'description' => '説明',
        'status' => 'ステータス',
    ],
    
    'widget' => [
        'identifier' => 'ウィジェット識別子',
    ],
    
    'widgets' => [
        'add_to_dashboard' => 'ダッシュボードに追加',
    ],
    
    'service' => [
        'disconnected' => 'サービスの接続を解除しました。',
        'disconnect_error' => 'サービスの接続解除に失敗しました。',
    ],
    
    'configuration' => [
        'required' => 'OAuth認証情報が必要です',
        'setup_instructions' => '.envファイルに以下の設定を追加してください：',
        'github_credentials' => 'GITHUB_CLIENT_ID と GITHUB_CLIENT_SECRET',
        'google_credentials' => 'GOOGLE_CLIENT_ID と GOOGLE_CLIENT_SECRET',
    ],

    // GitHub Plugin
    'github' => [
        'name' => 'GitHub',
        'description' => 'GitHubのリポジトリ、Issue、プルリクエスト、通知を管理します',
    ],
    
    // Google Plugin
    'google' => [
        'name' => 'Google',
        'description' => 'GmailやGoogle CalendarなどのGoogleサービスを連携します',
    ],
    
    // Twitter Plugin (将来追加用)
    'twitter' => [
        'name' => 'Twitter (X)',
        'description' => 'Twitterのタイムラインや通知を表示します',
    ],
];
