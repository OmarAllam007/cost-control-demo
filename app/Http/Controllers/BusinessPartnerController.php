<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use Illuminate\Http\Request;

class BusinessPartnerController extends Controller
{

    protected $rules = ['name' => 'required|unique:business_partners'];

    public function index()
    {
        $businessPartners = BusinessPartner::select('id','name','type')->groupBy('name')
            ->paginate();

        return view('business-partner.index', compact('businessPartners'));
    }

    public function create()
    {
        return view('business-partner.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        BusinessPartner::create($request->all());

        flash('Business partner has been saved', 'success');

        return \Redirect::route('business-partner.index');
    }

    public function show(BusinessPartner $business_partner)
    {
        return view('business-partner.show', compact('business_partner'));
    }

    public function edit(BusinessPartner $business_partner)
    {
        return view('business-partner.edit', compact('business_partner'));
    }

    public function update(BusinessPartner $business_partner, Request $request)
    {
        $this->validate($request, $this->rules);

        $business_partner->update($request->all());

        flash('Business partner has been saved', 'success');

        return \Redirect::route('business-partner.index');
    }

    public function destroy(BusinessPartner $business_partner)
    {
        $business_partner->delete();

        flash('Business partner has been deleted', 'success');

        return \Redirect::route('business-partner.index');
    }
}
