@extends('layouts.kpPageLayout')

@section('content')

    <div class="container">
        <div class="px-4 py-2">
            <div class="row head-bg">
                <div class="col-3 mt-3">
                    <img class="img-fluid" src="{{ asset('images/logo.png') }}" alt="">
                </div>
                <div class="col-5 mt-3">
                    <p class="mb-0 font-weight-bold h5">Продажа и установка систем безопасности</p>
                    <p class="mb-0 font-weight-bold h5">в Москве и Московской области</p>
                </div>
                <div class="col-4 mt-3">
                    <div class="col-12 float-right">
                        <p class="mb-0 text-white h4 text-right float-right"><img class="svg-icon mx-2" src="{{ asset('images/svg/phone-solid.svg') }}" alt="phone">+7(495)003-45-62</p>
                    </div>
                    <div class="float-right">
                        <p class="mb-0 text-white h4 text-right"><img class="svg-icon mx-2" src="{{ asset('images/svg/envelope-regular.svg') }}" alt="email">zapros@aronasb.ru</p>
                    </div>
                </div>
            </div>
            <div class="head-bg-bottom row align-items-center">
                <p class="font-weight-bold mt-1"><img class="svg-icon mx-2" src="{{ asset('images/svg/map-marker-alt-solid.svg') }}" alt="address">231123, Москва, 3-й проезд Перова поля, дом 8</p>
            </div>
            <div class="row">
                <div class="col-12 my-4">
                    <h1 class="font-weight-bold">
                        Коммерческое предложение <span class="text-primary">{{ $offersGroup->name }}</span>
                    </h1>
                </div>
            </div>
        </div>

        @foreach($offersGroup->offers as $offer)

            <div class="offer my-5">
                <div class="offer-name mt-3">
                    <h3>
                        {{ $offer->name }}
                        <span class="text-right">{{ \Carbon\Carbon::parse($offersGroup->created_at)->format('d.m.Y') }}</span>
                    </h3>
                </div>
                <div class="check-border p-3 my-4">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="6" class="border-top-0 border-bottom-0">1. Оборудование и расходные материалы</th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="3">Наименование</th>
                            <th scope="col" nowrap class="text-center">Кол-во</th>
                            <th scope="col" nowrap class="text-center">Цена, руб.</th>
                            <th scope="col" nowrap class="text-center">Всего, руб.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($totalEquipmentSum = 0)
                        @php($consumableSum = 0)
                        @foreach($offer->equipments as $equipment)
                            @if($equipment->class == 'equipment')
                                @if($equipment->type->slug != 'rashodnye-materialy')
                                    @php($totalEquipmentSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                    <tr>
                                        <td><img src="https://via.placeholder.com/100" alt=""></td>
                                        <td class="align-middle" colspan="2"><b>{{ $equipment->name }}</b> {{ $equipment->description }}</td>
                                        <td class="align-middle text-center">{{ $equipment->pivot->quantity }}</td>
                                        <td class="align-middle text-center">{{ $equipment->pivot->price }}</td>
                                        <td class="align-middle text-center">{{ $equipment->pivot->price * $equipment->pivot->quantity }}</td>
                                    </tr>
                                @else
                                    @php($consumableSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                @endif
                            @endif
                        @endforeach
                        <tr>
                            <td><img src="https://via.placeholder.com/100" alt=""></td>
                            <td class="align-middle" colspan="2"><b>Расходные материалы.</b> Коробы, провода и т.д.</td>
                            <td class="align-middle text-center">1</td>
                            <td class="align-middle text-center">{{ $consumableSum }}</td>
                            <td class="align-middle text-center">{{ $consumableSum }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="check-in-card-border my-4 text-right"><h4>Всего за оборудование {{ $totalEquipmentSum + $consumableSum }}р.</h4></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="check-border p-3 my-4">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="5" class="bg-gray">2. Монтажные и пуско-наладочные работы</th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="2">Наименование работ</th>
                            <th scope="col" nowrap class="text-center">Ед. изм.</th>
                            <th scope="col" nowrap class="text-center">Кол-во</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php($totalWorkSum = 0)
                            @foreach($offer->equipments as $equipment)
                                @if($equipment->class == 'work')
                                    @php($totalWorkSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                    <tr>
                                        <td class="align-middle">{{ $equipment->name }}</td>
                                        <td class="align-middle text-center"></td>
                                        <td class="align-middle text-center">{{ $equipment->points }}</td>
                                        <td class="align-middle text-center">{{ $equipment->pivot->quantity }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="5" class="check-in-card-border my-4 text-right"><h4>Всего за работы ( с НДС ): {{ $totalWorkSum }}р.</h4></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="check-in-card-border my-4 text-right"><h4>Всего за работы ( без НДС, Доп. скидка - <span class="text-danger">???</span> ): ???р.</h4></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
        @endforeach

        <table class="table table-borderless">
            <tr>
                <td class="text-right my-4 font-weight-bold"><h4>Общая стоимость ( 1 договор с НДС ): ???р.</h4></td>
            </tr>
            <tr>
                <td class="text-right my-4 font-weight-bold"><h4>Общая стоимость ( 2 договора - оборудование с НДС, работы без НДС ): ???р.</h4></td>
            </tr>
        </table>
    </div>

@endsection

@section('pdfButton')
    <div class="container text-center">
        <a href="{{route('downloadAsPdf', ['uuid' => $offersGroup->uuid])}}" class="btn pdf-btn text-white my-5 btn-primary create-btn">Скачать как pdf</a>
    </div>
@endsection