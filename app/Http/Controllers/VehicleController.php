<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Models\Driver;
use App\Models\MakeModel;
use App\Models\RentalCompany;
use App\Models\VehicleType;

class VehicleController extends AccountBaseController
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return "Hello World";
        $this->pageTitle = 'app.menu.vehicle';
        return view('vehicles.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.menu.AddVehicle');
        $this->view = 'vehicles.ajax.create';
        $this->drivers = Driver::get();
        $this->makeModel = MakeModel::get();
        $this->rentalCompany = RentalCompany::get();
        $this->vehicleType = VehicleType::get();

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('vehicles.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
