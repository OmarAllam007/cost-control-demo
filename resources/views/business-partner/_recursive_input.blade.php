<li>
    <div class="tree--item">
        <label class="tree--item--label">
            <input type="radio" class="tree-radio" name="{{$input or 'business_partner_id'}}" value="{{$partner->id}}" {{Form::getValueAttribute(isset($input)? $input : 'business_partner_id') == $partner->id? 'checked' : ''}}>
            <a href="{{$partner->id}}" class="node-label" data-toggle="collapse">{{$partner->name}}</a>
        </label>
    </div>

</li>

