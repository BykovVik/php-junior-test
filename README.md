# Задание №1

## Класс OrderService, предназначен для управления заказами в системе продажи билетов.

#### Класс имеет следующие методы:

* **createOrder:** этот метод создает новый заказ и отправляет его на бронирование на сторонний API. Если бронирование проходит успешно, метод сохраняет заказ в базе данных.
* **generateBarcode:** этот метод генерирует уникальный баркод для заказа.
* **bookOrder:** этот метод отправляет запрос на бронирование заказа на сторонний API.
* **approveOrder:** этот метод отправляет запрос на подтверждение заказа на сторонний API.
* **saveOrder:** этот метод сохраняет заказ в базе данных.
* Класс также имеет свойства **$pdo** и **$apiUrl**, которые представляют собой объект подключения к базе данных и URL стороннего API соответственно.

#### В методе createOrder происходит следующее:

1. Генерируется уникальный баркод для заказа.
2. Отправляется запрос на бронирование заказа на сторонний API.
3. Если бронирование проходит успешно, метод сохраняет заказ в базе данных.
4. Если бронирование не проходит успешно, метод повторяет попытку бронирования с новым баркодом.
5. В методе saveOrder происходит сохранение заказа в базе данных.

Код также использует объект подключения к базе данных **$pdo**для выполнения запросов к базе данных.


# Задание №2

В этой нормализованной таблице мы добавили две новые таблицы: **events** и **ticket_types**. Таблица **events** хранит информацию о событиях, а таблица **ticket_types** хранит информацию о типах билетов.

Таблица **orders** теперь хранит только общую информацию о заказе, а таблица **order_items** хранит информацию о каждом билете в заказе. В таблице **order_items** мы добавили поле **barcode**, которое хранит уникальный баркод для каждого билета.

Это решение позволяет нам хранить дополнительные типы билетов и уникальные баркоды для каждого билета, а также обеспечивает нормализацию таблицы.

