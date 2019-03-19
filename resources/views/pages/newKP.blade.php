<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Title</title>
    <link rel="stylesheet" href="{{asset('css/pages/newKP.css')}}">
</head>
<body class="print">

<div class="main-wraper container">
    <div class="inner-wrapper">
        <header class="main-header">
            <img src="{{asset('images/kpImages/header.png')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-header__picture" width="1019" height="156">
        </header>

        <main>
            <section class="price-list">
                <h1 class="price-list__main-title">Коммерческое предложение <span>{{ $offersGroup->name }}</span></h1>
                @if($pdf==null) <a href="{{route('downloadAsPdf', ['uuid' => $offersGroup->uuid])}}" class="btn btn-link btn-primary">Скачать PDF</a> @endif

                @foreach($offersGroup->offers as $index => $offer)
                    <div class="price-list__wraper clearfix">
                        <h2 class="price-list__variant">Вариант <span>№{{$index+1}}</span></h2>
                        <p class="price-list__description">{{ $offer->name }}</p>
                        <p class="price-list__date">{{ \Carbon\Carbon::parse($offersGroup->created_at)->format('d.m.Y') }}</p>
                    </div>

                    <div class="price-list__table-wraper">
                        <h2 class="price-list__table-title">Оборудование и расходные материалы</h2>
                        <table class="price-list__table1">
                            <thead>
                            <tr>
                                <td class="column1">Наименование</td>
                                <td class="column2"></td>
                                <td class="column3">Кол-во</td>
                                <td class="column4">Цена</td>
                                <td class="column5">Всего</td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($totalEquipmentSum = 0)
                            @php($consumableSum = 0)
                            @foreach($offer->equipments as $equipment)
                                @if($equipment->pivot->tab_slug != 'rashodnye-materialy')
                                    @php($totalEquipmentSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                    <tr>
                                        <td class="column1">{{ $equipment->name }}</td>
                                        <td class="column2">{{ $equipment->short_description ?? $equipment->description }}</td>
                                        <td class="column3">{{ $equipment->pivot->quantity }}</td>
                                        <td class="column4">{{ $equipment->pivot->price }}</td>
                                        <td class="column5">{{ $equipment->pivot->price * $equipment->pivot->quantity }}</td>
                                    </tr>
                                @elseif($equipment->class == 'work')
                                @else
                                    @php($consumableSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                @endif
                            @endforeach
                            <tr class="expanded">
                                <td class="column1">Монтажный комплект.</td>
                                <td class="column2">Мелкий расходник для монтажа.</td>
                                <td class="column3">1</td>
                                <td class="column4">{{ $consumableSum }}</td>
                                <td class="column5">{{ $consumableSum }}</td>
                            </tr>
                            <tr class="total">
                                <td colspan="3">Всего за оборудование:</td>
                                <td colspan="2">{{ $totalEquipmentSum + $consumableSum }} руб.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="price-list__table-wraper">
                        <h2 class="price-list__table-title">Монтажные и пуско-наладочные работы</h2>
                        <table class="price-list__table2">
                            <thead>
                            <tr>
                                <td class="column1">Наименование работ</td>
                                <td class="column2">Ед. изм.</td>
                                <td class="column3">Кол-во</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offersGroup->equipment as $work)
                                <tr>
                                    <td class="column1">{{ $work->name }}</td>
                                    <td class="column2">{{ $work->points }}</td>
                                    <td class="column3">{{ $work->pivot->quantity }}</td>
                                </tr>
                            @endforeach
                            <tr class="total">
                                <td>Всего за работы с НДС:</td>
                                <td colspan="2">{{ $offersGroup->getTotalWorkPrice() }} руб.</td>
                            </tr>
                            <tr class="total">
                                <td>Всего за работы без НДС (доп. скидка <span>{{ $offersGroup->getAdditionalDiscount() }} руб.</span>):</td>
                                <td colspan="2">{{ $offersGroup->getTotalWorkPriceNoVAT() }} руб.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <table class="price-list__table3">
                        <tr>
                            <td class="column1">Общая стоимость (1 договор - все с НДС)</td>
                            <td class="column2">{{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPrice() }} руб.</td>
                        </tr>
                        <tr>
                            <td class="column1">Общая стоимость (2 договора - оборудование с НДС, работы без НДС)</td>
                            <td class="column2">{{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPriceNoVAT() }} руб.</td>
                        </tr>
                    </table>
                @endforeach
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="main-footer">
            <img src="{{asset('images/kpImages/footer.jpg')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-footer__picture" width="1041" height="49">
        </footer>
    </div>
</div>

<div class="main-wraper container">
    <div class="inner-wrapper">
        <header class="main-header">
            <img src="{{asset('images/kpImages/header.png')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-header__picture" width="1019" height="156">
        </header>

        <main>

            <section class="setting">
                <h2 class="setting__title">Установка систем безопасности</h2>
                <p class="setting__description">Для каждого предприятия, офиса, квартиры или загородного дома необходима собственная концепция безопасности, внедрить которую способны комплексные системы безопасности, которые еще называют интегрированными. Именно комплексный подход необходим для установки охранной и сигнальной системы.</p>
            </section>

            <section class="certificates">
                <h2 class="certificates__title">Наши сертификаты</h2>
                <ul class="certificates__list clearfix">
                    <li class="certificates__item"><img src="{{asset('images/kpImages/sertification1.jpg')}}"  alt="Наши сертификаты"  width="123"  height="180"></li>
                    <li class="certificates__item"><img src="{{asset('images/kpImages/sertification2.jpg')}}" alt="Наши сертификаты" width="123" height="180"></li>
                    <li class="certificates__item"><img src="{{asset('images/kpImages/sertification3.jpg')}}" alt="Наши сертификаты" width="123" height="180"></li>
                    <li class="certificates__item"><img src="{{asset('images/kpImages/sertification4.jpg')}}" alt="Наши сертификаты" width="123" height="180"></li>
                    <li class="certificates__item"><img src="{{asset('images/kpImages/sertification5.jpg')}}" alt="Наши сертификаты" width="123" height="180"></li>
                </ul>
            </section>

            <section class="example">
                <h2 class="example__title">Примеры работ</h2>
                <ul class="example__list clearfix">
                    <li class="example__item"><img src="{{asset('images/kpImages/example1.jpg')}}" alt="Примеры работ" width="213" height="159"></li>
                    <li class="example__item"><img src="{{asset('images/kpImages/example2.jpg')}}" alt="Примеры работ" width="213" height="159"></li>
                    <li class="example__item"><img src="{{asset('images/kpImages/example3.jpg')}}" alt="Примеры работ" width="213" height="159"></li>
                    <li class="example__item"><img src="{{asset('images/kpImages/example4.jpg')}}" alt="Примеры работ" width="213" height="159"></li>
                </ul>
                <p class="example__link">Со всеми работами вы можете ознакомиться по ссылке:
                    <a target="_blank" href="https://aronasb.ru/videonablyudenie/">https://aronasb.ru/videonablyudenie/</a>
                </p>
            </section>

            <section class="reviews">
                <h2 class="reviews__title">Отзывы клиентов</h2>
                <div class="reviews__wraper clearfix">
                    <ul class="reviews__list clearfix">
                        <li class="reviews__item"><img src="{{asset('images/kpImages/reviews.jpg')}}" alt="Отзывы клиентов" width="127" height="183" ></li>
                        <li class="reviews__item"><img src="{{asset('images/kpImages/reviews.jpg')}}" alt="Отзывы клиентов" width="127" height="183"></li>
                        <li class="reviews__item"><img src="{{asset('images/kpImages/reviews.jpg')}}" alt="Отзывы клиентов" width="127" height="183"></li>
                    </ul>
                    <ul class="reviews__features-list">
                        <li class="reviews__features-item">Работаем с 2014 года</li>
                        <li class="reviews__features-item">Реализовано более 530 проектов</li>
                        <li class="reviews__features-item">Выполнено 100% гарантийных обязательств</li>
                        <li class="reviews__features-item">Не менее 3-ех  сертификатов у каждого специалиста</li>
                    </ul>
                </div>
                <p class="reviews__link">Со всеми отзывами вы можете ознакомиться по ссылке:
                    <a target="_blank" href="http://aronasb.ru/foto.html">http://aronasb.ru/foto.html</a>
                </p>
            </section>
        </main>

        <footer class="main-footer">
            <img src="{{asset('images/kpImages/footer.jpg')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-footer__picture" width="1041" height="49">
        </footer>
    </div>
</div>

<div class="main-wraper container">
    <div class="inner-wrapper">
        <header class="main-header">
            <img src="{{asset('images/kpImages/header.png')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-header__picture" width="1019" height="156">
        </header>

        <main>
            <section class="work">
                <h2 class="work__title">Как мы работаем</h2>
                <ul class="work__list clearfix">
                    <li class="work__item work__item1">Выясняем потребности Заказчика</li>
                    <li class="work__item work__item2">Подготавливаем КП</li>
                    <li class="work__item work__item3">Проводим обследование объекта</li>
                    <li class="work__item work__item4">Корректируем КП</li>
                    <li class="work__item work__item5">Согласовываем и заключаем Договор</li>
                    <li class="work__item work__item6">Производится оплата за оборудование</li>
                    <li class="work__item work__item7">Монтаж</li>
                    <li class="work__item work__item8">Производится оплата за работы</li>
                    <li class="work__item work__item9">Гарантийное<br> обслуживание</li>
                </ul>
            </section>

            <section class="next">
                <h2 class="next__title">Ваш следующий шаг</h2>
                <p class="next__description">После согласования предварительной цены в коммерческом предложении на осмотр объекта поедет наш инженер. Он произведет замер для уточнения технических деталей, покажет в реальном времени качество видеоизображения и продемонстрирует оборудование на мобильном стенде.</p>
                <div class="next__wraper clearfix">
                    <p class="next__contacts">
                        С уважением, {{ $offersGroup->user->getFullNameAttribute() }}<br>
                        {{ $offersGroup->user->position }}<br>
                        Компания {{ $offersGroup->user->company }}<br>
                        @if($offersGroup->user->phone_number) Тел. {{ $offersGroup->user->phone_number }}<br> @endif
                        @if($offersGroup->user->mobile_number) Тел. {{ $offersGroup->user->mobile_number }}<br> @endif
                        <a href="mailto:{{ $offersGroup->user->contact_email }}">{{ $offersGroup->user->contact_email }}</a>
                    </p>
                    @if($offersGroup->user->phone_number) <p class="next__phone">
                        Звоните<br>
                        {{ $offersGroup->user->phone_number }}
                    </p> @endif
                </div>
            </section>

        </main>

        <!-- FOOTER -->
        <footer class="main-footer">
            <img src="{{asset('images/kpImages/footer.jpg')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-footer__picture" width="1041" height="49">
        </footer>
    </div>
</div>

<div class="main-wraper container">
    <div class="inner-wrapper">
        <header class="main-header">
            <img src="{{asset('images/kpImages/header.png')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-header__picture" width="1019" height="156">
        </header>

        <main>
            <section class="question" id="question">
                <h2 class="question__title">Часто задаваемые вопросы</h2>
                <ul class="question__list">
                    <li class="question__item">
                        <h3>Каким образом производится оплата?</h3>
                        <p>100% предоплата за оборудвоание и постпоалата за монтжаные работы. В некоторых случаях готовы рассмотреть вариант оплаты 30% аванс и 70% по окончанию монтажных работ.</p>
                    </li>
                    <li class="question__item">
                        <h3>За счет чего вы даете скидку на оборудование?</h3>
                        <p>Нашей компанией заключены Договоры с ведущими поставщиками систем безопасности у которых мы закупаем объем оборудования.</p>
                    </li>
                    <li class="question__item">
                        <h3>Кто заплатит за испорченную чистовую отделку?</h3>
                        <p>От человеческого фактора никто не застрахован, но мы несем ответственность за недочеты во время монтажа и устраняем их.</p>
                    </li>
                    <li class="question__item">
                        <h3>Привлекаем ли мы иностранных граждан на установку систем?</h3>
                        <p>Нет, в нашей организации работают только граждане Российской Федерации.</p>
                    </li>
                    <li class="question__item">
                        <h3>Как вы обеспечите гарантийные обязательства?</h3>
                        <p>Все гарантийные обязательства прописаны в Договоре, который мы предоставляем на согласование каждому Заказчику.</p>
                    </li>
                    <li class="question__item">
                        <h3>Работаете ли вы с НДС?</h3>
                        <p>Да, наша организация работает с НДС.</p>
                    </li>
                </ul>
            </section>
        </main>

        <footer class="main-footer">
            <img src="{{asset('images/kpImages/footer.jpg')}}" alt="Компания Арона. Продажа  установка систем безопасности в Москве и Московской области. Телефон - +7 (495) 003-45-62ю Email - zapros@aronasb.ru" class="main-footer__picture" width="1041" height="49">
        </footer>
    </div>
</div>