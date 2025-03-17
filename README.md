#　デプロイ中のURL
https://self-ordering-starter-rose.vercel.app/order/

## 管理画面のURL
- 管理画面(パスワードは`eaglelunch`) https://self-ordering-starter-rose.vercel.app/login

## ローカルで動かす

↑の新しく作ったプロジェクトを`git clone`後

### Laravel開発環境が揃ってる場合
PHP, composer, node.js/npmがインストール済み。

```bash
composer install

cp .env.example .env
php artisan key:generate

npm i && npm run build

php artisan serve
```
http://127.0.0.1:8000/order で表示。

### Dockerのみインストール済みの場合
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

cp .env.example .env

./vendor/bin/sail artisan key:generate

./vendor/bin/sail npm i

./vendor/bin/sail npm run build

./vendor/bin/sail up -d
```
http://localhost/order で表示。

## 開発作業
- 注文を受けると`App\Listeners\OrderEntryListener`が呼び出されるので「注文情報をどこかに送信する」はここで処理。

## LINEに通知機能を使うには追加でインストール
```bash
composer require revolution/laravel-line-sdk
```

## LICENCE
MIT
