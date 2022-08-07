# Тестовое задание по архитектуре для Wisebits

Представляю вашему вниманию, архитектурно "бохатый" тестовый проект по DDD подходу, в связке с гексагональной архитектурой и CQRS! 
Малополезный подход на начальном этапе разработки приложения, но на большом проекте, при правильной готовке, это может значительно упростить расширение и переезд на микросервисную архитектуру.

## Структура
```
- Core
-- Application <- Блок отвечающий за логику работы с данными по вызову из вне или внутреннему событию
---- Command
---- EventHandler
---- Query
-- Domain <- Доменные сущности на базе доктрины и интерфейсы взаимодействия внутри доменного контекста
-- Infrastructure
---- Repository <- Репозитории для получения данных
-- Ports
---- Rest <- интерфейсы для взаимодействия с доменом по Http
----- Serializer <- шаблоны сериализации DTO под конкретный апи-метод
---- CLI
- Shared
-- Domain <- Общие интерфейсы для доменных сущностей
-- Infrastructure
---- Constraint <- Кастомные правила валидации доменных сущностей(тут находится проверка на присутствие в черном списке)
---- Doctrine <- Отлов событий ORM для safe-delete логики, автоматической валидации и обработки событий доменных сущностей
---- Http
---- Migration
```

Пример схемы работы создания сущности:  
- В `Core\Ports\Rest\User\CreateUserRequest` поступает запрос
- В параметре `CreateUserRequest` проводится проверка валидности параметров запроса, если запрос невалиден, то выкидываем эксепшен со списком ошибок
- Подготавливаем `App\Core\Application\Command\User\CreateUserCommand` и отправляем его во внутреннюю шину сообщений
- Шина на основании правил в `config/packages/messenger.yaml` и `config/services.yaml` подбирает необходимый хендлер, **открывает транзакцию** и отправляет запрос на него
- `App\Core\Application\Command\User\CreateUserCommandHandler` получает запрос, создает доменную сущность и отправляет её в репозиторий
- В процессе сохранения мы отлавливаем события изменения сущностей в `App\Shared\Infrastructure\Doctrine`. 
- `ValidateDomainEventSubscriber` проверяет что данные в модели валидны(это вторая валидация, продублирована она по 2-м причинам: 1. Валидацию уникальности юзера и наличия "плохих" слов удобнее всего реализовать в доменной модели, а к ней нет доступа на уровне порта. 2. Если появится другой эндпоинт, который будет заполнять данные некорректно, то возникнут сайд-эффекты)
- `SafeRemoveDomainEventSubscriber` отменяет удаление всех сущностей, которые реализуют интерфейс `SafeDeleteEntityInterface`, и просто помечает их как удаленные
- `TriggerDomainEventSubscriber` рассылает события об изменениях сущностей согласно данным интерфейса `ObservingEntityInterface`
- Если сущность была успешно создана, и она реализует логику рассылки событий своего состояния, то срабатывает хендлер `App\Core\Application\EventHandler\User\UserCreatedEventHandler`. Кол-во обработчиков событий никак не ограничено. Если возникнет какая-либо ошибка при обработке событий, то вся транзакция будет отменена.
- По завершению процесса обработки `CreateUserCommand`, шина **применяет транзакцию**, и возвращает идентификатор созданной сущности(вообще вроде как по канонам команда не должна возвращать результат, но на самом деле это заблуждение, она может возвращать результат своей работы, но не более того)



## Установка
```
docker-compose up -d && \
docker-compose exec php composer install && \
docker-compose exec php bin/console doctrine:migrations:migrate
```

## Статы
#### Psalm:
```
 % docker-compose run php ./vendor/bin/psalm   
Target PHP version: 8.1 (inferred from composer.json)
Scanning files...
Analyzing files...

░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░

------------------------------
                              
       No errors found!       
                              
------------------------------
```

#### PHPUnit:
```
% docker-compose run php ./vendor/bin/phpunit tests
PHPUnit 9.5.21 #StandWithUkraine

...........................................                       43 / 43 (100%)

Time: 00:01.935, Memory: 20.00 MB

OK (43 tests, 100 assertions)
```