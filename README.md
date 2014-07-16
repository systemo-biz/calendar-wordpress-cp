Calendar for WordPress by CasePress
=====================

# Todo
Этот раздел описывает требования к плагину
## Общие требования
1. Тип поста event_cp, слаг events
2. Дата начала = дата поста. Чтобы иметь возможность выборки событий по дате старта через WP_Date_Query (https://wpmag.ru/2013/rabota-s-datami-v-wordpress-wp_date_query/)
3. На странице архива по этому посту выводить справа календарь. Слева сайдбар. Обычно.
4. Таксономия "Категория событий". Позволят указывать типы. Примеры: Срок, Напоминание, Начало.
5. Дата окончания события хранится в поле order таблицы posts, и является количеством секунд от даты начала. Затем чтобы прибавляя order к дате начала, иметь возможность получить длительность события. Обычно оно равно часу. Но может быть больше.


## API
1. add_event_cp($object_type, $object_id, $event_key, $event_value, $event_duration) - добавляет события с привзякой. аналог http://wp-kama.ru/function/add_metadata
2. update_event_cp($object_type, $object_id, $event_key, $event_value, $event_duration) - обновляет событие. если оно отличается.
3. delete_event_cp
4. get_event_cp


##Форма добавляени события
1. Делаем шорткодом
2. Можем заполнить поля: Наименование, Описание, Ссылка (то куда перейдем при переходе на событие), Основание (может быть ID поста), Участники (также как в Делах)
3. Поля также можем заполнить через GET.
4. Можем в опциях выбрать страницу, которая будет основой формы (для того чтобы затем иметь возможность плучить ID этой страницы и интегрировать генерацию УРЛ в другие части системы)


## Шаблон архива / списка
1. Календарь выводим на базе http://arshaw.com/fullcalendar/
2. Встает вопрос как этому календарю отдать его данные в формате JSON? Для этого подойдет механизм по аналогии с feed, только JSON. Передаем в URL параметр типа ?view_cp=json-fullcalendar и получаем вывод постов не в виде обычной таблицы, а в виде JSON


## Переход на страницу события
1. Сами события как посты не публичные. Переходить на них нельзя.
2. У каждого события есть либо родитель в виде другого поста, либо ID комментария в мете "parrent_comment". Чтобы иметь возможность получить родительский пост или родительский коммент.


