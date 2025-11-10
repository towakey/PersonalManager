<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Widget;
use App\Services\Plugins\PluginManager;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public array $widgets = [];
    public bool $showAddWidgetModal = false;
    public array $availableWidgets = [];
    public string $selectedWidgetType = '';

    protected $listeners = [
        'widgetAdded' => '$refresh',
        'widgetDeleted' => '$refresh',
        'widgetRefreshed' => '$refresh',
    ];

    public function mount()
    {
        $this->loadWidgets();
        $this->loadAvailableWidgets();
    }

    /**
     * ユーザーのウィジェットを読み込む
     */
    private function loadWidgets()
    {
        $userWidgets = Widget::where('user_id', Auth::id())
            ->orderBy('position')
            ->get()
            ->toArray();

        $this->widgets = $userWidgets;
    }

    /**
     * 利用可能なウィジェットを読み込む
     */
    private function loadAvailableWidgets()
    {
        try {
            $pluginManager = new PluginManager();
            $this->availableWidgets = $pluginManager->getAvailableWidgetsForUser()->toArray();
        } catch (\Exception $e) {
            $this->availableWidgets = [];
        }
    }

    /**
     * ウィジェット追加モーダルを開く
     */
    public function openAddWidgetModal()
    {
        $this->showAddWidgetModal = true;
        $this->loadAvailableWidgets();
    }

    /**
     * ウィジェット追加モーダルを閉じる
     */
    public function closeAddWidgetModal()
    {
        $this->showAddWidgetModal = false;
        $this->selectedWidgetType = '';
    }

    /**
     * ウィジェットを追加
     */
    public function addWidget()
    {
        if (!$this->selectedWidgetType) {
            return;
        }

        $maxPosition = Widget::where('user_id', Auth::id())->max('position') ?? 0;

        Widget::create([
            'user_id' => Auth::id(),
            'type' => $this->selectedWidgetType,
            'position' => $maxPosition + 1,
            'settings' => [],
        ]);

        $this->closeAddWidgetModal();
        $this->loadWidgets();
        $this->dispatch('widgetAdded');
    }

    /**
     * ウィジェットを削除
     */
    public function deleteWidget(int $widgetId)
    {
        $widget = Widget::where('user_id', Auth::id())
            ->where('id', $widgetId)
            ->first();

        if ($widget) {
            $widget->delete();
            $this->loadWidgets();
            $this->dispatch('widgetDeleted');
        }
    }

    /**
     * ウィジェットの位置を更新
     */
    public function updateWidgetOrder(array $widgetIds)
    {
        foreach ($widgetIds as $index => $widgetId) {
            Widget::where('user_id', Auth::id())
                ->where('id', $widgetId)
                ->update(['position' => $index]);
        }

        $this->loadWidgets();
    }

    /**
     * 描画処理
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}
