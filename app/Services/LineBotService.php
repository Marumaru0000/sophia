<?php
namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use Illuminate\Support\Facades\Log;

class LineBotService
{
    private MessagingApiApi $api;

    public function __construct()
    {
        // GuzzleHttp クライアント
        $httpClient = new GuzzleClient();

        // SDK 側の Configuration にアクセストークンをセット
        $config = new Configuration();
        $config->setAccessToken(config('services.line.channel_access_token'));

        // MessagingApiApi をインスタンス化
        $this->api = new MessagingApiApi(
            client: $httpClient,
            config: $config,
        );
    }

    /**
     * LINE Push 送信
     *
     * @param string $to LINE の userId
     * @param string $message テキスト本文
     */
    public function push(string $to, string $message): void
    {
        // 先に TextMessage オブジェクトを生成
        $textMessage = new TextMessage([
            'type' => 'text',
            'text' => $message,
        ]);

        // Push API 用のリクエストを組み立て
        $request = new PushMessageRequest([
            'to'       => $to,
            'messages' => [$textMessage],
        ]);

        try {
            // Push 実行
            $this->api->pushMessage($request);
        } catch (\Throwable $e) {
            Log::error('LINE Push Error: ' . $e->getMessage());
        }
    }
}
