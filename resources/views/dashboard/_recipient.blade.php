<tr>
    <td class="{{$errors->first("recipients.$key.name", 'has-error')}}">
        <input id="recipients-{{$key}}_name" name="recipients[{{$key}}][name]" type="text" class="form-control input-sm" placeholder="Recipient Name" required>
    </td>

    <td class="{{$errors->first("recipients.$key.email", 'has-error')}}">
        <input id="recipients-{{$key}}_email" name="recipients[{{$key}}][email]" type="email" class="form-control input-sm" placeholder="Recipient Email" required>
    </td>

    <td class="text-center">
        <button class="btn btn-warning btn-sm delete-recipient"><i class="fa fa-trash"></i></button>
    </td>
</tr>