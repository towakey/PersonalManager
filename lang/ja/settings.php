<?php

return [
    'title' => '設定',
    'description' => 'アカウント設定と連携サービスを管理',

    'tabs' => [
        'profile' => 'プロフィール',
        'accounts' => 'アカウント連携',
        'sharing' => 'データ公開',
        'servers' => 'サーバー連携',
    ],

    'profile' => [
        'title' => 'プロフィール設定',
        'description' => '基本情報とパスワードを管理',
        'name' => '名前',
        'email' => 'メールアドレス',
        'language' => '言語',
        'change_password' => 'パスワード変更',
        'password_description' => '新しいパスワードを設定する場合は、現在のパスワードを入力してください',
        'current_password' => '現在のパスワード',
        'new_password' => '新しいパスワード',
        'confirm_password' => 'パスワード（確認用）',
        'save' => '保存',
        'updated' => 'プロフィールを更新しました',
    ],

    'accounts' => [
        'title' => 'アカウント連携',
        'description' => '外部サービスとの連携を管理',
        'connected' => '連携済み',
        'not_connected' => '未連携',
        'connect' => '連携する',
        'disconnect' => '連携を解除',
        'disconnected' => 'アカウント連携を解除しました',
        'error' => 'アカウント連携の処理に失敗しました',
    ],

    'sharing' => [
        'title' => 'データ公開設定',
        'description' => 'ウィジェットデータの公開範囲を設定',
        'current_setting' => '現在の設定',
        'no_widgets' => 'ウィジェットがありません',
        'no_widgets_description' => 'データ公開設定を変更するには、まずダッシュボードでウィジェットを追加してください',
        'go_to_dashboard' => 'ダッシュボードへ移動',
        'updated' => '公開設定を更新しました',
        'rule_added' => '共有ルールを追加しました',
        'rule_removed' => '共有ルールを削除しました',
        'add_rule' => '共有ルールを追加',
        'target_type' => '対象タイプ',
        'target_id' => '対象ID',
        'remove_rule' => 'ルールを削除',

        'types' => [
            'private' => '自分のみ',
            'specific_users' => '指定ユーザー',
            'specific_groups' => '指定グループ',
            'specific_servers' => '指定サーバー',
        ],
    ],

    'servers' => [
        'title' => 'サーバー間連携',
        'description' => '他のPersonalManagerサーバーとの連携を管理',
        'add_server' => 'サーバーを追加',
        'name' => 'サーバー名',
        'url' => 'URL',
        'identifier' => 'サーバー識別子',
        'add' => '追加',
        'remove' => '削除',
        'approve' => '承認',
        'reject' => '拒否',
        'no_servers' => '連携サーバーがありません',
        'no_servers_description' => '他のサーバーとの連携を開始するには、サーバーを追加してください',

        'status' => [
            'pending_sent' => '招待送信済み',
            'pending_received' => '招待受信済み',
            'approved' => '連携済み',
            'rejected' => '拒否済み',
        ],

        'invitation_sent' => 'サーバーへの招待を送信しました',
        'removed' => 'サーバー連携を削除しました',
        'approved' => 'サーバー連携を承認しました',
        'rejected' => 'サーバー連携を拒否しました',
        'error' => 'サーバー連携の処理に失敗しました',
    ],
];
