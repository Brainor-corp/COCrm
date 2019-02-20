@extends('layouts.kpPageLayout')

@section('pdfFontStyle')
    <style>
        *{ font-family: DejaVu Sans !important;}
    </style>
@endsection

@section('content')
    <div class="container-fluid content-border">
        <div class="py-2">
            <div class="head-bg-pdf">
                <table class="head-table">
                    <tr>
                        <td><img class="logo-pdf" src="{{ asset('images/logo.png') }}" alt=""></td>
                        <td>
                            <div class="middle">
                                <p class="mb-0 font-weight-bold fs-pdf">Продажа и установка систем безопасности</p>
                                <p class="mb-0 font-weight-bold fs-pdf">в Москве и Московской области</p>
                            </div>
                        </td>
                        <td>
                            <div class="last">
                                <p class="mb-0 text-white"><img class="svg-icon mx-2 horizontal-flip" src="{{ asset('images/svg/phone-solid-pdf.svg') }}" alt="phone">{{ $offersGroup->user->contact_email ?? '+7(495)003-45-62' }}</p>
                                <p class="mb-0 text-white"><img class="svg-icon mx-2" src="{{ asset('images/svg/envelope-regular.svg') }}" alt="email">{{ $offersGroup->user->contact_email ?? 'zapros@aronasb.ru' }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="head-bg-bottom-pdf align-items-center">
                <table>
                    <tr>
                        <td>
                            <p class="font-weight-bold mt-1"><img class="svg-icon" src="{{ asset('images/svg/map-marker-alt-solid.svg') }}" alt="address">231123, Москва, 3-й проезд Перова поля, дом 8</p>
                        </td>
                    </tr>
                </table>
            </div>
            {{--<div class="col-12 px-0">--}}
                {{--<img src="{{ asset('images/pdf/head.png') }}" class="img-fluid" alt="">--}}
            {{--</div>--}}
            <div class="row">
                <div class="col-12 my-4">
                    <h4 class="font-weight-bold">
                        Коммерческое предложение <span class="text-primary">{{ $offersGroup->name }}</span>
                    </h4>
                </div>
            </div>
        @foreach($offersGroup->offers as $offer)
            {{--<div class="page-break"></div>--}}
            <div class="offer my-5">
                <div class="offer-name mt-3">
                    <h5>
                        {{ $offer->name }}
                    </h5>
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th colspan="6" class="border-top-0 border-bottom-0">Оборудование и расходные материалы</th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="3" class="text-gray border-top-0 border-bottom-0">Наименование</th>
                            <th scope="col" nowrap class="text-center text-gray border-top-0 border-bottom-0">Кол-во</th>
                            <th scope="col" nowrap class="text-center text-gray border-top-0 border-bottom-0">Цена, руб.</th>
                            <th scope="col" nowrap class="text-center text-gray border-top-0 border-bottom-0">Всего, руб.</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($totalEquipmentSum = 0)
                    @php($consumableSum = 0)
                    @foreach($offer->equipments as $equipment)
                        @if($equipment->class == 'equipment')
                            @if($equipment->pivot->type != 'rashodnye-materialy')
                                @php($totalEquipmentSum += $equipment->pivot->counted_price * $equipment->pivot->quantity)
                                <tr>
                                    <td class="border-top-0 border-bottom"><img src="https://via.placeholder.com/100" alt=""></td>
                                    <td class="align-middle border-top-0 border-bottom" colspan="2"><b>{{ $equipment->name }}</b> {{ $equipment->description }}</td>
                                    <td class="align-middle border-top-0 border-bottom text-center">{{ $equipment->pivot->quantity }}</td>
                                    <td class="align-middle border-top-0 border-bottom text-center">{{ $equipment->pivot->counted_price }}</td>
                                    <td class="align-middle border-top-0 border-bottom text-center">{{ $equipment->pivot->counted_price * $equipment->pivot->quantity }}</td>
                                </tr>
                            @else
                                @php($consumableSum += $equipment->pivot->counted_price * $equipment->pivot->quantity)
                            @endif
                        @endif
                    @endforeach
                    <tr>
                        <td class="border-top-0 border-bottom"><img src="https://via.placeholder.com/100" alt=""></td>
                        <td class="align-middle" colspan="2"><b>Расходные материалы.</b> Коробы, провода и т.д.</td>
                        <td class="align-middle text-center">1</td>
                        <td class="align-middle text-center">{{ $consumableSum }}</td>
                        <td class="align-middle text-center">{{ $consumableSum }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="check-in-card-border my-4 p-total"><h5>Всего за оборудование</h5></td>
                        <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h5>{{ $totalEquipmentSum + $consumableSum }}р.</h5></td>
                    </tr>
                    </tbody>
                </table>
                {{--<div class="page-break"></div>--}}
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th colspan="5" class="border-top-0 border-bottom-0 h4">2. Монтажные и пуско-наладочные работы</th>
                    </tr>
                    <tr>
                        <th scope="col" colspan="2" class="text-gray border-top-0 border-bottom-0">Наименование работ</th>
                        <th scope="col" nowrap class="text-center text-gray border-top-0">Ед. изм.</th>
                        <th scope="col" nowrap class="text-center text-gray border-top-0">Кол-во</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($offersGroup->equipment as $work)
                            <tr>
                                <td class="align-middle border-bottom border-top-0">{{ $work->name }}</td>
                                <td class="align-middle border-bottom border-top-0 text-center"></td>
                                <td class="align-middle border-bottom border-top-0 text-center">{{ $work->points }}</td>
                                <td class="align-middle border-bottom border-top-0 text-center">{{ $work->pivot->quantity }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="check-in-card-border my-4 p-total"><h5>Всего за работы с НДС:</h5></td>
                            <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h5>{{ $offersGroup->getTotalWorkPrice() }}р.</h5></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="check-in-card-border my-4 p-total"><h5>Всего за работы без НДС, Доп. скидка - <span class="text-danger">{{ $offersGroup->getAdditionalDiscount() }}</span></h5></td>
                            <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h5>{{ $offersGroup->getTotalWorkPriceNoVAT() }}р.</h5></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <table class="table table-sm table-borderless">
            <tr>
                <td colspan="5" class="my-4 font-weight-bold"><h5>Общая стоимость ( 1 договор с НДС ): {{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPrice() }}р.</h5></td>
            </tr>
            <tr>
                <td colspan="5" class="my-4 font-weight-bold"><h5>Общая стоимость ( 2 договора - оборудование с НДС, работы без НДС ): {{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPriceNoVAT() }}р.</h5></td>
            </tr>
        </table>
        </div>
        <div class="row">
            <div class="bottom-bg row align-items-center col">
                <p class="font-weight-bold mt-1">www.aronasb.ru</p>
            </div>
            <div class="logo-bottom row align-items-center col"></div>
        </div>
    </div>

@endsection