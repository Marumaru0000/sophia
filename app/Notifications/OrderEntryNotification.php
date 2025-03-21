<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Revolution\Line\Notifications\LineChannel;
use Revolution\Line\Notifications\LineMessage;
use Revolution\Ordering\Contracts\Payment\PaymentMethodFactory;
use Revolution\Ordering\Events\OrderEntry;

class OrderEntryNotification extends Notification
{
    use Queueable;

    /**
     * 注文ID.
     *
     * @var string
     */
    public string $order_id;

    /**
     * 詳細を含む商品データ.
     *
     * @var array|null
     */
    public ?array $items;

    /**
     * テーブル番号.
     *
     * @var string|null
     */
    public ?string $table;

    /**
     * 追加メモ.
     *
     * @var string|null
     */
    public ?string $memo;

    /**
     * オプションデータ.
     *
     * @var array|null
     */
    public ?array $options;

    /**
     * @var string|null
     */
    private ?string $payment;

    /**
     * @var string|null
     */
    private ?string $order_items;

    /**
     * Create a new notification instance.
     *
     * @param  OrderEntry  $event
     */
    public function __construct(OrderEntry $event)
    {
        $this->order_id = $event->order_id;
        $this->items = $event->items;
        $this->memo = $event->memo;
        $this->options = $event->options;

        $this->payment = app(PaymentMethodFactory::class)
            ->name(Arr::get($this->options, 'payment', 'cash'));

        $this->order_items = collect($this->items)
            ->map(fn ($item) => '【'.Arr::get($item, 'name').'】('.Arr::get($item, 'price').'円)')
            ->implode(PHP_EOL);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return [
            //'mail',
            //LineChannel::class,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting(__('注文が送信されました'))
            ->subject(__('注文ID:') . $this->order_id)
            ->line('◆注文番号：'.$this->order_id)
            ->line('◆メモ：'.$this->memo)
            ->line('◆合計：'.collect($this->items)->sum('price').'円')
            ->line('◆支払い方法：'.$this->payment)
            ->line('◆注文◆')
            ->line($this->order_items);
    }

    public function toLine(object $notifiable): LineMessage
    {
        $message = collect([
            '',
            '◆注文番号：'.$this->order_id,
            '◆メモ：'.$this->memo,
            '◆合計：'.collect($this->items)->sum('price').'円',
            '◆支払い方法：'.$this->payment,
            '◆注文◆'.PHP_EOL.$this->order_items,
        ])->implode(PHP_EOL.PHP_EOL);

        return LineMessage::create($message);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
