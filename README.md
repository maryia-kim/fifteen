# Fifteen
API для пятнашек

Авторизация Bearer api_token. Токен генерится при регистрации. 
Создание игры доступно только для залогиненного юзера.
Решать игру может только автор игры. 
  
**POST /api/game**

В теле запроса может содержать строку с раскладкой поля 
`{"board":"1,2,3,0,5,6,7,8,9,10,11,12,13,14,15,4"}`

Если тело пустое,то поле генерится на сервере.

Пример ответа в файле newGameResponse.json

**POST /api/game/id/solve**

В запрос присылается последовательность ходов. 

Пример формата в файле solveRequest.json.

Если игра решена, приходит ответ с временем прохождения, иначе сообщение о неверном решении. 
