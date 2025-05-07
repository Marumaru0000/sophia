@php
    $orders = collect($orders);
@endphp

<div class="p-4" wire:poll.5000ms="loadOrders">
    {{-- ステータス切り替え --}}
    <div class="flex justify-between mb-6 flex-wrap gap-4">
        <div class="flex space-x-2">
            <button wire:click="setStatusView('active')" 
                class="px-4 py-2 rounded font-bold border 
                    {{ $statusView === 'active' ? 'bg-blue-600 text-white border-blue-800' : 'bg-gray-200 text-gray-700 border-gray-400' }}">
                未準備 / 準備完了
            </button>
            <button wire:click="setStatusView('completed')" 
                class="px-4 py-2 rounded font-bold border 
                    {{ $statusView === 'completed' ? 'bg-green-600 text-white border-green-800' : 'bg-gray-200 text-gray-700 border-gray-400' }}">
                受け渡し完了
            </button>
        </div>

        {{-- カテゴリフィルタ --}}
        <div class="flex space-x-2 overflow-x-auto">
            <span class="font-bold">カテゴリ:</span>
            @foreach($categories as $category)
                <button wire:click="setCategoryFilter('{{ $category }}')"
                    class="px-3 py-1 rounded font-bold border 
                        {{ $categoryFilter === $category ? 'bg-blue-600 text-white border-blue-800' : 'bg-gray-200 text-gray-700 border-gray-400' }}">
                    {{ $category }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- アクティブ状態（未準備/準備完了） --}}
    @if($statusView === 'active')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 未準備 --}}
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-2xl font-bold text-red-600 mb-4 border-b pb-2">🛠 未準備</h2>
                @forelse($orders->where('status', '未準備')->groupBy('order_id') as $orderId => $group)
                    <div class="border p-4 mb-4 rounded bg-red-50">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-lg text-red-800">注文ID: {{ $orderId }}</h3>
                            <span class="text-sm text-gray-500">{{ $group->first()['time'] }}</span>
                        </div>
                        <ul class="mt-3 space-y-1 text-sm">
                            @foreach($group as $order)
                                <li>
                                    🍽 <strong>{{ $order['item'] }}</strong>
                                    @if($order['options'])
                                        <span class="text-gray-600">（{{ $order['options'] }}）</span>
                                    @endif
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-200 rounded">
                                        {{ $order['category'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 text-right">
                            <button wire:click="updateStatusByOrderId('{{ $orderId }}', '準備完了')"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                準備完了にする
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">現在未準備の注文はありません。</p>
                @endforelse
            </div>

            {{-- 準備完了 --}}
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-2xl font-bold text-yellow-600 mb-4 border-b pb-2">✅ 準備完了</h2>
                @forelse($orders->where('status', '準備完了')->groupBy('order_id') as $orderId => $group)
                    <div class="border p-4 mb-4 rounded bg-yellow-50">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-lg text-yellow-800">注文ID: {{ $orderId }}</h3>
                            <span class="text-sm text-gray-500">{{ $group->first()['time'] }}</span>
                        </div>
                        <ul class="mt-3 space-y-1 text-sm">
                            @foreach($group as $order)
                                <li>
                                    🍽 <strong>{{ $order['item'] }}</strong>
                                    @if($order['options'])
                                        <span class="text-gray-600">（{{ $order['options'] }}）</span>
                                    @endif
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-200 rounded">
                                        {{ $order['category'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 text-right">
                            <button wire:click="updateStatusByOrderId('{{ $orderId }}', '受け渡し完了')"
                                    class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded text-sm">
                                受け渡し完了にする
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">現在準備完了の注文はありません。</p>
                @endforelse
            </div>
        </div>

    {{-- 受け渡し完了 --}}
    @else
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-2xl font-bold text-green-600 mb-4 border-b pb-2">📦 受け渡し完了</h2>
            @forelse($orders->where('status', '受け渡し完了')->sortByDesc('time')->groupBy('order_id') as $orderId => $group)
                <div class="border p-4 mb-4 rounded bg-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">注文ID: {{ $orderId }}</h3>
                        <span class="text-sm text-gray-500">{{ $group->first()['time'] }}</span>
                    </div>
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach($group as $order)
                            <li>
                                🍽 <strong>{{ $order['item'] }}</strong>
                                @if($order['options'])
                                    <span class="text-gray-600">（{{ $order['options'] }}）</span>
                                @endif
                                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-300 rounded">
                                    {{ $order['category'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-gray-500">過去の注文はありません。</p>
            @endforelse
        </div>
    @endif
</div>
