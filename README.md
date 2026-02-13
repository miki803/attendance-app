
# 勤怠管理アプリ
一般ユーザーの勤怠打刻および、管理者による勤怠管理・修正申請の承認を行うWebアプリケーションです。

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:miki803/attendance-app.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. `cp .env.example .env`
4. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
5. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.27
- MySQL8.0.26

## ER図
![alt](ER.png)

## URL
- 開発環境：http://localhost/login
- 開発環境：http://localhostadmin/login
- phpMyAdmin:：http://localhost:8080/

## テストアカウント
管理者
name: 管理者ユーザ  
email: admin@example.com  
password: password  
-------------------------
一般ユーザー
name: 山田 太郎
email: 	taro.y@coachtech.com 
password: password  
-------------------------

## テスト
Featureテストを中心に29件実装
主なテスト内容
認証
- 会員登録成功 / バリデーション
- ログイン成功 / 失敗
- 管理者ログイン

勤怠
- 出勤・退勤・休憩
- 二重出勤防止
- 勤怠一覧 / 詳細表示

申請
- 修正申請送信
- 管理者承認

権限制御
- 一般ユーザーの管理画面アクセス拒否（403）

バリデーション
- 時間の整合性チェック
- 備考必須チェック（管理者・ユーザー）