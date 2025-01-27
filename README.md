composer create-project symfony/skeleton task-manager-api
cd task-manager-api
composer require symfony/orm-pack symfony/maker-bundle annotations twig
composer require symfony/validator
composer require lexik/jwt-authentication-bundle

Ввести это в терминале ( если у вас не установлена симфони и эти пакеты )

в репо весь проект с env файлом ничего не надо менять 

Создайте базу через консноль psql с названием task_manager 
Создайте пользователя psql с именем и паролем в env файле 

Запустите сервак php -S localhost:8000 -t public/

проверяйте коллекцию запросов через postman установив заранее коллекцию 
(файл unicredo.postman_collection.json)
