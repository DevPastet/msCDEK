/**
 * Created by mvoevodskiy on 28.10.15.
 */
$(function () {
    const log = false;

    var APPConfig = {
        CityArray: [],
        city_index: 0,
        city_autocomplete_scrollpos: 0,
        city_autocomplete_scroll_height: 0,
        $city_select: $('#city_select'),
        $city_autocomplete: $(".city-autocomplete"),
        $city_autocomplete_list: $(".city-autocomplete ul"),
        $city_preloader: $(".city-autocomplete__preloader"),
        city_timer_id: 0,
        city_timer_active: false,
        city_search_status: false,
        city_timer_count: 0,
        recent_searches: "",
        city_search_complete: false
    };

    var $dp_select = $('#deliveryPoints'),
        $dp_message = $(".deliveryPoints__message"),
        dp_opt_array = [],
        deliveryDP = +$('input[name="delivery"]:checked').val();


    $('input[name="delivery"]').change(function () {
        deliveryDP = +$(this).val();
        APPConfig.city_search_status = false;
        city_autocomplete_reset();
        timer_reset();
        delivery_reset();
        // delivery_getPoints();
    });

    function valid_substring(str) {
        var array = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ&!?/|{][}\';:%$#*^<>@1234567890()№_=+';
        var newStr = str.toUpperCase();

        log === true ? console.log(newStr) : false;

        for (var i = 0; i < newStr.length; i++) {

            for (var a = 0; a < array.length; a++) {
                if (newStr[i] === array[a]) {
                    log === true ? console.log("в запросе запрещенные знаки") : false;

                    return false
                }
            }
        }
        log === true ? console.log("Запрос верный") : false;

        return true
    }



    function citySelectFunc(event) {

        if (event) {
            if (event.keyCode == 8 || event.keyCode == 46) {
                delivery_reset();
                timer_reset();
                city_autocomplete_reset();
                APPConfig.recent_searches = false;
                APPConfig.city_search_status = false;
            }
        }
        if (APPConfig.city_search_status) { // если сейчас активен поиск, то мы не считываем поле input
            log === true ? console.log("в данный момент идет поиск") : false;

            return
        }
        APPConfig.city_timer_count = 0;
        if (event) {
            event.stopPropagation();
            if (event.type != "cut" && event.type != "paste") {
                event.preventDefault();
            } else {
                delivery_reset();
            }
            var keycode = event.keyCode;
            if (keycode === 9) {
                APPConfig.$city_autocomplete_list.focus();
                return
            }
            if (keycode == 37 || keycode == 39) {
                timer_reset();
                return
            }

            if (keycode == 40 || keycode == 38) {
                if (APPConfig.$city_autocomplete_list["0"].childElementCount > 2) {
                    timer_reset();
                    moveCityList(keycode);
                }
                return
            }

            if (keycode == 13) {
                if (APPConfig.$city_autocomplete.hasClass("active")) {
                    moveCityList(keycode);
                }
                return
            }
        }

        if (APPConfig.$city_select.val().length < 3) {
            log === true ? console.log("содержимое инпута меньше трех символов") : false;

            city_autocomplete_reset();
            return
        }

        if (APPConfig.city_timer_active === false) {
            log === true ? console.log("активируем таймер") : false;

            city_timer();
            APPConfig.city_timer_active = true;
        }
    }

    var city_timer = function () {
        APPConfig.city_timer_count++;
        log === true ? console.log("time " + APPConfig.city_timer_count) : false;

        if (APPConfig.$city_select.val().length < 2) {
            timer_reset();
            return
        }
        if (APPConfig.city_timer_count === 4) {
            clearTimeout(city_timer);
            log === true ? console.log("search") : false;

            if (APPConfig.recent_searches === APPConfig.$city_select.val()) {
                timer_reset();
                return
            }
            var valid = valid_substring(APPConfig.$city_select.val());
            if (valid === false) {
                timer_reset();
                return
            }
            city_autocomplete_reset();
            APPConfig.city_search_status = true;
            // miniShop2.Order.add('city', APPConfig.$city_select.val());
            getcities();
            return
        }
        setTimeout(city_timer, 100);
    };

    function getcities() {
        log === true ? console.log("getcities()") : false;
        if (APPConfig.recent_searches !== APPConfig.$city_select.val()) {

            APPConfig.recent_searches = APPConfig.$city_select.val();

            APPConfig.$city_preloader.addClass("active");
            $.post('/assets/components/mscdek/action.php', {
                mscdek_action: "delivery/getcities",
                cityName: APPConfig.$city_select.val(),
                format: 'json'
            }).done(function (data) {
                log === true ? console.log(data, "success") : false;
                // createLI($.parseJSON(success));

                try{
                    // var data = $.parseJSON(success);
                    createLI($.parseJSON(data));
                    console.log($.parseJSON(data), 'Успех');
                } catch (e) {
                    console.log(e, "Ошибка");
                    reset_city_data();
                    city_autocomplete_reset();
                    timer_reset();
                    APPConfig.$city_preloader.removeClass("active");
                }

            }).fail(function(error) {
                console.log(error, "error" );
            })
        }
    }

    function createLI(data) {
        log === true ? console.log("createLI(data)") : false;

        if (data.length === 0) {
            log === true ? console.log("город не найден") : false;

            APPConfig.city_search_status = false;
            timer_reset();
            city_autocomplete_reset();
            APPConfig.$city_preloader.removeClass("active");
            return
        }

        APPConfig.$city_autocomplete.addClass('active');

// создание набора дом элементов с городами
        for (var i = 0; i < data.length; i++) {
            var elem = document.createElement('li');
            $(elem).text(data[i].name);
            if (i === 0) {
                $(elem).addClass("active");
            }
            APPConfig.CityArray.push(elem);
        }
// вставка дом элементов на страницу
        APPConfig.$city_autocomplete_list.append(APPConfig.CityArray);
        // сброс таймера
        timer_reset();
        $(APPConfig.CityArray).map(function (indx, element) {
            APPConfig.city_autocomplete_scroll_height = APPConfig.city_autocomplete_scroll_height + $(element).innerHeight();
        });
// добавление обработчика событий на элементы списка городов
        $(".city-autocomplete li").on("click", function (event) {
            event.stopPropagation();
            APPConfig.$city_select.val($(this)["0"].innerHTML);
            // скрытие списка городов
            reset_city_data();
            delivery_getPoints();
            APPConfig.recent_searches = "";
            miniShop2.Order.add('city', APPConfig.$city_select.val());
            return
        });
// отключение преловдера
        APPConfig.$city_preloader.removeClass("active");
        // сброс скрола в списке городов
        APPConfig.$city_autocomplete_list.scrollTop(APPConfig.city_autocomplete_scrollpos);

        APPConfig.city_search_status = false;
        log === true ? console.log(APPConfig.city_search_status, "после создания списка городов") : false;

        if (APPConfig.recent_searches !== APPConfig.$city_select.val()) {
            log === true ? console.log("текст инпута не совпадает с предыдущим результатом поиска") : false;

            citySelectFunc();
        }
    }

    function moveCityList(keycode) {

        log === true ? console.log("moveCityList(keycode") : false;
        if (keycode === 13) {
            $(APPConfig.CityArray).map(function (indx, element) {
                if ($(element).hasClass("active")) {
                    APPConfig.$city_select.val($(element).text());
                    reset_city_data();
                    delivery_getPoints();
                    APPConfig.recent_searches = "";
                    miniShop2.Order.add('city', APPConfig.$city_select.val());
                    return
                }
            });
        }

        $(APPConfig.CityArray).map(function (indx, element) {
            if ($(element).hasClass("active")) {
                $(element).removeClass("active");
                APPConfig.city_index = indx;
                return
            }
        });

        if (keycode == 40) {
            if (APPConfig.city_index == APPConfig.CityArray.length - 1) {
                $(APPConfig.CityArray[APPConfig.city_index]).addClass("active");
                return
            }
            $(APPConfig.CityArray[APPConfig.city_index += 1]).addClass("active");

            APPConfig.city_autocomplete_scrollpos = APPConfig.city_autocomplete_scrollpos + $(APPConfig.CityArray[APPConfig.city_index]).position().top;
            APPConfig.$city_autocomplete_list.scrollTop(APPConfig.city_autocomplete_scrollpos);
            return
        }

        if (event.keyCode == 38) {
            if (APPConfig.city_index <= 0) {
                $(APPConfig.CityArray[0]).addClass("active");
                APPConfig.$city_autocomplete_list.scrollTop(0);
                return
            }
            $(APPConfig.CityArray[APPConfig.city_index -= 1]).addClass("active");
            if ($(APPConfig.CityArray[APPConfig.city_index]).position().top > 0) {
                APPConfig.city_autocomplete_scrollpos = APPConfig.city_autocomplete_scrollpos - $(APPConfig.CityArray[APPConfig.city_index]).position().top;
                APPConfig.$city_autocomplete_list.scrollTop(APPConfig.city_autocomplete_scrollpos);
                return
            }
            APPConfig.city_autocomplete_scrollpos = APPConfig.city_autocomplete_scrollpos + $(APPConfig.CityArray[APPConfig.city_index]).position().top;
            APPConfig.$city_autocomplete_list.scrollTop(APPConfig.city_autocomplete_scrollpos)
        }
    }

    function delivery_getPoints() {
        log === true ? console.log("delivery_getPoints()") : false;
        var valid = false;

        for (var i = 0; i < msCDEKConfig.deliveriesDP.length; i++) {
            if (deliveryDP === msCDEKConfig.deliveriesDP[i]) {
                valid = true;
            }
        }

        log === true ? console.log(valid, "valid доставки") : false;
        if (valid === false) {
            delivery_reset();
            log === true ? console.log(deliveryDP, valid, "неверный способ доставки") : false;
            return
        }
        $.post('/assets/components/mscdek/action.php', {
                mscdek_action: "delivery/getPoints",
                city: APPConfig.$city_select.val()
            },
            function (data) {
                log === true ? console.log(data, "getPoints") : false;

                if (data.points.length > 0) {
                    $dp_select.addClass("active");
                    $dp_message.removeClass("active");
                } else {
                    $dp_message.text(data.message);
                    $dp_message.addClass("active");
                    $dp_select.removeClass("active");
                    return
                }
                $dp_select.html('<option value="">  Выберите пункт выдачи  </option>');

                data.points.forEach(function (value) {
                    var opt = '<option value="' + value.address + '">' + value.address + '</option>';
                    dp_opt_array.push(opt);
                });
                $dp_select.append(dp_opt_array);
            }, 'json');
    }


    $("#msOrder").keydown(function (event) {

        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    $(document).click(function (event) {
        if ($(event.target).closest(".city-autocomplete__wrap").length || $(event.target).closest("#deliveryPoints").length) return;

        log === true ? console.log('клик по окну') : false;
        APPConfig.$city_autocomplete.removeClass("active");
        APPConfig.city_search_status = false;
        timer_reset();
        city_autocomplete_reset();
        event.stopPropagation();
    });


    // события на контролы
    $('#country_select').on('change', function () {
        miniShop2.Order.add('country', $(this).val());
    });


    $(function () {
        if (APPConfig.$city_select["0"].nodeName == "SELECT") {
            APPConfig.$city_select.on('change', function () {
                console.log('change');
                miniShop2.Order.add('city', $(this).val());
                delivery_reset();
                //  delivery_getPoints();
            });
            $.post('/assets/components/mscdek/action.php', {mscdek_action: "delivery/getcities"}, function (success) {
                miniShop2.Order.add('city', '');
                APPConfig.$city_select.html('<option value="">  Выберите  </option>' + success);
            });
        } else if (APPConfig.$city_select["0"].nodeName == "INPUT") {
            APPConfig.$city_select.on('keyup', citySelectFunc);
            selectID = document.getElementById("city_select");
            selectID.addEventListener('cut', citySelectFunc);
            selectID.addEventListener('paste', citySelectFunc);
        }
    }());

    function city_autocomplete_reset() {

        log === true ? console.log("city_autocomplete_reset") : false;
        APPConfig.CityArray.splice(0, APPConfig.CityArray.length);
        APPConfig.$city_autocomplete.removeClass('active');
        APPConfig.$city_autocomplete_list.empty();
        APPConfig.city_autocomplete_scrollpos = 0;
    }

    function timer_reset() {
        log === true ? console.log("timer_reset") : false;
        APPConfig.city_timer_count = 0;
        APPConfig.city_timer_active = false;
        clearTimeout(city_timer);
    }

    function reset_city_data() {
        log === true ? console.log("reset_city_data()") : false;
        city_autocomplete_reset();
        timer_reset();
        delivery_reset();
    }

    function delivery_reset() {
        log === true ? console.log("delivery_reset()") : false;

        dp_opt_array.splice(0, dp_opt_array.length);
        $dp_select.removeClass("active");
        $dp_message.removeClass("active");
        $dp_select.empty();
    }

    miniShop2.Callbacks.add('Order.add.response.success', 'msCDEKOrderAdd', function (response) {
        log === true ? console.log(response, "delivery/gettime response") : false;
        console.log(response, 'change');
        if (response.data != undefined && response.data.country != undefined) {
            console.log('change');
            $('#country_select').val(response.data.country);
            $.post('/assets/components/mscdek/action.php', {mscdek_action: "delivery/getcities"}, function (success) {
                miniShop2.Order.add('city', '');
                APPConfig.$city_select.html('<option value="">  Выберите  </option>' + success);
            });
        }


        // if (response.data.city && APPConfig.$city_select["0"].nodeName == "INPUT") {
        //
        //     if (response.data.city.length >= 2 && APPConfig.recent_searches != APPConfig.$city_select.val()) {
        //
        //         APPConfig.recent_searches = APPConfig.$city_select.val();
        //
        //         APPConfig.$city_preloader.addClass("active");
        //         $.post('/assets/components/mscdek/action.php', {
        //             mscdek_action: "delivery/getcities",
        //             cityName: APPConfig.$city_select.val(),
        //             format: 'json'
        //         }, function (success) {
        //             console.log("success");
        //             createLI($.parseJSON(success));
        //         })
        //     }
        // }

        if (response.data != undefined && response.data.city != undefined) {
            miniShop2.Order.getcost();
        }
    });

    miniShop2.Callbacks.add('Order.getcost.response.success', 'msCDEKOrderGetсost', function (response) {

        log === true ? console.log(response, "delivery/gettime response") : false;

        // получаем срок
        // из-за него кстати сказать и происходила перезагрузка страницы, вернее из-за того что плагин возвращал код страницы если был выбран другой способ доставки
        $.post('/assets/components/mscdek/action.php', {mscdek_action: "delivery/gettime"}, function (data) {
            log === true ? console.log(data, "delivery/gettime - data") : false;

            delivery_getPoints();

            var deliverytimespan = $('#ms2_delivery_notify');
            log === true ? console.log(data, "delivery/gettime - data") : false;
            log === true ? console.log(deliverytimespan, "delivery/gettime - deliverytimespan") : false;
            if (deliverytimespan.length > 0) {
                button = $('#msOrder button[type="submit"]');
                deliverytimespan.html(data.message);
                if (data.success) {
                    button.removeAttr('disabled');
                } else {
                    button.attr('disabled', 'disabled');
                }
            }
        }, 'json');

    });


});

