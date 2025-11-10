<?php

namespace App\Services\Plugins;

use App\Services\Plugins\Contracts\ServicePluginInterface;
use App\Http\Livewire\Widgets\BaseWidget;
use App\Models\ConnectedAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PluginManager
{
    /**
     * 有効なサービスプラグインの一覧を取得
     */
    public function getAvailableServices(): Collection
    {
        $providers = config('app.providers', []);
        $services = collect();

        foreach ($providers as $provider) {
            if (str_contains($provider, 'Services\\Plugins')) {
                $serviceClass = str_replace('ServiceProvider', 'Service', $provider);
                
                if (class_exists($serviceClass) && is_subclass_of($serviceClass, ServicePluginInterface::class)) {
                    $services->push($serviceClass);
                }
            }
        }

        return $services;
    }

    /**
     * 利用可能なウィジェットの一覧を取得
     */
    public function getAvailableWidgets(): Collection
    {
        $widgets = collect();
        
        $this->getAvailableServices()->each(function ($serviceClass) use ($widgets) {
            $serviceWidgets = $serviceClass::getAvailableWidgets();
            foreach ($serviceWidgets as $widgetClass) {
                if (class_exists($widgetClass) && is_subclass_of($widgetClass, BaseWidget::class)) {
                    $widgets->push($widgetClass);
                }
            }
        });

        return $widgets;
    }

    /**
     * 指定されたサービスが利用可能かチェック
     */
    public function isServiceAvailable(string $serviceIdentifier): bool
    {
        return $this->getAvailableServices()->contains(function ($serviceClass) use ($serviceIdentifier) {
            return $serviceClass::getIdentifier() === $serviceIdentifier;
        });
    }

    /**
     * 指定されたウィジェットが利用可能かチェック
     */
    public function isWidgetAvailable(string $widgetIdentifier): bool
    {
        return $this->getAvailableWidgets()->contains(function ($widgetClass) use ($widgetIdentifier) {
            return $widgetClass::getIdentifier() === $widgetIdentifier;
        });
    }

    /**
     * ユーザーが指定されたサービスに接続済みかチェック
     */
    public function isUserConnectedToService(string $serviceIdentifier): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return ConnectedAccount::where('user_id', Auth::id())
            ->where('service_name', $serviceIdentifier)
            ->exists();
    }

    /**
     * ウィジェットの依存関係が満たされているかチェック
     */
    public function areWidgetDependenciesMet(string $widgetClass): bool
    {
        if (!class_exists($widgetClass) || !is_subclass_of($widgetClass, BaseWidget::class)) {
            return false;
        }

        $dependencies = $widgetClass::getDependencies();
        
        foreach ($dependencies as $dependency) {
            if (!$this->isServiceAvailable($dependency) || !$this->isUserConnectedToService($dependency)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 利用可能なウィジェット情報を取得（依存関係チェック済み）
     */
    public function getAvailableWidgetsForUser(): Collection
    {
        return $this->getAvailableWidgets()->filter(function ($widgetClass) {
            return $this->areWidgetDependenciesMet($widgetClass);
        })->map(function ($widgetClass) {
            return [
                'class' => $widgetClass,
                'identifier' => $widgetClass::getIdentifier(),
                'display_name' => $widgetClass::getDisplayName(),
                'service_class' => $widgetClass::getServiceClass(),
                'service_identifier' => $widgetClass::getServiceClass()::getIdentifier(),
                'service_display_name' => $widgetClass::getServiceClass()::getDisplayName(),
                'is_sharable' => $widgetClass::isSharable(),
            ];
        });
    }
}
