Ветка php2-lesson8

Реализована в коде тема урока.

Выполнено ДЗ-8:
Добавлено к команде создания тестовых данных две опции — users-number и posts-number.
Эти опции переопределяют количество создаваемых пользователей и статей
соответственно.

Дополнена команда создания тестовых данных так, чтобы она также генерировала
комментарии к статьям.

php cli.php fake-data:populate-db -help

Description:
  Populates DB with fake data

Usage:
  fake-data:populate-db [<users-number> [<posts-number> [<comments-number>]]]

Arguments:
  users-number          Number of Users [default: 1]
  posts-number          Number of Posts [default: 1]
  comments-number       Number of Comments from each User [default: 1]