# README

## SYSTEM REQUIREMENT

* DB
  - MySQL 5.6
* Apache 
    - 2.4
* PHP
  - >= 7.1.3
* Laravel
  - 5.6
* Composer
  - 1.4.1


## Deploy
* Add folder
```
mkdir public/media/avatars
mkdir public/media/drivers
mkdir public/media/orders
mkdir bootstrap/cache
mkdir storage/
mkdir storage/framework/sessions
mkdir storage/logs
mkdir storage/framework
mkdir storage/framework/views
mkdir storage/framework/cache
mkdir storage/framework/cache/data
```

* permission
```
chmod -R 0777 public/
chmod -R 0777 bootstrap/cache
chmod -R 0777 storage/logs/
chmod -R 0777 storage/framework
chmod -R 0777 storage/framework/sessions
chmod -R 0777 storage/framework/cache
chmod -R 0777 storage/framework/cache/data
chmod -R 0777 public/media
chmod -R 0777 public/tmp_uploads
chmod -R 0777 public/file
```

* run
```bash
 composer install
 php artisan cache:clear
 php artisan config:clear
 php artisan view:clear
 php artisan route:cache
```

* run deploy
```bash
cp .env.example .env
php artisan key:generate
```
* config your database in .env
find and replace database config
```bash
vi .env
```
* run database
```bash
php artisan migrate
composer dump-autoload
php artisan db:seed
```

* for developer
```bash
php artisan ide-helper:generate
```

* client script
```bash
npm install
npm run dev
npm run watch
```

* use webpack for backend
```bash
npm install
npm run build
set USE_WEBPACK_BACKEND=true
```

* role and permission
```bash
set env SUPER_ADMIN_ID
composer update
php artisan migrate
```
```bash
php artisan db:seed --class=RoleAndPermissionSeeder
php artisan db:seed --class=SubRoleAndPermissionSeeder
php artisan db:seed --class=SuperAdminUserSeeder

php artisan db:seed --class=ReportProcedureSeeder
```
```bash
php artisan permission:create-role writer
php artisan role:create-permission "edit articles"
```
> **Note**: Cách thêm permission mới

> **Note**: Chỉnh sửa file SubRoleAndPermissionSeeder theo rule cho permission
action + tên permission theo alias của modal ( không có S, cách nhau bằng "_" )

```bash
composer dump-autoload
php artisan db:seed --class=SubRoleAndPermissionSeeder
php artisan permission:cache-reset
```

* refresh data province, district, ward
```bash
TRUNCATE TABLE m_province;
TRUNCATE TABLE m_ward;
TRUNCATE TABLE m_district;

php artisan db:seed --class=MDistrictTableSeeder
php artisan db:seed --class=MProvinceTableSeeder
php artisan db:seed --class=MWardTableSeeder
```