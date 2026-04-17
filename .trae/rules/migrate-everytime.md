需要运行项目的相关命令时，需要在命令前面加：docker exec -w /www/wwwroot/linshe/rhis bt\_dev\_env

例如：

- 执行迁移：docker exec -w /www/wwwroot/linshe/rhis bt\_dev\_env php artisan migrate
- 生成 Filament 资源：docker exec -w /www/wwwroot/linshe/rhis bt\_dev\_env php artisan make:filament-resource PatientProfile
- 清理缓存：docker exec -w /www/wwwroot/linshe/rhis bt\_dev\_env php artisan optimize:clear
