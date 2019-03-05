<label for="input_equipment">Оборудование</label>

<select name="equipment[]" id="input_equipment" class="ajax-multiselect-equipment w-100" multiple>
    @if($type)
        @foreach($type->equipment as $equipment)
            <option value="{{ $equipment->id }}" selected>{{$equipment->code . " — " . $equipment->name }}</option>
        @endforeach
    @endif
</select>