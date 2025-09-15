# Udemy Laravel入門学習リポジトリ

## 概要
- Udemy学習で使用したLaravelコードの保存及び機能開発の学習用に使用するリポジトリ

## ブランチ
- main
  - Udemy学習終了時のコード内容
- feature-xxx
  - 自主学習用に機能開発を追加したブランチ

## 機能開発やることリスト
- [x] web上のブログ一覧
- [x] web上のブログ詳細ページ
- [x] web上のねこ一覧
- [x] トップページでの最新ブログ3件表示
- [x] 投稿者とブログの紐づけ
- [x] 管理画面権限分け
  - 全確認できるユーザー、投稿だけできるユーザー
- [ ] ユーザーログインの2要素認証管理
- [ ] 管理画面ユーザー管理
- [ ] 管理画面お問い合わせ管理
- [ ] 管理画面ねこ管理追加
- [ ] カテゴリー追加

## 開発環境構築手順（Windows 11）
1. 予め`Docker Desktop`をインストールしておく（詳細手順省略）
1. `wsl2`をセットアップ（Ubuntu Noble Numbatで実施済み。詳細手順省略）
1. 本リポジトリを`git clone`
1. 以下コマンドで`composer install`
```bash
docker run --rm \
  -v $(pwd):/app \
  -w /app \
  composer install
```
1. 以下コマンドでエイリアス登録
```bash
echo $SHELL
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.bashrc
exec $SHELL -l
sail artisan -V
```
1. 以下で起動
```bash
sail up -d
```
1. 以下でマイグレーション
```bash
sail artisan migrate
# Seeder登録する場合は以下を実行
sail artisan migrate:fresh --seed
```

### 停止時
```bash
sail down
```
