## Новое в PHP7.1 
### Добавлен возвращаемый тип «void» (RFC)
Теперь функции и методы, которые не должны ничего возвращать, можно помечать возвращаемым типом void:

````php
function someNethod(): void {
    // работает если return отсутсвует
    // работает с return;
    // не работает если return null;
    // не работает если return 123;
}
````

Возврат какого-то значения из метода/функции, который помечен как void, будет генерировать исключение уровня Fatal Error. Обратите внимание, что NULL значение не приравнивается к void (отсутствию значения), то есть возращать NULL нельзя.

Кстати, это не значит что $x = someNethod(); не вернет ничего. Как и прежде в $x будет значение NULL. Так же void нельзя использовать как тип к параметру.

````php
function bar(void $foo) {}
// Выбросит: Fatal error: void cannot be used as a parameter type in...
````

### Добавлен новый псевдо-тип: «iterable»

````php
function walkList(iterable $list): iterable {
foreach ($list as $value) {
yield $value[‘id’];
}
}
````

Этот тип по сути объединяет примитивный тип array и интерфейс Traversable (а значит и его производные: Iterator, Generator, etc). Проблема возникла на почве того, что к примеру, foreach может работать с обоими типами, но функция с типом array не примет объект с интерфейсом Traversable и наоборот.

Так же в рамках этого RFC была добавлена новая функция is_iterable(), которая работает аналогично другим is_* функциям.

### Появилась возможность разрешать null в типизированных и возвращаемых параметрах

````php
function callMethod(?Bar $bar): ?Bar {}
$this->callMethod($bar); // Работает
$this->callMethod(null);   // Работает
$this->callMethod();        // НЕ работает
````

Обратите внимание, что использование "?" и значение null по умолчанию не одно и тоже что

````php
function callMethod(int $bar = null) {}
$this->callMethod(1); // Работает
$this->callMethod(null); // Работает
$this->callMethod(); // Тоже работает
````

Причем добавление "?" оставляет поведение обратно совместимым

````php
function callMethod(?Bar $bar = null) {}
// Работает так же как и без “?”
````

Также важный момент по наследованию:

````php
interface Fooable {
function foo(int $i): ?Fooable;
}
interface StrictFooable extends Fooable {
function foo(?int $i): Fooable; // valid
}
````

В наследнике можно делать «строже» возвращаемый тип (то есть запрещать nullable), а параметр наоборот расширять до nullable, НО не наоборот!

### Добавлена возможность использовать отрицательное значение для смещения в строках


````php
echo $msg[-1]; // вернет последний символ
echo $msg{-3}; // Причем RFC явно рекомендует использовать способ $str{} так как $str[] может сбивать с толку
// и в будущем может быть объявлен как устаревшим.
````

Отрицательные значения так же стали разрешены в некоторых строковых функциях: strpos, stripos, substr_count, grapheme_strpos, grapheme_stripos, grapheme_extract, iconv_strpos, file_get_contents, mb_strimwidth, mb_ereg_search_setpos, mb_strpos, mb_stripos.

Везде это означает считать смещение с конца строки.

### Разрешено использовать строковые ключи в конструкции list() 
Так же был добавлен короткий синтаксис для list


````php
["test" => $a, "name" => $b] = ["name" => "Hello", "test" => "World!"];
var_dump($a); // World!
var_dump($b); // Hello
````

Особенности:

- нельзя использовать смешанный синтаксис (если указываем ключи — то указываем их везде, если нет, то используются обычные индексы 0, 1, 2… как обычно):
````php
  // Parse error: syntax error, ...
  ["a" => $a, $b] = ["a" => 1, 2]- 
````

- пустые элементы с ключами тоже же не разрешены:
````php
  // Parse error: syntax error, ...
  list(,,,, "key" => $keyed) = $array;- 
````

- если ключа в исходном массиве нет, то будет выброшено предупреждение Notice: Undefined index: name, а в переменной будет NULL
- при использовании вложенной конструкции list способы можно комбинировать

````php
$points = [
["x" => 1, "y" => 2],
["x" => 2, "y" => 1]
];
[["x" => $x1, "y" => $y1], ["x" => $x2, "y" => $y2]] = $points;

````

### Конвертация callable выражений в замыкание

````php
Closure::fromCallable(callable $calback);
````

Вот наглядный пример применения:

````php
class A {
public function getValidator(string $name = 'byDefault') {
return Closure::fromCallable([$this, $name]);
}
private function byDefault(...$options) {
echo "Private default with:".print_r($options, true);
}
public function __call ( string $name , array $args ) {
echo "Call $name with:".print_r($args, true);
}
}

$a = new A();
$a->getValidator("test")(1,2,3);
// Call test with: Array ( [0] => 1 [1] => 2 [2] => 3 )
$a->getValidator()(‘p1’, ‘p2’);
// Private default with: Array ( [0] => ‘p1’, [1] => ‘p2’)
// Внимание Closure::fromCallable передает контекст ($this) в момент вызова внутрь замыкания,
// тем самым разрешая обращаться к приватным методам
// если оставить только return [$this, $name]; то
$a->getValidator()(‘p1’, ‘p2’);
// вернет
// Call byDefault with:Array ( [0] => p1 [1] => p2 )
// то есть вызовет только публичный метод и не будет иметь доступа к приватным методам объекта
````

### Поддержка модификаторов видимости для констант класса
````php
class Token {
// Константа без модификатора по умолчанию “public”
const PUBLIC_CONST = 0;

        // Константы с различной областью видимости
        private const PRIVATE_CONST = 0;
        protected const PROTECTED_CONST = 0;
        public const PUBLIC_CONST_TWO = 0;
 
        // Весь список имеет одну область видимости
        private const FOO = 1, BAR = 2;
}
````

### Ловить исключения можно объединяя несколько типов исключений в один блок
````php
try {
echo "OK";
} catch (Exception | DomainException $e) {
// ... обработка 2ух типов исключений сразу
} catch (TypeError $e) {
// ...
}
````

### Выбросы ошибок уровня E_NOTICE and E_WARNING при арифметических операциях над строками содержащие не валидные числа

````php
$numberOfApples = "10 apples" + "5 pears";
// Выбросит
// Notice: A non well formed numeric string encountered in example.php on line 3
// Notice: A non well formed numeric string encountered in example.php on line 3
$numberOfPears = 5 * "orange";
// Warning: A non-numeric string encountered in example.php on line 3
````

Это довольно важное изменение, которое теоритически может сломать обратную совместимость приложения если используются свои error handlers для перехвата предупреждений.

Причем есть интересная особенность: пробел в начале строк “ 5” + “ 3” — не даст ошибок. А вот “5 ” + “3 ” — пробел в конце уже даст выдаст предупреждения.

Для обхода последствий неявного преобразования и выброса предупреждений можно явно указывать “cast” в нужный тип: (int)“5 ” + (int)“3 ” или подавлять все принудительно @(“5 ” + “3 ”).

### Другие изменения и обратные несовместимости

- В связи с новыми типами, добавлены новые зарезервированные слова void, iterable, и код который содержит классы, интерфейсы, трейты с такими именами будет давать ошибку в 7.1

- Поменяли поведение в php экстеншенах, которые продолжали выкидывать Fatal Error вместо генерации Error исключения (как текущее ядро 7.0), плюс ошибки уровня E_ERROR или E_RECOVERABLE_ERROR тоже стали выбрасывать исключения там, где возможно (понятное дело, что при нехватки памяти по прежнему скрипт необратимо падает (RFC)).

- Изменилось поведение при вызове функций / методов без передачи обязательных аргументов. Теперь вместо привычного Warning предупреждения, будет выброшено исключение ArgumentCountError (наследует тип Error RFC):

````php
function foo($a) {
var_dump($a);   // теперь исполнение сюда не дойдет и в $a не будет NULL
}
foo();
// Fatal error: Uncaught ArgumentCountError: Too few arguments to function foo(), 0 passed in...
````

- Следующие функции больше нельзя вызвать динамически через: $func(), call_user_func(), array_map() и тд:

* extract()
* compact()
* get_defined_vars()
* func_get_args()
* func_get_arg()
* func_num_args()
* parse_str() с одним аргументом
* mb_parse_str() с одним аргументом
* assert() больше нельзя использовать строку в качестве аргумента

- Функции rand() и srand() теперь просто псевдонимы (alias) к функциям mt_rand() и mt_srand().
Это в свою очередь затронет вывод таких функций:

* rand()
* shuffle()
* str_shuffle()
* array_rand()

- Добавлена функция session_gc(). Теперь можно чистить старые сессии прямо из скриптов.

- Добавлена функция session_create_id(), которая позволяет сгенерировать валидный автоматический id сесии без запуска новой сесии, который можно будет использовать в session_id() для старта сессии со сгенерированным ранее ID.

- Ускорили генерацию ID сессии в 2+ раз, убрав хеширование и используя новую функцию из 7.0 php_random_bytes()

````php
Скорость до: Requests per second: 899.36 [#/sec]
Скорость после: Requests per second: 2278.59 [#/sec]
````

- Убрали неконсистентное поведение над переменной $this

````php
function foo($this) { // Fatal error: Cannot use $this as parameter
}
static $this; // Fatal error: Cannot use $this as static variable
global $this; // Fatal error: Cannot use $this as global variable
try {
...
} catch (Exception $this) { // Fatal error: Cannot re-assign $this
}
foreach ($a as $this) { // Fatal error: Cannot re-assign $this
}
unset($this); // Fatal error: Cannot unset $this
$a = "this";
$$a = 42; // throw new Error("Cannot re-assign $this")
function foo() {
var_dump($this); // throws "Using $this when not in object context"
// php-7.0 emitted "Undefined variable: this" and printed NULL
}
foo();
// и другие кейсы
````

- Расширение mcrypt помечено как устаревшее и все mcrypt_* функции будут выкидывать E_DEPRECATED.

- В curl расширение добавлена поддержка для HTTP/2 Server Push, так же были добавлены новый функции curl_multi_errno(), curl_share_errno(), curl_share_strerror().

- Опция 'e' для функций mb_ereg_replace() и mb_eregi_replace() обьявлена устаревшей.