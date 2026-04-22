## Docker 环境
容器名称：bt_dev_env
容器内项目根目录绝对路径：/www/wwwroot/linshe/rhis

需要运行项目的相关命令时，需要在命令前面加：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env

例如：
- 执行迁移：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan migrate

- 生成 Filament 资源：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan make:filament-resource PatientProfile

- 清理缓存：docker exec -w /www/wwwroot/linshe/rhis bt_dev_env php artisan optimize:clear

