<?php

namespace Granal1\Php2\test\Commands;

use PHPUnit\Framework\TestCase;
use Granal1\Php2\Blog\Commands\Arguments;
use Granal1\Php2\Blog\Exceptions\ArgumentsException;

final class ArgumentsTest extends TestCase
{
    public function testItReturnsArgumentsValueByName(): void
    {
        // Подготовка
        $arguments = new Arguments(['some_key' => 'some_value']);

        // Действие
        $value = $arguments->get('some_key');

        // Проверка
        $this->assertEquals('some_value',$value);
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготавливаем объект с пустым набором данных
        $arguments = new Arguments([]);

        // Описываем тип ожидаемого исключения
        $this->expectException(ArgumentsException::class);
        // и его сообщение
        $this->expectExceptionMessage("No such argument: some_key");

        // Выполняем действие, приводящее к выбрасыванию исключения
        $arguments->get('some_key');
    }

    // Провайдер данных
    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'], 
            [' some_string', 'some_string'], // Тестовый набор No2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];

        // Тестовый набор
        // Первое значение будет передано в тест первым аргументом,
        // второе значение будет передано в тест вторым аргументом
    }

    // Связываем тест с провайдером данных с помощью аннотации @dataProvider
    // У теста два агрумента
    // В одном тестовом наборе из провайдера данных два значения
    
    /**
    * @dataProvider argumentsProvider
    */
    public function testItConvertsArgumentsToStrings($inputValue, $expectedValue): void 
    {
        // Подставляем первое значение из тестового набора
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');

        // Сверяем со вторым значением из тестового набора
        $this->assertEquals($expectedValue, $value);
    }

}