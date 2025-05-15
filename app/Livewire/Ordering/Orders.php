<?php

namespace App\Livewire\Ordering;
use App\Services\LineBotService;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Orders extends Component
{
    public array $orders = [];
    public int $orderCount = 0;
    public string $statusView = 'active'; // active = 未準備/準備完了, completed = 受け渡し完了
    public string $categoryFilter = ''; // カテゴリ絞り込み、デフォルトは空（全カテゴリ）
    public array $categories = []; // MicroCMSから取得したカテゴリ一覧

    public function mount()
    {
        $this->loadCategories();
        $this->loadOrders();
    }

    public function loadCategories()
    {
        try {
            $client = new Client();
            $response = $client->get(env('ORDERING_MICROCMS_ENDPOINT'), [
                'headers' => ['X-API-KEY' => env('ORDERING_MICROCMS_API_KEY')],
                'query' => [
                    'limit' => 100,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            // ★ここでデバッグ用のログを出力する（全体）
            Log::info('microCMSから取得したデータ:', $data);

            // ★categoryのみを抽出してログに出力
            $categoriesRaw = collect($data['contents'] ?? [])->pluck('category')->toArray();
            Log::info('microCMSの各itemのcategory構造:', $categoriesRaw);
            $this->categories = collect($data['contents'] ?? [])
    ->flatMap(function ($item) {
        $categories = $item['category'] ?? [];

        // null → []、string → [string] に統一
        if (is_null($categories)) {
            return [];
        }
        if (is_string($categories)) {
            $categories = [$categories];
        }

        return collect($categories)->map(function ($category) {
            if (is_array($category) && isset($category['value'])) {
                return $category['value'];
            }
            if (is_string($category)) {
                return $category;
            }
            return null;
        });
    })
    ->filter()
    ->unique()
    ->values()
    ->toArray();


        } catch (\Exception $e) {
            Log::error('microCMSカテゴリ取得エラー:', ['message' => $e->getMessage()]);
            $this->categories = [];
        }
    }

    public function loadOrders()
    {
        $response = Http::withToken(env('AIRTABLE_API_KEY'))
            ->get("https://api.airtable.com/v0/" . env('AIRTABLE_BASE_ID') . "/" . env('AIRTABLE_TABLE_NAME'));

            $orders = collect($response['records'])->map(function ($record) {
                return [
                    'id' => $record['id'],
                    'order_id' => $record['fields']['order_id'] ?? '',
                    'user_id' => $record['fields']['line_user_id'] ?? '',
                    'item' => $record['fields']['item_name'] ?? '',
                    'category' => $record['fields']['category'] ?? '未分類',
                    'options' => $record['fields']['selected_option'] ?? '',
                    'status' => $record['fields']['status'] ?? '',
                    'time' => $record['fields']['purchase_time'] ?? '',
                ];
            });            

        $this->orderCount = $orders->count();
        $this->orders = $orders->toArray();
    }

    public function updateStatus($id, $status)
    {
        Http::withToken(env('AIRTABLE_API_KEY'))
            ->patch("https://api.airtable.com/v0/" . env('AIRTABLE_BASE_ID') . "/" . env('AIRTABLE_TABLE_NAME') . "/" . $id, [
                'fields' => [
                    'status' => $status,
                ],
            ]);

        $this->loadOrders();
    }
    public function updateStatusByOrderId(string $orderId, string $status)
{
    $targetOrders = collect($this->orders)->where('order_id', $orderId);

    foreach ($targetOrders as $order) {
        Http::withToken(env('AIRTABLE_API_KEY'))
            ->patch("https://api.airtable.com/v0/" . env('AIRTABLE_BASE_ID') . "/" . env('AIRTABLE_TABLE_NAME') . "/" . $order['id'], [
                'fields' => ['status' => $status],
            ]);
    }
    // 準備完了になったら、ユーザーへ LINE Push
    if ($status === '準備完了') {
        $lineUserIds = collect($this->orders)
            ->where('order_id', $orderId)
            ->pluck('user_id')
            ->unique()
            ->filter();

        /** @var LineBotService $svc */
        $svc = app(LineBotService::class);
        foreach ($lineUserIds as $uid) {
            $svc->push(
                $uid,
                "🛎 ご注文 {$orderId} のお料理が準備完了しました。お受け取り口までお越しください！"
            );
        }
        // 任意でログも残せます
        Log::info("LINE Push sent to: " . $lineUserIds->implode(','));
    }

    $this->loadOrders();
}


    public function setStatusView($view)
    {
        $this->statusView = $view;
    }

    public function setCategoryFilter($filter)
    {
        $this->categoryFilter = $filter;
    }

    public function getFilteredOrdersProperty(): Collection
    {
        $orders = collect($this->orders);

        if (!empty($this->categoryFilter)) {
            $orders = $orders->filter(fn($order) =>
                str_contains($order['category'] ?? '', $this->categoryFilter)
            );
        }        

        return $orders->sortBy('time')->values();
    }

    public function render()
    {
        return view('livewire.ordering.orders', [
            'orders' => $this->filteredOrders,
            'categories' => $this->categories,
            'statusView' => $this->statusView,
            'categoryFilter' => $this->categoryFilter,
        ]);
    }
}