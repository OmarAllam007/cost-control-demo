<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Filter\BusinessPartnerFilter;
use App\Http\Requests\WipeRequest;
use Illuminate\Http\Request;

class BusinessPartnerController extends Controller
{

    protected $rules = ['name' => 'required|unique:business_partners'];

    public function index()
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new BusinessPartnerFilter(BusinessPartner::query(), session('filters.partners'));
        $businessPartners = $filter->filter()->orderBy('name')->paginate(100);
        return view('business-partner.index', compact('businessPartners'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('business-partner.create');
    }

    public function store(Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('business-partner.edit', compact('business_partner'));
    }

    public function update(BusinessPartner $business_partner, Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $business_partner->update($request->all());

        flash('Business partner has been saved', 'success');

        return \Redirect::route('business-partner.index');
    }

    public function destroy(BusinessPartner $business_partner)
    {
        if (\Gate::denies('delete', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $business_partner->delete();

        flash('Business partner has been deleted', 'success');

        return \Redirect::route('business-partner.index');
    }

    public function filter(Request $request)
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $data = $request->only(['name', 'type']);
        \Session::set('filters.partners', $data);
        return \Redirect::back();
    }
    function wipe(WipeRequest $request)
    {
        \DB::table('business_partners')->delete();
        flash('All Partners have been deleted', 'info');
        return \Redirect::route('business-partner.index');
    }
}
