## Новое в PHP8.0.

### Вместо docblock — атрибуты.

````php
use \Support\Attributes\ListensTo;

class ProductSubscriber
{
<<ListensTo(ProductCreated::class)>>
public function onProductCreated(ProductCreated $event) { /* … */ }

    <<ListensTo(ProductDeleted::class)>>
    public function onProductDeleted(ProductDeleted $event) { /* … */ }
}
````

### Объединение типов аргументов методов

````php
public function foo(Foo|Bar $input): int|float;
public function bar(mixed $input): mixed;

````

Объединение типов позволяет указывать сразу несколько типов. Также есть новый mixed тип, который представляет несколько типов одновременно.

### Можно указать static в return type.
````php
interface Foo
{
public function bar(): static;
}
````

### Встроенный JIT-компилятор.
````php
[JIT]
opcache.jit=5
````

Что такое JIT?

«JIT» расшифровывается как «just in time» — «в нужный момент». Вы, вероятно, знаете, что PHP является интерпретируемым языком: он не скомпилирован как программа на C, Java или Rust. Вместо этого он переводится в машинный код — то, что понимает процессор — во время выполнения.

«JIT» — это метод, который компилирует части кода во время выполнения так, что вместо этого можно использовать скомпилированную версию.

Думайте об этом как о «кэшированной версии» интерпретируемого кода, сгенерированного во время выполнения.


### throw может быть использован в выражениях.

````php
$triggerError = fn() => throw new MyError();

$foo = $bar['offset'] ?? throw new OffsetDoesNotExist('offset');
````

### Можно не указывать переменную исключения, если она вам не нужна.

````php
try {
    // Something goes wrong
} catch (MySpecialException) {
    Log::error("Something went wrong");
}
````

### В конце списка параметров можно указать запятую.

````php
public function(
    string $parameterA,
    int $parameterB,
    Foo $objectfoo,
) {
    // …
}
````

### Новые строковые функции
````php
str_contains('string with lots of words', 'words');

str_starts_with('haystack', 'hay');

str_ends_with('haystack', 'stack');
````

### Новый Stringable interface, который автоматически добавляется в классы, которые реализуют метод __toString ().
````php
function bar(Stringable $stringable) { /* … */ }
````

### Вызов ::class сразу из объекта.
````php
$object::class
````


