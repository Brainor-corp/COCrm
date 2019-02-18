<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <td>#</td>
            <td>Наименование</td>
            <td>Уникальный id</td>
            <td>Ред.</td>
            <td>Удал.</td>
        </tr>
    </thead>
    <tbody>
        @foreach($offerGroups as $key => $offerGroup)
            <tr>
                <td>{{$key}}</td>
                <td>{{$offerGroup->name}}</td>
                <td>{{$offerGroup->uuid}}</td>
                <td>
                    <a class="text-success" href="{{route('/', ['id' => $offerGroup->id])}}">
                        Ред.
                    </a>
                </td>
                <td>{{$offerGroup->uuid}}</td>
            </tr>
        @endforeach
    </tbody>
</table>