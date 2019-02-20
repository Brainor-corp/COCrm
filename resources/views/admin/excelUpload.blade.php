<form action="{{ route('excel-upload') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <input type="file" name="file" class="form-control-file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
    </div>
    <button class="btn btn-secondary" type="submit">Загрузить</button>
</form>