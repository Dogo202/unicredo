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

запросы 

GET /api/tasks — возвращает список всех задач.
GET /api/tasks?status=completed - Получение задач со статусом "completed"
GET /api/tasks?page=2&limit=5 - Получение задач с пагинацией (2-я страница, 5 задач на странице)
Создание новой задачи
POST /api/tasks — принимает данные задачи (название, описание, статус) и сохраняет её в базе данных.
Обновление существующей задачи
PUT /api/tasks/{id} — обновляет информацию о задаче по ID (например, название или описание).
Удаление задачи
DELETE /api/tasks/{id} — удаляет задачу по ID.
Получение задачи по ID
GET /api/tasks/{id} — возвращает информацию о задаче по её ID.

