<?php

namespace App\Http\Controllers;

use App\DataTables\RentalCompanyDataTable;
use App\Helper\Reply;
use App\Http\Requests\Admin\RentalCompany\StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Files;
use App\Http\Requests\Admin\RentalCompany\UpdateRequest;
use App\Models\RentalCompany;

class RentalCompanyController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(RentalCompanyDataTable $dataTable)
    {
        $this->pageTitle = 'app.menu.rental_company';
        return $dataTable->render('rental-company.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.menu.addRentalCompany');
        $this->view = 'rental-company.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('rental-company.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $image = Files::uploadLocalOrS3($request->image, 'rental-company', 300);
            }

            RentalCompany::create([
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

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vehicle-rental-company.index')]);
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
    public function edit(RentalCompany $vehicle_rental_company)
    {
        $this->pageTitle = __('app.update');
        $this->rentalCompany = $vehicle_rental_company;
        $this->view = 'rental-company.ajax.edit';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('rental-company.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, RentalCompany $vehicle_rental_company)
    {
        if ($request->image_delete == 'yes') {
            Files::deleteFile($vehicle_rental_company->image, 'rental-company');
            $vehicle_rental_company->image = null;
        }

        $vehicle_rental_company->update([
            'name' => $request->name,
        ]);

        if ($request->hasFile('image')) {
            $image = Files::uploadLocalOrS3($request->image, 'rental-company', 300);
            $vehicle_rental_company->image = $image;
            $vehicle_rental_company->save();
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vehicle-rental-company.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->makeModel = RentalCompany::findOrFail($id);

        Files::deleteFile($this->makeModel->image, 'rental-company');

        RentalCompany::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
