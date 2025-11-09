# プラグインアーキテクチャ設計書

## 1. 設計思想

*   `specification.md` の要件に基づき、外部サービスとの連携機能を「プラグイン」として独立させ、コアシステムとの疎結合を実現します。
*   コミュニティの貢献者が新しいサービス連携プラグインを容易に開発・追加できるよう、明確なルールと共通のインターフェースを定義します。
*   各プラグインは、認証、ウィジェットの提供、API通信など、サービス連携に必要な責務を自己完結して持ちます。

## 2. ディレクトリ構造

サービス連携プラグインは、`app/Services/Plugins` ディレクトリ配下に、サービス名で分類して格納します。

```
app/
└── Services/
    └── Plugins/
        ├── GitHub/
        │   ├── GitHubServiceProvider.php  // サービスプロバイダ
        │   ├── GitHubService.php          // API通信などの中核ロジック
        │   ├── Contracts/                 // インターフェース定義
        │   │   └── GitHubApiInterface.php
        │   └── Widgets/
        │       ├── Notifications.php      // 通知ウィジェット (Livewireコンポーネント)
        │       └── Issues.php             // Issueウィジェット (Livewireコンポーネント)
        ├── Google/
        │   ├── GoogleServiceProvider.php
        │   ├── GoogleService.php
        │   ├── Contracts/
        │   │   └── GoogleApiInterface.php
        │   └── Widgets/
        │       ├── Gmail.php
        │       └── Calendar.php
        └── ... (他のサービス)
```
* **補足:** 認証処理（OAuth等）は、各サービスプラグイン内に閉じるのではなく、`app/Http/Controllers/Auth/` 配下にサービスごとのコントローラーを配置し、ルーティングで一元管理する方針も検討します。今回は初期設計としてプラグイン内に含める案で進めます。

## 3. 共通インターフェース

すべてのサービスプラグインは、以下のインターフェースを実装した`Service`クラスを持つことを推奨します。

### 3.1. `ServicePluginInterface`

各サービスの基本的な情報や機能を提供します。

```php
namespace App\Services\Plugins\Contracts;

use Illuminate\Http\Request;
use App\Models\ConnectedAccount;

interface ServicePluginInterface
{
    /**
     * サービスの一意な識別子を返す (例: 'github')
     * @return string
     */
    public static function getIdentifier(): string;

    /**
     * サービスの表示名に対応する言語キーを返す (例: 'plugins.github.name')
     * @return string
     */
    public static function getDisplayName(): string;

    /**
     * サービスの説明に対応する言語キーを返す (例: 'plugins.github.description')
     * @return string
     */
    public static function getDescription(): string;

    /**
     * このサービスが依存する他のサービスの識別子リストを返す (例: ['google_auth'])
     * @return array<string>
     */
    public static function getDependencies(): array;

    /**
     * このサービスが提供するウィジェットのLivewireコンポーネントクラス名リストを返す
     * @return array<class-string>
     */
    public static function getAvailableWidgets(): array;

    /**
     * 認証を開始するためのリダイレクトレスポンスを生成する
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getAuthRedirect(): \Illuminate\Http\RedirectResponse;

    /**
     * 認証コールバックを処理し、認証情報をConnectedAccountモデルとして返す
     * @param Request $request
     * @return ConnectedAccount
     */
    public function handleAuthCallback(Request $request): ConnectedAccount;

    /**
     * 指定されたアカウントのAPIクライアントを生成して返す
     * @param ConnectedAccount $account
     * @return mixed
     */
    public function getApiClient(ConnectedAccount $account);
}
```

### 3.2. `WidgetInterface` (Livewireコンポーネントの基底クラス)

各ウィジェットの表示や更新処理は、Livewireの標準的なライフサイクルメソッドを利用します。
全ウィジェットで共通の処理を持たせるため、`WidgetComponent` のような基底クラスを作成し、それを継承する形を推奨します。

```php
namespace App\Http\Livewire\Widgets;

use Livewire\Component;
use App\Models\Widget;

abstract class BaseWidget extends Component
{
    public Widget $widget;

    /**
     * ウィジェットの識別子 (例: 'github-notifications')
     */
    abstract public static function getIdentifier(): string;

    /**
     * ウィジェットの表示名に対応する言語キーを返す (例: 'widgets.github_notifications.name')
     */
    abstract public static function getDisplayName(): string;

    /**
     * このウィジェットが属するサービスのServiceクラス名
     * @return class-string<ServicePluginInterface>
     */
    abstract public static function getServiceClass(): string;

    /**
     * このウィジェットが依存するサービスの識別子リストを返す
     * @return array<string>
     */
    abstract public static function getDependencies(): array;

    /**
     * このウィジェットがデータ公開機能に対応しているかを返す
     * @return bool
     */
    public static function isSharable(): bool
    {
        return false;
    }

    /**
     * 初期マウント処理
     */
    public function mount(Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * データをリフレッシュする
     */
    abstract public function refresh();

    /**
     * 描画処理
     */
    abstract public function render();
}
```

## 4. プラグインの登録と管理

*   各プラグインは、自身の`ServiceProvider`（例: `GitHubServiceProvider`）を持ちます。
*   この`ServiceProvider`内で、`ServicePluginInterface`を実装したサービスクラスをサービスコンテナに登録します。
*   コアアプリケーションは、`config/app.php` の `providers` 配列に各プラグインの`ServiceProvider`を登録することで、プラグインを有効化します。

```php
// config/app.php

'providers' => [
    // ... 他プロバイダ

    /*
     * Plugin Service Providers...
     */
    App\Services\Plugins\GitHub\GitHubServiceProvider::class,
    App\Services\Plugins\Google\GoogleServiceProvider::class,
    // ...
],
```
*   **プラグイン管理:** `PluginManager`のようなクラスを作成し、`config('app.providers')`からプラグインの`ServiceProvider`を走査し、`getIdentifier()`や`getAvailableWidgets()`を呼び出すことで、有効なサービスやウィジェットの一覧を取得できるようにします。

### 4.1. 依存関係の管理 (Dependency Management)

*   **目的:** プラグイン間の依存関係を定義し、依存するプラグインが利用できない状態での実行を防ぎます。例えば、「Google Calendarウィジェット」は、「Google連携プラグイン」が有効であり、かつユーザーがGoogleアカウントを連携済みでなければ使用できません。
*   **実装:**
    1.  `PluginManager` は、すべての有効なプラグイン（`Service`クラス）とウィジェット（`BaseWidget`クラス）を登録する際に、`getDependencies()` メソッドを呼び出して依存関係を解決し、マップを構築します。
    2.  あるプラグインやウィジェットが利用可能かどうかを判断する際、`PluginManager` は以下の条件をチェックします。
        *   **インストールの依存:** 依存先のサービス識別子を持つプラグインが、`config/app.php` の `providers` に登録され、有効になっているか。
        *   **アカウント連携の依存:** 依存先のサービスについて、ログイン中のユーザーが `connected_accounts` テーブルに有効な連携情報を持っているか。
    3.  これらの条件が満たされない場合、そのプラグインやウィジェットは「利用不可」状態として扱われます。
*   **UIへの反映:**
    *   **設定画面:** 「アカウント連携」ページでは、依存関係が満たされていないサービスは「Connect」ボタンを無効化し、その理由（例：「Google連携が必要です」）をツールチップなどで表示します。
    *   **ダッシュボード:** 「ウィジェット追加」モーダルでは、利用不可のウィジェットはグレーアウトし、選択できないようにします。

## 5. 処理フロー例：ダッシュボード表示

1.  `DashboardController` (またはLivewireのDashboardコンポーネント) が、ログイン中のユーザーに紐づく `widgets` をDBから取得します。
2.  `PluginManager` を通じて、利用可能な全ウィジェットの定義（クラス名、表示名など）を取得します。
3.  Bladeビュー内で、ユーザーの `widgets` データをループ処理します。
4.  各`widget`の `type` (例: 'github-notifications') に対応するLivewireコンポーネントを `<livewire:is>` タグで動的に描画します。
    ```blade
    @foreach ($userWidgets as $widget)
        <livewire:is :component="$widget->type" :widget="$widget" :key="$widget->id" />
    @endforeach
    ```
5.  各Livewireウィジェットコンポーネント (`BaseWidget`を継承) は、`mount()` で自身の`Widget`モデルを受け取ります。
6.  `render()` メソッド内で、`PluginManager`または`Service`クラス経由でAPIクライアントを取得し、データを取得してビューに渡します。
7.  `refresh()` アクションがトリガーされると、再度APIからデータを取得し、コンポーネントが再描画されます。
