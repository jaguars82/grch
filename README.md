Установка проекта
-----------------

~~~
git clone https://github.com/jaguars82/grch
cd <корень проекта>
php composer install
~~~

Создать файл `<корень проекта>/config/private_params.php` и заполнить его актуальными данными:

~~~
<?php

return [
    'yandexApiKey' => '<Ключ для работы с Yandex картой>',
    'telegramApiKey' => '<Ключ для работы с Telegram API>',
];
~~~

Создать файл `<корень проекта>/config/mailer` и заполнить его данными для работы с почтой, например(в данном случае отправленная почта сохраняется в файлы, чтобы почта действительно отправлялась см. документацию к Yii2):

~~~
<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'useFileTransport' => true,
];
~~~

Создать и настроить БД.
Создать файл `<корень проекта>/config/db.php` и заполнить его данными для работы с БД, например:

~~~
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=<Имя БД>',
    'username' => '<Имя пользователя БД>',
    'password' => '<Пароль>',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
];
~~~

Применить миграции

~~~
cd <корень проекта>
yii migrate
yii migrate --migrationPath=@yii/rbac/migrations
~~~

Изменить если требуется данные для пользователя с ролью администратор(по умолчанию это пользователь с email ts-working@yandex.ru) в файле `<корень проекта>/commands/InitController.php` (функция `addUser`).

~~~
cd <корень проекта>
yii init/run
~~~

Настроить и запустить веб-сервер, например:

~~~
cd <корень проекта>
yii serve
~~~