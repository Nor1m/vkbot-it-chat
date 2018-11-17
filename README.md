## Описание
Чат бот для беседы вк.

## Команды

### Команда kick
Исключает одного или нескольких участников из беседы.
Принимает неограниченное количество аргументов.
Пример:
```html
$ kick @username @username2
```
___
### Команда menu
Выводит в чат команды бота.
Не принимает аргументы.
Пример:
```html
$ menu
```
___
### Команда rules
Выводит в чат правила беседы.
Не принимает аргументы.
Пример:
```html
$ rules
```
___
### Команда admins
Выводит в чат админов.
Не принимает аргументы.
Пример:
```html
$ admins
```
___
### Команда wiki
Возвращает информацию с сайта Wikipedia.
Принимает неограниченное количество аргументов.
Пример:
```html
$ wiki hello world
```
___
### Команда translate
Переводит текст с помощью сервиса Yandex Translate.
Принимает 2 аргумента: код языка и текст.
Пример:
```html
$ translate -en Привет мир
```
___
Доступные коды языков:
```html
ru, az, be, bg, ca, cs, da, de, el, en, es, et, fi, fr, hr, hu, hy, it, lt, lv, mk, nl, no, pl, pt, ro, sk, sl, sq, sr, sv, tr, uk.
```
___
### Команда weather
Выводит прогноз погоды для заданного города.
Принимает 2 аргумента: лимит и город.
Аргумент 'лимит' может содержать:
 **-w** -погода на 10 дней;
 **-d** -погода на сегодня (по умолчанию).
Пример:
```html
Пример: $ weather -w Москва
```
___

## Глобальные аргументы

## Аргумент -h
Выводит информацию о команде.
Должен идти после команды.
Пример:
```html
$ translate -h
```
___
## Аргумент -a
Выводит алиасы команды.
Должен идти после команды.
Пример:
```html
$ translate -a
```

## License

MIT License

Copyright (c) 2018 Vitaly Mironov

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
