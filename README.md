#Price Reader

Плагин для Joomla! для отображения цен из загруженных через Price Uploader таблиц.

##Использование

###Примеры синтаксиса:
Загрузить из таблицы #__pricelistbase строку с id=1 и вывести первую цену(Price1)
`{price=1 type=base column=1}`

Загрузить из таблицы #__pricelistcity строку с id=1 и вывести первую цену(Price1)
`{price=1 type=city column=1}`

Загрузить из таблицы #__pricelistbase строку с id=12 и вывести первую цену(Price3)
`{price=12 type=base column=3}`

Загрузить из таблицы #__pricelistbase строку с id=23 и вывести первую цену(Price2) и текстовое описание ""
`{price=23 type=base column=2 des=""}`

Загрузить из таблицы #__pricelistbase строку с id=23 и вывести первую цену(Price2) и текстовое описание "эни кей деск!"
`{price=23 type=base column=2 des="эни кей деск!"}`