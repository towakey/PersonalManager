<?php

namespace App\Http\Livewire\Widgets;

use Livewire\Component;
use App\Models\Widget;
use App\Services\Plugins\Contracts\ServicePluginInterface;

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
