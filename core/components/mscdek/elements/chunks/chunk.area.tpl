<select id="[[+type]]_select" name="[[+type]]" class="form-control [[+errors.[[+type]]]]">
    <option value="">  Выберите город </option>
    [[!msCDEKAreas? &type=`[[+type]]`]]
</select>
