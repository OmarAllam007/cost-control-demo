<tr>
    <td>
        <input class="form-control input-sm" type="hidden" name="resources[{{$index}}][std_activity_resource_id]"
               id="resourceId{{$index}}" value="{{old("resources.$index.std_activity_resource_id")}}"
               j-model="std_activity_resource_id" readonly>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][resource_type]"
               id="resourceType{{$index}}" value="{{old("resources.$index.resource_type")}}" j-model="resource_type"
               readonly>
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][resource_name]"
               id="resourceType{{$index}}" value="{{old("resources.$index.resource_name")}}" j-model="resource_name"
               readonly>
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][budget_qty]"
               id="budgetQuantity{{$index}}" value="{{old("resources.$index.budget_qty")}}" j-model="budget_qty">
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][eng_qty]"
               id="engQuantity{{$index}}" value="{{old("resources.$index.eng_qty")}}" j-model="eng_qty">
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][resource_waste]"
               id="resourceWaste{{$index}}" value="{{old("resources.$index.resource_waste")}}" j-model="resource_waste">
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][labor_count]"
               id="laborsCount{{$index}}" value="{{old("resources.$index.labor_count")}}" j-model="labor_count">
    </td>

    <td>
        {{Form::select("resources[$index][productivity_id]", App\Productivity::options(), null, ['class' => 'form-control input-sm', 'j-model' => 'productivity_id', 'id' => "ProductivityID$index"])}}
    </td>

    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][remarks]"
               id="laborsCount{{$index}}" value="{{old("resources.$index.remarks")}}" j-model="remarks">
    </td>
    <td>
        <input class="form-control input-sm" type="text" name="resources[{{$index}}][equation]"
               id="laborsCount{{$index}}" value="{{old("resources.$index.equation")}}" j-model="equation">
    </td>
</tr>