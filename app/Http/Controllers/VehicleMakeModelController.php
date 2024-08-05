<?php

namespace App\Http\Controllers;

use App\DataTables\VehicleMakeModelDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\VehicleMakeModel\StoreRequest;
use App\Helper\Files;
use App\Http\Requests\Admin\VehicleMakeModel\UpdateRequest;
use App\Models\MakeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VehicleMakeModelController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(VehicleMakeModelDataTable $dataTable)
    {
        $this->pageTitle = 'app.menu.make_model';
        return $dataTable->render('vehicle-make-model.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.menu.addMakeModel');
        $this->view = 'vehicle-make-model.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('vehicle-make-model.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $image = Files::uploadLocalOrS3($request->image, 'make-model', 300);
            }

            MakeModel::create([
                'name' => $request->name,
                'image' => $image,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollback();
        }

        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vehicle-make-model.index')]);
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
    public function edit(MakeModel $vehicle_make_model)
    {
        $this->pageTitle = __('app.update');
        $this->makeModel = $vehicle_make_model;
        $this->view = 'vehicle-make-model.ajax.edit';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('vehicle-make-model.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, MakeModel $vehicle_make_model)
    {
        if ($request->image_delete == 'yes') {
            Files::deleteFile($vehicle_make_model->image, 'make-model');
            $vehicle_make_model->image = null;
        }

        $vehicle_make_model->update([
            'name' => $request->name,
        ]);

        if ($request->hasFile('image')) {
            $image = Files::uploadLocalOrS3($request->image, 'make-model', 300);
            $vehicle_make_model->image = $image;
            $vehicle_make_model->save();
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vehicle-make-model.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->makeModel = MakeModel::findOrFail($id);

        Files::deleteFile($this->makeModel->image, 'make-model');

        MakeModel::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
