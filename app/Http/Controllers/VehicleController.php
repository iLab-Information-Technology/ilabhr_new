<?php

namespace App\Http\Controllers;

use App\DataTables\VehicleDataTable;
use App\DataTables\VehicleDriverDataTable;
use App\Helper\Files;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Http\Requests\Admin\Vehicles\StoreRequest;
use App\Http\Requests\Admin\Vehicles\UpdateRequest;
use App\Models\Driver;
use App\Models\MakeModel;
use App\Models\RentalCompany;
use App\Models\Vehicle;
use App\Models\VehicleReplacement;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleController extends AccountBaseController
{

    public function __construct(private VehicleDriverDataTable $vehicleDriverDatatable)
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.drivers';
        // $this->middleware(functio ($request, $next) {
        // abort_403(!in_array('drivers', $this->user->modules));
        // return $next($request);
        // });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VehicleDataTable $dataTable)
    {
        // return "Hello World";
        $this->pageTitle = 'app.menu.vehicle';
        return $dataTable->render('vehicles.index', $this->data);
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
    public function store(StoreRequest $request)
    {
        // return $request->all();
        DB::beginTransaction();
        try {
            $vehicle = Vehicle::create($request->all());

            if ($request->status == 3) {
                VehicleReplacement::create([
                    'vehicle_id' => $vehicle->id,
                    'date' => $request->replacement_date,
                    'reason' => $request->replacement_reason,
                ]);
            }

            if ($request->driver_id) {
                $vehicle->driver_id = $request->driver_id;
                $vehicle->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support ' . $e->getMessage());
        }

        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vehicles.index')]);
    }

    public function drivers($vehicleId)
    {
        $tab = request('tab');
        $this->activeTab = $tab ?: 'driver';
        $this->view = 'vehicles.ajax.driver';

        // Pass the vehicle ID to the DataTable
        return $this->vehicleDriverDatatable->with('vehicle_id', $vehicleId)->render('vehicles.show', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->vehicle = Vehicle::with('driver', 'images')->findOrFail($id);
        $this->pageTitle = $this->vehicle->vehicle_plate_number;

        // return $this->drivers($id);

        $tab = request('tab');

        switch ($tab) {
            case 'driver':
                return $this->drivers($id);
            case 'documents':
                $this->view = 'vehicles.ajax.documents';
                break;
            default:
                $this->view = 'vehicles.ajax.profile';
                break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('vehicles.show', $this->data);
    }

    public function linkDriver($id)
    {
        $this->pageTitle = __('app.menu.linkDriver');
        $this->vehicle = Vehicle::find($id);
        $this->countries = countries();

        $this->view = 'vehicles.ajax.link-driver';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('vehicles.link-driver', $this->data);
    }

    public function submitDriverLink(Request $request, Vehicle $vehicle)
    {
        // return $request->all();


        if ($vehicle->driver_id) {
            return Reply::error('Driver is Already linked to this vehicle');
        } else {
            $vehicle->update([
                'driver_id' => $request->driver_id,
            ]);

            return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vehicles.index')]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $this->pageTitle = __('app.update');

        $this->drivers = Driver::get();
        $this->makeModel = MakeModel::get();
        $this->rentalCompany = RentalCompany::get();
        $this->vehicleType = VehicleType::get();

        $this->vehicle = $vehicle;

        $tab = request('tab');

        switch ($tab) {
            case 'istimarah':
                $this->view = 'vehicles.ajax.istimarah';
                break;
            case 'tamm_report':
                $this->view = 'vehicles.ajax.tamm-report';
                break;

            case 'other-report':
                $this->view = 'vehicles.ajax.other-report';
                break;

            case 'inside-pictures':
                $this->view = 'vehicles.ajax.inside-picture';
                break;
            case 'outside-picture':
                $this->view = 'vehicles.ajax.outside-picture';
                break;
            default:
                $this->view = 'vehicles.ajax.edit';
                break;
        }

        if (request()->ajax()) {
            if (!$tab) {
                $html = view($this->view, $this->data)->render();
                return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            }

            return view($this->view, $this->data);
        }

        return view('vehicles.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Vehicle $vehicle)
    {
        // return $request->all();
        $validated = $request->validated();

        if(isset($request->tamm_expiry_date)){
            $validated['tamm_expiry_date'] = $request->tamm_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->tamm_expiry_date)->format('Y-m-d') : null;
        }

        if(isset($request->istimarah_expiry_date)){
            $validated['istimarah_expiry_date'] = $request->istimarah_expiry_date ? Carbon::createFromFormat($this->company->date_format, $request->istimarah_expiry_date)->format('Y-m-d') : null;
        }

        
        if ($request->hasFile('istimarah')) {
            $validated['istimarah'] = Files::uploadLocalOrS3($request->istimarah, 'istimarah', 300);
        }
        
        if ($request->istimarah_delete == 'yes') {
            Files::deleteFile($vehicle->istimarah, 'istimarah');
            $vehicle->istimarah = null;
            $validated['istimarah_expiry_date'] = null;
        }

        if ($request->hasFile('tamm_report')) {
            $validated['tamm_report'] = Files::uploadLocalOrS3($request->tamm_report, 'tamm-report', 300);
        }

        if ($request->tamm_report_delete == 'yes') {
            Files::deleteFile($vehicle->tamm_report, 'tamm-report');
            $vehicle->tamm_report = null;
            $validated['tamm_expiry_date'] = null;
        }

        if ($request->hasFile('other_report')) {
            $validated['other_report'] = Files::uploadLocalOrS3($request->other_report, 'other-report', 300);
        }

        if ($request->other_report_delete == 'yes') {
            Files::deleteFile($vehicle->other_report, 'other-report');
            $vehicle->other_report = null;
        }

        if ($request->hasFile('inside_images')) {
            $existingImages = $vehicle->images()->where('type', 'inside')->get();
            if ($existingImages) {
                foreach ($existingImages as $image) {
                    // Delete the file from the filesystem
                    $filePath = Files::deleteFile($image->image, 'vehicle-images');
                }

                $vehicle->images()->where('type', 'inside')->delete();
            }

            foreach ($request->file('inside_images') as $file) {
                $imagePath = Files::uploadLocalOrS3($file, 'vehicle-images', 300);
                // Create or update image record
                $vehicle->images()->updateOrCreate(
                    ['type' => 'inside', 'image' => $imagePath]
                );
            }
        }

        if ($request->inside_image_delete == 'yes') {
            $imagePath = Files::deleteFile($vehicle->images[0]->firstWhere('type', 'inside')->image, 'vehicle-images');

            $vehicle->images()->where('type', 'inside')->delete();
        }

        if ($request->hasFile('outside_images')) {
            $existingImages = $vehicle->images()->where('type', 'outside')->get();
            if ($existingImages) {
                foreach ($existingImages as $image) {
                    // Delete the file from the filesystem
                    $filePath = Files::deleteFile($image->image, 'vehicle-images');
                }

                $vehicle->images()->where('type', 'outside')->delete();
            }

            foreach ($request->file('outside_images') as $file) {
                $imagePath = Files::uploadLocalOrS3($file, 'vehicle-images', 300);
                // Create or update image record
                $vehicle->images()->updateOrCreate(
                    ['type' => 'outside', 'image' => $imagePath]
                );
            }
        }

        if ($request->outside_image_delete == 'yes') {
            $imagePath = Files::deleteFile($vehicle->images[0]->firstWhere('type', 'outside')->image, 'vehicle-images');

            $vehicle->images()->where('type', 'outside')->delete();
        }

        if ($request->status == 3) {
            VehicleReplacement::create([
                'vehicle_id' => $vehicle->id,
                'date' => $request->replacement_date,
                'reason' => $request->replacement_reason,
            ]);
        }


        $vehicle->update($validated);

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vehicles.index')]);
    }

    public function uploadImages(Request $request, Vehicle $vehicle)
    {
        // return response()->json(['request' => $request->all()]);

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vehicles.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
