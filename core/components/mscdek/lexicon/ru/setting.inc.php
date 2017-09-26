<?php
/** @var array $_lang */
$_lang['area_mscdek_main'] = 'Основные';
$_lang['area_ms2_delivery'] = 'Параметры доставки';

$_lang['setting_mscdek_from_cityid'] = 'ID города отправителя';
$_lang['setting_mscdek_from_cityid_desc'] = 'По умолчанию 137 - Санкт-Петербург. Узнать ID вашего города можно в файле /core/components/mscdek/docs/cities_c.txt';

$_lang['setting_ms2_delivery_weight_in_kg'] = 'Вес в килограммах';
$_lang['setting_ms2_delivery_weight_in_kg_desc'] = 'Если вес для товаров на сайте указан в килограммах, установите Да';

$_lang['setting_mscdek_cities_list'] = 'Путь до файла со списком городов';
$_lang['setting_mscdek_cities_list_desc'] = 'Путь до файла со списком городов и их ID';

$_lang['setting_mscdek_default_size'] = 'Размер отправления по умолчанию';
$_lang['setting_mscdek_default_size_desc'] = 'Размер одной стороны отправления, в см. На данный момент опция больше для факта, так как в minishop2 нет универсального способа указания объемного размера для товаров';

$_lang['setting_mscdek_default_weight'] = 'Вес отправления по умолчанию';
$_lang['setting_mscdek_default_weight_desc'] = 'Вес одного товара по умолчанию. Указывается в кг или г в зависимости от значения настройки ms2_delivery_weight_in_kg';

$_lang['setting_mscdek_find_size'] = 'Искать габариты в свойствах товаров';
$_lang['setting_mscdek_find_size_desc'] = 'Если включено, при расчете стоимости будут учитываться габариты. Для правильного определения размеров укажите верные названия полей для ширины, высоты, глубины. Поиск осуществляется в следующем порядке: опции товара в корзине -> поля ресурса -> опции ms2 -> TV';

$_lang['setting_mscdek_field_width'] = 'Поле ширины';
$_lang['setting_mscdek_field_width_desc'] = 'Поле ширины';

$_lang['setting_mscdek_field_height'] = 'Поле высоты';
$_lang['setting_mscdek_field_height_desc'] = 'Поле высоты';

$_lang['setting_mscdek_field_depth'] = 'Поле глубины';
$_lang['setting_mscdek_field_depth_desc'] = 'Поле глубины';

$_lang['setting_mscdek_login'] = 'Имя пользователя для интеграции';
$_lang['setting_mscdek_login_desc'] = 'Логин (Account), выдается компанией СДЭК по вашему запросу. Обязательны для учета индивидуальных тарифов и учета условий доставок по тарифам «посылка». Запрос необходимо отправить на адрес integrator@cdek.ru с указанием номера договора со СДЭК. ВАЖНО: Учетная запись для интеграции не совпадает с учетной записью доступа в Личный Кабинет СДЭК.';

$_lang['setting_mscdek_password'] = 'Пароль для интеграции';
$_lang['setting_mscdek_password_desc'] = 'Пароль выдается вместе с логином при запросе';

$_lang['setting_mscdek_return_time'] = 'Отображать срок доставки';
$_lang['setting_mscdek_return_time_desc'] = 'Если отключить, пользователю не будет возвращаться срок доставки, в чанке для этого предназначенном отобразятся только сообщения о невозможности доставки. Значение по умолчанию: включено';

$_lang['setting_mscdek_frontend_js'] = 'Путь к JS';
$_lang['setting_mscdek_frontend_js_desc'] = 'Укажите путь к используемому скрипту. Значение по умолчанию: [[+jsUrl]]web/default.js';

$_lang['setting_mscdek_default_country'] = 'Страна по умолчанию';
$_lang['setting_mscdek_default_country_desc'] = 'Страна по умолчанию    ';
