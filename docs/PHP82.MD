## Новое в PHP8.2.

### Readonly-классы

Начиная с PHP 8.2.0, класс может быть помечен модификатором readonly. 
Пометка класса как readonly добавит модификатор readonly к каждому объявленному свойству и предотвратит создание 
динамических свойств. Более того, невозможно добавить их поддержку с помощью атрибута AllowDynamicProperties. 
Попытка это сделать приведёт к ошибке компиляции.


### Типы в виде дизъюнктивной нормальной формы
````php
class Foo {
    public function bar((A&B)|null $entity) {
        return $entity;
    }
}
````

### Самостоятельные типы null, false и true
````php
class Falsy
{
    public function alwaysFalse(): false { /* ... */ *}

    public function alwaysTrue(): true { /* ... */ *}

    public function alwaysNull(): null { /* ... */ *}
}
````

### Новый модуль "Random"

Модуль "random" предлагает новый объектно-ориентированный API для генерации случайных чисел. Вместо использования глобального генератора случайных чисел (ГСЧ) на базе алгоритма вихря Мерсенна, в объектно-ориентированном API доступно несколько ГСЧ, представленных отдельными классами (как реализации интерфейса Engine), которые хранят внутреннее состояние, позволяя создавать несколько независимых последовательностей случайных чисел.

Класс \Random\Randomizer представляет высокоуровневый интерфейс по использованию движков для генерации случайного целого числа, перемешивания массива или строки, выбора случайных ключей массива и многое другое.


### Константы в трейтах

Нельзя получить доступ к константе через имя трейта, но можно через класс, который использует этот трейт.

````php
trait Foo
{
    public const CONSTANT = 1;
}

class Bar
{
    use Foo;
}

var_dump(Bar::CONSTANT); // 1
var_dump(Foo::CONSTANT); // Error
````


### Получение свойств перечислений в константных выражениях

````php
enum A: string 
{
    case B = 'B';
    
    const C = [self::B->value => self::B];
}
````




### Динамические свойства объявлены устаревшими
````php
class User
{
    public $name;
}

$user = new User();
$user->last_name = 'Doe'; // Deprecated notice

$user = new stdClass();
$user->last_name = 'Doe'; // Still allowed
````

Динамические свойства устарели в PHP 8.2 и будут выдавать ошибку ErrorException в PHP 9.0. Если вы не знаете, что такое динамические свойства, то это свойства, которые не присутствуют в объекте, но тем не менее присваиваются или запрашиваются:

Чтобы помочь избежать ошибок и опечаток, больше не рекомендуется определять динамические свойства, 
только если сам класс явно не разрешит это при помощи атрибута #[\AllowDynamicProperties]. 
В экземплярах stdClass по-прежнему можно использовать динамические свойства.

Это изменение не влияет на использование магических методов __get/__set.


### Новые классы, интерфейсы и функции
- Новая функция mysqli_execute_query и метод mysqli::execute_query.
- Новые атрибуты #[\AllowDynamicProperties] и #[\SensitiveParameter].
- Новые методы ZipArchive::getStreamIndex, ZipArchive::getStreamName и ZipArchive::clearError.
- Новые методы ReflectionFunction::isAnonymous и ReflectionMethod::hasPrototype.
- Новые функции curl_upkeep, memory_reset_peak_usage, ini_parse_quantity, libxml_get_external_entity_loader, sodium_crypto_stream_xchacha20_xor_ic, openssl_cipher_key_length.

### Устаревшая функциональность и изменения в обратной совместимости

- Интерполяции строк вида ${} следует избегать.
- Не рекомендуется использовать функции utf8_encode и utf8_decode.
- У методов DateTime::createFromImmutable и DateTimeImmutable::createFromMutable задан предварительный тип возвращаемого значения static.
- Модули ODBC и PDO_ODBC экранирует имя пользователя и пароль.
- При работе функции strtolower и strtoupper теперь не учитывают локаль.
- Методы SplFileObject::getCsvControl, SplFileObject::fflush, SplFileObject::ftell, SplFileObject::fgetc и SplFileObject::fpassthru усиливают свою сигнатуру.
- У метода SplFileObject::hasChildren предварительный тип возвращаемого значения задан как false.
- У метода SplFileObject::getChildren предварительный тип возвращаемого значения задан как null.
- Внутренний метод SplFileInfo::_bad_state_ex объявлен устаревшим.

### ksort(..., SORT_REGULAR) изменения порядка сортировки
До версии PHP 8.2 ksort буквенные ключи помещались перед числовыми

### Устаревание utf8_encode() и utf8_decode()

В PHP 8.2 использование любой функции из utf8_encode() или utf8_decode() вызовет следующие уведомления об устаревании

### Нечувствительный к локали strtolower() и strtoupper()
И strtolower() и strtoupper() больше не зависят от региональных настроек. Вы можете использовать mb_strtolower(), если хотите локализованное преобразование регистра.

