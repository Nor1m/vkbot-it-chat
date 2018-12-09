<?php

namespace App\base;

/**
 * Класс Deferred
 *
 * Менеджер отложенных операций, которые будут выполняться после
 * закрытия подключения
 *
 * @package App\base
 * @author Roman Mitasov <metas_roman@mail.ru>
 */
class Deferred
{
    /**
     * @var callable[] Массив задач
     */
    private static $_tasks = [];

    /**
     * Добавить задачу
     * @param callable $callable Функция или лямбда. Параметры в неё передаваться не будут
     * @return void
     */
    public static function add(callable $callable): void
    {
        self::$_tasks[] = $callable;
    }

    /**
     * Имеются ли задачи в наборе
     * @return bool
     */
    public static function hasTasks(): bool
    {
        return count(self::$_tasks) > 0;
    }

    /**
     * Запустить все задачи в порядке их добавления
     * @return void
     */
    public static function run(): void
    {
        foreach (self::$_tasks as $task) {
            call_user_func($task);
        }
    }
}
