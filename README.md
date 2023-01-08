# Forum

This is open source forum

## Installation

### Step 1.

>Require php7

Clone this repo, install dependency

```bash
git clone https://github.com/HuyNguyen206/forum-council.git
composer i
php artisan key:generate

npm i && npm run dev
```
### Step 2.
Create a .env file by copy it from .env.example file
copy .env.example .env

Update database connection
Run php artisan migrate to prepare the database

### Step 3.
Create an account in google captcha to get app_id and secret_key, then update it into .env file
Create an account in Algolia, then update their id, key into .env file

### Step 4.
Until the admin feature available, add channel record manually in channels table
Run php artisan cache:clear to remove the cache if there are any
