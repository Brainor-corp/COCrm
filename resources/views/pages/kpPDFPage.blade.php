@extends('layouts.kpPageLayout')

@section('pdfFontStyle')
    <style>
        *{ font-family: DejaVu Sans !important;}
    </style>
@endsection

@section('content')

    <div class="container">
        <div class="offerGroup-name px-4 py-2">
            <div class="row">
                <div class="col-12">
                    <h3>
                        Коммерческое предложение {{ $offersGroup->name }}
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-right">
                    <h3>
                        {{ \Carbon\Carbon::parse($offersGroup->created_at)->format('d.m.Y') }}
                    </h3>
                </div>
            </div>
        </div>

        @foreach($offersGroup->offers as $offer)
            <div class="page-break"></div>

            <div class="offer my-5">
                <div class="offer-name mt-3">
                    <h4>
                        {{ $offer->name }}
                    </h4>
                </div>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th colspan="6" class="bg-gray">1. Оборудование и расходные материалы</th>
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
                        @foreach($offer->equipments as $equipment)
                            @if($equipment->class == 'equipment')
                                @php($totalEquipmentSum += $equipment->pivot->price * $equipment->pivot->quantity)
                                <tr>
                                    <td><img src="https://via.placeholder.com/100" alt=""></td>
                                    <td class="align-middle" colspan="2"><b>{{ $equipment->name }}</b> {{ $equipment->description }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->quantity }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->price }}</td>
                                    <td class="align-middle text-center">{{ $equipment->pivot->price * $equipment->pivot->quantity }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="6" class="bg-light-blue text-right"><h4>Всего за оборудование {{ $totalEquipmentSum }}р.</h4></td>
                        </tr>
                    </tbody>
                </table>
                <div class="page-break"></div>
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
                            <td colspan="5" class="bg-beige text-right"><h4>Всего за работы ( с НДС ): {{ $totalWorkSum }}р.</h4></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="bg-light-blue text-right"><h4>Всего за работы ( без НДС, Доп. скидка - ??? ): ???р.</h4></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <table class="table table-sm">
            <tr>
                <td colspan="5" class="bg-beige text-right"><h4>Общая стоимость ( 1 договор с НДС ): ???р.</h4></td>
            </tr>
            <tr>
                <td colspan="5" class="bg-light-blue text-right"><h4>Общая стоимость ( 2 договора - оборудование с НДС, работы без НДС ): ???р.</h4></td>
            </tr>
        </table>

    </div>

@endsection