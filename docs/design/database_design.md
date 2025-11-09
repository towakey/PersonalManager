# データベース設計書

## 1. 設計思想

*   `specification.md` の要件に基づき、ユーザー情報、連携サービス情報、ダッシュボードのウィジェット情報を管理できる構造とします。
*   機密情報（APIトークンなど）はLaravelの暗号化機能を利用して安全に保管することを前提とします。
*   拡張性を考慮し、新しいサービスやウィジェットの種類が増えても対応しやすいシンプルな設計を目指します。

## 2. ER図 (テキスト表現)

```
+-------------+       +----------------------+       +------------------+
|    users    |       | connected_accounts   |       |      widgets     |
+-------------+       +----------------------+       +------------------+
| id (PK)     |------>| user_id (FK)         |------>| user_id (FK)     |
| name        |       | id (PK)              |       | id (PK)          |
| email       |       | service_name         |       | type             |
| password    |       | access_token         |       | settings         |
| locale      |       | refresh_token (nullable) |   | position         |
| created_at  |       | expires_at (nullable)  |   | created_at       |
| updated_at  |       | created_at           |       | updated_at       |
+-------------+       | updated_at           |       +------------------+
       |              +----------------------+                |
       |                                                      |
       +------------------+                    +----------------v------+
                          |                    |  sharing_settings   |
+-------------+       +---v---------+          +---------------------+
|   groups    |------>| group_user  |<---------| id (PK)             |
+-------------+       +-------------+          | widget_id (FK)      |
| id (PK)     |       | group_id (FK) |          | sharing_type        |
| name        |       | user_id (FK)  |          | access_token        |
| owner_id (FK)|       | role          |          +---------------------+
| created_at  |       +-------------+                         |
| updated_at  |                                               |
+-------------+                                +----------------v------+
                                             |   sharing_rules     |
                                             +---------------------+
                                             | id (PK)             |
                                             | sharing_setting_id (FK)|
                                             | target_id           |
                                             | target_type         |
                                             +---------------------+


+-----------------------+
|  server_connections   |
+-----------------------+
| id (PK)               |
| name                  |
| url                   |
| server_identifier     |
| status                |
| api_token (nullable)  |
| created_at            |
| updated_at            |
+-----------------------+
```

## 3. テーブル定義

### 3.1. `users` テーブル

ユーザーの基本情報を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `name` | `VARCHAR(255)` | ユーザー名 |
| `email` | `VARCHAR(255)`, `UNIQUE` | メールアドレス |
| `email_verified_at` | `TIMESTAMP`, `nullable` | メールアドレス検証日時 |
| `password` | `VARCHAR(255)` | ハッシュ化されたパスワード |
| `locale` | `VARCHAR(10)`, `nullable` | ユーザーの優先言語 (例: 'ja', 'en')。デフォルトはアプリケーションのデフォルト言語。 |
| `remember_token` | `VARCHAR(100)`, `nullable` | "Remember Me" 機能のトークン |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

### 3.2. `groups` テーブル

ユーザーが所属するグループ（組織）の情報を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `name` | `VARCHAR(255)` | グループ名 |
| `owner_id` | `BIGINT`, `UNSIGNED`, `FK` | `users`テーブルへの外部キー (グループの所有者) |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

### 3.3. `group_user` テーブル

ユーザーとグループの多対多の関連を定義する中間テーブルです。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `group_id` | `BIGINT`, `UNSIGNED`, `FK` | `groups`テーブルへの外部キー |
| `user_id` | `BIGINT`, `UNSIGNED`, `FK` | `users`テーブルへの外部キー |
| `role` | `VARCHAR(255)` | グループ内での役割 (例: 'admin', 'member') |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

### 3.4. `connected_accounts` テーブル

ユーザーが連携した外部サービスのアカウント情報を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `user_id` | `BIGINT`, `UNSIGNED`, `FK` | `users`テーブルへの外部キー |
| `service_name` | `VARCHAR(255)` | サービス名 (例: 'github', 'google', 'twitter') |
| `access_token` | `TEXT` | 暗号化されたアクセストークン |
| `refresh_token` | `TEXT`, `nullable` | 暗号化されたリフレッシュトークン（提供される場合） |
| `expires_at` | `TIMESTAMP`, `nullable` | トークンの有効期限 |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

**補足:**
*   `service_name` と `user_id` の組み合わせでユニーク制約を設けることを検討します。
*   `access_token`, `refresh_token` は `encrypt()` ヘルパーで暗号化して保存します。

### 3.5. `widgets` テーブル

ユーザーがダッシュボードに表示するウィジェットの設定を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `user_id` | `BIGINT`, `UNSIGNED`, `FK` | `users`テーブルへの外部キー |
| `type` | `VARCHAR(255)` | ウィジェットの種類 (例: 'github_notifications', 'gmail_inbox') |
| `settings` | `JSON`, `nullable` | ウィジェットごとの設定 (例: 表示件数、フィルタ条件) |
| `position` | `INTEGER` | ダッシュボード上での表示順 |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

**補足:**
*   `type` は、後述するプラグインアーキテクチャにおける各サービスのウィジェット識別子と対応します。
*   `settings` にJSON型を利用することで、ウィジェットごとの柔軟な設定項目に対応できます。

### 3.6. `sharing_settings` テーブル

ウィジェットデータの公開範囲の基本設定を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `widget_id` | `BIGINT`, `UNSIGNED`, `FK` | `widgets`テーブルへの外部キー (UNIQUE制約) |
| `sharing_type` | `VARCHAR(255)` | 公開種別 ('private', 'specific_users', 'specific_groups', 'specific_servers') |
| `access_token` | `VARCHAR(255)`, `UNIQUE` | 公開データにアクセスするためのユニークなトークン |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

### 3.7. `sharing_rules` テーブル

`sharing_type` が 'specific_users' または 'specific_servers' の場合の、具体的な公開対象を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `sharing_setting_id` | `BIGINT`, `UNSIGNED`, `FK` | `sharing_settings`テーブルへの外部キー |
| `target_id` | `VARCHAR(255)` | 公開対象のID (ユーザーID、グループID、またはサーバーの識別子) |
| `target_type` | `VARCHAR(255)` | 公開対象の種別 ('user', 'group', 'server') |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |

### 3.8. `server_connections` テーブル

他のシステムインスタンスとの連携（信頼関係）の状態を格納します。

| カラム名 | データ型 | 説明 |
| :--- | :--- | :--- |
| `id` | `BIGINT`, `UNSIGNED`, `PK` | 主キー |
| `name` | `VARCHAR(255)` | 連携先サーバーの分かりやすい名前 |
| `url` | `VARCHAR(255)` | 連携先サーバーのURL |
| `server_identifier` | `VARCHAR(255)`, `UNIQUE` | 連携先サーバーのシステム識別子 |
| `status` | `VARCHAR(255)` | 連携状態 ('pending_sent', 'pending_received', 'approved', 'rejected') |
| `api_token` | `TEXT`, `nullable` | 連携先サーバーからのAPIリクエストを認証するためのトークン（暗号化して保存） |
| `created_at` | `TIMESTAMP` | 作成日時 |
| `updated_at` | `TIMESTAMP` | 更新日時 |
