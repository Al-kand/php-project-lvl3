# Анализатор страниц

[![Maintainability](https://api.codeclimate.com/v1/badges/cd6868a01700b1520071/maintainability)](https://codeclimate.com/github/Al-kand/php-project-lvl3/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/cd6868a01700b1520071/test_coverage)](https://codeclimate.com/github/Al-kand/php-project-lvl3/test_coverage)
[![Actions Status](https://github.com/Al-kand/php-project-lvl3/workflows/hexlet-check/badge.svg)](https://github.com/Al-kand/php-project-lvl3/actions)
[![PHP CI](https://github.com/Al-kand/php-project-lvl3/actions/workflows/phpci.yml/badge.svg)](https://github.com/Al-kand/php-project-lvl3/actions/workflows/phpci.yml)

Анализатор главных страниц сайтов

## Функции

Получение кода ответа сервера, title, description, а также заголовка при обращении к доменному имени сайта

## Системные требования

PHP версия от 7.3, composer

## Интрукция по установке

1. Скопируйте проект на свой компьютер

```
git clone https://github.com/Al-kand/php-project-lvl3.git
```

2. Перейдите в папку с проектом

```
cd php-project-lvl3
```

3. Установите пакеты при помощи composer

```
composer install
```

4. Создайте и при необходимости отредактируйте файл .env

```
cp -n .env.example .env
```

5. Установите ключ

```
php artisan key:genеrате
```

6. Создайте таблицы в базе данных

```
php artisan migrate
```

7. Запустите приложение на локальном сервере

```
php artisan serve --host localhost
```
8. Приложение доступно по адресу `http://localhost:8000`

## Пример

[Анализатор страниц](https://hexlet3.herokuapp.com)
