<?php

namespace App\Http\Controllers;

use App\DataTables\VehicleTypeDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\VehicleType\StoreRequest;
use App\Http\Requests\Admin\VehicleType\UpdateRequest;
use App\Models\VehicleType;
use App\Helper\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleTypeController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(VehicleTypeDataTable $dataTable)
    {
        $this->pageTitle = 'app.menu.vehicle_type';
        return $dataTable->render('vehicle-types.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.menu.addVehicleType');
        $this->view = 'vehicle-types.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('vehicle-types.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $image = Files::uploadLocalOrS3($request->image, 'vehicle-type', 300);
            }
            VehicleType::create([
                'name' => $request->name,
                'image' => $image,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support '. $e->getMessage());
        }


        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vehicle-types.index')]);
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
    public function edit(VehicleType $vehicle_type)
    {
        $this->pageTitle = __('app.update');

        $this->vehicle_type = $vehicle_type;

        $this->view = 'vehicle-types.ajax.edit';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }
        return view('vehicle-types.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, VehicleType $vehicle_type)
    {
        if ($request->image_delete == 'yes') {
            Files::deleteFile($vehicle_type->image, 'vehicle-type');
            $vehicle_type->image = null;
        }

        if ($request->hasFile('image')) {
            $image = Files::uploadLocalOrS3($request->image, 'vehicle-type', 300);
            $vehicle_type->image = $image;
            $vehicle_type->save();
        }

        $vehicle_type->update([
            'name' => $request->name,
        ]);
        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vehicle-types.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->vehicleType = VehicleType::findOrFail($id);

        Files::deleteFile($this->vehicleType->image, 'vehicle-type');

        VehicleType::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
