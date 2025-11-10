<div class="container mx-auto px-4 py-8">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Dashboard') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</p>
        </div>
        <div class="flex space-x-4">
            <button 
                wire:click="openAddWidgetModal"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition-colors"
            >
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Add Widget') }}
            </button>
        </div>
    </div>

    <!-- ウィジェットグリッド -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="widgets-container">
        @foreach($widgets as $widget)
            <div class="widget-item" data-widget-id="{{ $widget['id'] }}">
                <div class="relative">
                    <!-- ウィジェット削除ボタン -->
                    <button 
                        wire:click="deleteWidget({{ $widget['id'] }})"
                        class="absolute top-2 right-2 z-10 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- ウィジェットコンテンツ -->
                    @livewire($widget['type'], ['widget' => $widget], key('widget-' . $widget['id']))
                </div>
            </div>
        @endforeach

        @if(empty($widgets))
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No widgets yet') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('Get started by adding your first widget') }}</p>
                <button 
                    wire:click="openAddWidgetModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition-colors"
                >
                    {{ __('Add Your First Widget') }}
                </button>
            </div>
        @endif
    </div>

    <!-- ウィジェット追加モーダル -->
    @if($showAddWidgetModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Add Widget') }}</h3>
                        <button 
                            wire:click="closeAddWidgetModal"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @if(empty($availableWidgets))
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <p class="text-gray-600">{{ __('No available widgets') }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ __('Connect services to unlock widgets') }}</p>
                        </div>
                    @else
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($availableWidgets as $widget)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input 
                                        type="radio" 
                                        name="widget_type" 
                                        value="{{ $widget['identifier'] }}"
                                        wire:model="selectedWidgetType"
                                        class="mr-3"
                                    >
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __($widget['display_name']) }}</div>
                                        <div class="text-sm text-gray-500">{{ __($widget['service_display_name']) }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button 
                                wire:click="closeAddWidgetModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                            >
                                {{ __('Cancel') }}
                            </button>
                            <button 
                                wire:click="addWidget"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors"
                                :disabled="{{ !$selectedWidgetType }}"
                            >
                                <span wire:loading.remove>{{ __('Add Widget') }}</span>
                                <span wire:loading>{{ __('Adding...') }}</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('widgets-container');
    let draggedElement = null;

    // ドラッグ＆ドロップ機能
    container.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('widget-item')) {
            draggedElement = e.target;
            e.target.style.opacity = '0.5';
        }
    });

    container.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('widget-item')) {
            e.target.style.opacity = '';
        }
    });

    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement == null) {
            container.appendChild(draggedElement);
        } else {
            container.insertBefore(draggedElement, afterElement);
        }
    });

    container.addEventListener('drop', function(e) {
        e.preventDefault();
        updateWidgetOrder();
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.widget-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateWidgetOrder() {
        const widgetElements = container.querySelectorAll('.widget-item');
        const widgetIds = Array.from(widgetElements).map(el => el.dataset.widgetId);
        
        @this.call('updateWidgetOrder', widgetIds);
    }

    // ウィジェットアイテムにドラッグ属性を追加
    document.querySelectorAll('.widget-item').forEach(item => {
        item.draggable = true;
        item.classList.add('cursor-move');
    });
});
</script>
@endpush
