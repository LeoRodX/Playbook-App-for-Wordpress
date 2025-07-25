# Описание приложения Playbook для WordPress

## Общее описание

Playbook - это WordPress-плагин, который предоставляет структурированную систему навигации по сценариям и документам. Приложение позволяет:

1. Организовывать материалы в иерархической структуре (категории → подкатегории → документы)
2. Предоставлять пользователям удобный интерфейс для поиска и просмотра материалов
3. Собирать статистику просмотров документов
4. Генерировать отчеты по структуре контента

Основные компоненты системы:
- Административный интерфейс для управления структурой
- Пользовательский интерфейс с навигационным меню и областью контента
- Система поиска по материалам
- Статистика и отчеты

## Инструкция для администратора

### Установка и активация

1. Установите плагин через административную панель WordPress
2. Активируйте плагин "Меню и конструктор Playbook"
3. При активации автоматически создаются необходимые таблицы в базе данных

### Управление структурой контента

1. В административной панели перейдите в "Конструктор Playbook"
2. Для добавления новой категории нажмите кнопку "Добавить категорию"
3. Внутри каждой категории можно:
   - Добавлять подкатегории (кнопка "Добавить подкатегорию")
   - Указывать страницу сценария (выпадающий список)
   - Добавлять связанные документы (кнопка "Добавить документ")
4. Для изменения порядка элементов перетаскивайте их мышью
5. Сохраните изменения кнопкой "Сохранить структуру"

### Работа с отчетами

1. Перейдите в "Отчет PlayBook"
2. Используйте фильтры для поиска нужных материалов:
   - По категории
   - По подкатегории
   - По названию страницы
3. Экспортируйте отчет в CSV при необходимости

### Просмотр статистики

1. Перейдите в "Статистика PlayBook"
2. Установите диапазон дат для фильтрации
3. Просмотрите таблицу с количеством просмотров каждой страницы
4. Экспортируйте статистику в CSV при необходимости

## Инструкция для пользователя

### Основная навигация

1. На странице с Playbook вы увидите:
   - Верхнее меню с категориями и подкатегориями
   - Вкладки документов
   - Область отображения контента

2. Для просмотра материалов:
   - Выберите категорию в левом выпадающем меню
   - Выберите подкатегорию в среднем меню
   - Переключайтесь между вкладками документов

### Поиск материалов

1. Введите поисковый запрос в поле поиска
2. Просмотрите результаты поиска
3. Для перехода к материалу нажмите на его название
4. Для перехода к связанной подкатегории нажмите на ссылку в результатах поиска

### Просмотр документов

1. Выберите нужную вкладку документа
2. Содержимое документа автоматически загрузится в основную область
3. Для возврата к результатам поиска используйте кнопку "Вернуться к результатам"

## Технические особенности

1. Данные хранятся в двух таблицах:
   - `wp_scenario_navigator` - структура категорий и документов
   - `wp_scenario_navigator_stats` - статистика просмотров

2. Для вставки навигатора на страницу используйте шорткод `[scenario_navigator]`

3. Система поддерживает:
   - Адаптивный дизайн для мобильных устройств
   - Подсветку результатов поиска
   - Сохранение статистики просмотров
   - Экспорт данных в CSV

Приложение разработано для удобного структурирования и доступа к корпоративным знаниям и документам.