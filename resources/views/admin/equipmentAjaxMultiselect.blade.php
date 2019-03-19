<label for="input_equipment">Оборудование</label>

@if($tab)
    <select name="equipment[]" id="input_equipment" data-class="{{ $tab->class }}" class="ajax-multiselect-equipment w-100" multiple>
        @foreach($tab->equipments as $equipment)
            <option value="{{ $equipment->id }}" selected>{{$equipment->code . " — " . $equipment->name }}</option>
        @endforeach
    </select>
@endif
