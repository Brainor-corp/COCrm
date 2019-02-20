@extends('layouts.kpPageLayout')

@section('content')

    <div class="container content-border">
        <div class="px-4 py-2">
            <div class="row head-bg">
                <div class="col-3 mt-3">
                    <img class="img-fluid" src="{{ asset('images/logo.png') }}" alt="">
                </div>
                <div class="col-5 mt-3">
                    <p class="mb-0 font-weight-bold">Продажа и установка систем безопасности</p>
                    <p class="mb-0 font-weight-bold">в Москве и Московской области</p>
                </div>
                <div class="col-4 mt-3">
                    <div class="col-12 float-right">
                        <p class="mb-0 text-white h4 text-right float-right"><img class="svg-icon mx-2" src="{{ asset('images/svg/phone-solid.svg') }}" alt="phone">{{ $offersGroup->user->tel }}</p>
                    </div>
                    <div class="float-right">
                        <p class="mb-0 text-white h4 text-right"><img class="svg-icon mx-2" src="{{ asset('images/svg/envelope-regular.svg') }}" alt="email">{{ $offersGroup->user->contact_email }}</p>
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
            <div class="offer">
                <div class="offer-name mt-2 mb-4 col-12">
                    <h3>
                        {{ $offer->name }}
                        <span class="text-right">{{ \Carbon\Carbon::parse($offersGroup->created_at)->format('d.m.Y') }}</span>
                    </h3>
                </div>
                <div class="check-border p-3 my-4">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="6" class="border-top-0 border-bottom-0 h4">Оборудование и расходные материалы</th>
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
                            @if($equipment->type->slug != 'rashodnye-materialy')
                                @php($totalEquipmentSum += $equipment->pivot->counted_price * $equipment->pivot->quantity)
                                <tr>
                                    <td><img src="https://via.placeholder.com/100" alt=""></td>
                                    <td class="align-middle" colspan="2"><b>{{ $equipment->name }}</b> {{ $equipment->description }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->quantity }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->counted_price }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->counted_price * $equipment->pivot->quantity }}</td>
                                </tr>
                            @else
                                @php($consumableSum += $equipment->pivot->counted_price * $equipment->pivot->quantity)
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
                            <td colspan="2" class="check-in-card-border my-4 p-total"><h3>Всего за оборудование</h3></td>
                            <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h3>{{ $totalEquipmentSum + $consumableSum }}р.</h3></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="check-border p-3 my-4">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th colspan="5" class="border-top-0 border-bottom-0 h4">Монтажные и пуско-наладочные работы</th>
                        </tr>
                        <tr>
                            <th scope="col" class="text-gray border-top-0 border-bottom-0" colspan="2">Наименование работ</th>
                            <th scope="col" nowrap class="text-center text-gray border-top-0 border-bottom-0">Ед. изм.</th>
                            <th scope="col" nowrap class="text-center text-gray border-top-0 border-bottom-0">Кол-во</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($offersGroup->equipment as $work)
                            <tr>
                                <td class="align-middle">{{ $work->name }}</td>
                                <td class="align-middle text-center"></td>
                                <td class="align-middle text-center">{{ $work->points }}</td>
                                <td class="align-middle text-center">{{ $work->pivot->quantity }}</td>
                            </tr>
                        @endforeach
                            <tr>
                                <td colspan="2" class="check-in-card-border my-4 p-total"><h4>Всего за работы с НДС:</h4></td>
                                <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h4> {{ $offersGroup->getTotalWorkPrice() }}р.</h4></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="check-in-card-border my-4 p-total"><h4>Всего за работы без НДС, Доп. скидка - <span class="text-danger">{{ $offersGroup->getAdditionalDiscount() }}</span></h4></td>
                                <td colspan="5" class="check-in-card-border my-4 text-right p-total"><h4>{{ $offersGroup->getTotalWorkPriceNoVAT() }}р.</h4></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
        @endforeach
        <table class="table table-borderless">
            <tr>
                <td class="text-right my-4 font-weight-bold p-total"><h4>Общая стоимость ( 1 договор с НДС ):{{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPrice() }}р.</h4></td>
            </tr>
            <tr>
                <td class="text-right my-4 font-weight-bold p-total"><h4>Общая стоимость ( 2 договора - оборудование с НДС, работы без НДС ): {{ $totalEquipmentSum + $consumableSum + $offersGroup->getTotalWorkPriceNoVAT() }}р.</h4></td>
            </tr>
        </table>
        <div class="row">
            <div class="bottom-bg row align-items-center col">
                <p class="font-weight-bold mt-1">www.aronasb.ru</p>
            </div>
            <div class="logo-bottom row align-items-center col"></div>
        </div>
    </div>
@endsection

@section('pdfButton')
    <div class="container">
        <div class="col-12">
            <a href="{{route('downloadAsPdf', ['uuid' => $offersGroup->uuid])}}" class="btn pdf-btn btn-lg btn-primary create-btn d-flex align-items-center justify-content-center mx-auto my-5">Скачать как pdf</a>
        </div>
    </div>
@endsection