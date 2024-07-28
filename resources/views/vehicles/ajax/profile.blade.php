<script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
<style>
    .card-img {
        width: 120px;
        height: 120px;
    }

    .card-img img {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    .appreciation-count {
        top: -6px;
        right: 10px;
    }

</style>

<div class="d-lg-flex">

    <div class="w-100 py-0 py-lg-3 py-md-0">
        <!-- ROW START -->
        <div class="row">
            <!--  USER CARDS START -->
            <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
                <div class="row">
                    <div class="col-xl-7 col-md-6 mb-4 mb-lg-0">
{{-- 
                        @if ($driver->employeeDetail->about_me != '')
                            <x-cards.data :title="__('app.about')" class="mt-4">
                                <div>{{ $driver->employeeDetail->about_me }}</div>
                            </x-cards.data>
                        @endif --}}


                        <x-cards.data :title="__('modules.client.profileInfo')" class=" mt-4">
                            {{-- <x-cards.data-row :label="__('modules.employees.employeeId')"
                                :value="(!is_null($driver->employeeDetail) && !is_null($driver->employeeDetail->employee_id)) ? ($driver->employeeDetail->employee_id) : '--'" />

                            <x-cards.data-row :label="__('modules.employees.fullName')"
                                :value="$driver->name" />

                            <x-cards.data-row :label="__('app.designation')"
                                :value="(!is_null($driver->employeeDetail) && !is_null($driver->employeeDetail->designation)) ? ($driver->employeeDetail->designation->name) : '--'" />

                            <x-cards.data-row :label="__('app.department')"
                                :value="(isset($driver->employeeDetail) && !is_null($driver->employeeDetail->department) && !is_null($driver->employeeDetail->department)) ? ($driver->employeeDetail->department->team_name) : '--'" />

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block text-capitalize">
                                    @lang('modules.employees.gender')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    <x-gender :gender='$driver->gender' />
                                </p>
                            </div> --}}


                            {{-- @php
                                $currentyearJoiningDate = \Carbon\Carbon::parse(now(company()->timezone)->year.'-'.$driver->employeeDetail->joining_date->translatedFormat('m-d'));
                                if ($currentyearJoiningDate->copy()->endOfDay()->isPast()) {
                                    $currentyearJoiningDate = $currentyearJoiningDate->addYear();
                                }
                                $diffInHoursJoiningDate = now(company()->timezone)->floatDiffInHours($currentyearJoiningDate, false);
                            @endphp

                            <x-cards.data-row :label="__('modules.employees.workAnniversary')" :value="(!is_null($driver->employeeDetail) && !is_null($driver->employeeDetail->joining_date)) ? (($diffInHoursJoiningDate > -23 && $diffInHoursJoiningDate <= 0) ? __('app.today') : $currentyearJoiningDate->longRelativeToNowDiffForHumans()) : '--'" /> --}}

                                <x-cards.data-row :label="__('modules.vehicles.date')"
                                :value="is_null($vehicle->date) ? '--' : date('d F', strtotime($vehicle->date))" />

                                <x-cards.data-row :label="__('modules.vehicles.ilab_id')"
                                :value="$vehicle->ilab_id" />

                                <x-cards.data-row :label="__('app.menu.vehicleType')"
                                :value="$vehicle->vehicleType->name" />

                                <x-cards.data-row :label="__('modules.vehicles.number_plate')"
                                :value="$vehicle->vehicle_plate_number" />

                                <x-cards.data-row :label="__('app.menu.makeModel')"
                                :value="$vehicle->makeModel->name" />

                                <x-cards.data-row :label="__('app.menu.rentalCompany')"
                                :value="$vehicle->rentalCompany->name" />

                                <x-cards.data-row :label="__('modules.vehicles.color')"
                                    :value="$vehicle->color ?? '--'" />


                        </x-cards.data>


                    </div>

                    <div class="col-xl-5 col-lg-6 col-md-6">

                        {{-- @if ($showFullProfile)
                             <x-cards.data class="mb-4" :title="__('modules.appreciations.appreciation')">
                                @forelse ($driver->appreciationsGrouped as $item)
                                <div class="float-left position-relative mb-2" style="width: 50px" data-toggle="tooltip" data-original-title="@if(isset($item->award->title)){{  $item->award->title }} @endif">
                                    @if(isset($item->award->awardIcon->icon))
                                        <x-award-icon :award="$item->award" />
                                    @endif
                                    <span class="position-absolute badge badge-secondary rounded-circle border-additional-grey appreciation-count">{{ $item->no_of_awards }}</span>
                                </div>
                                @empty
                                    <x-cards.no-record icon="medal" :message="__('messages.noRecordFound')" />
                                @endforelse
                            </x-cards.data>
                        @endif --}}

                            {{-- <div class="row">
                                @if (in_array('attendance', user_modules()))
                                    <div class="col-xl-6 col-sm-12 mb-4">
                                        <x-cards.widget :title="__('modules.dashboard.lateAttendanceMark')"
                                            :value="$lateAttendance" :info="__('modules.dashboard.thisMonth')"
                                            icon="map-marker-alt" />
                                    </div>
                                @endif
                                @if (in_array('leaves', user_modules()))
                                    <div class="col-xl-6 col-sm-12 mb-4">
                                        <x-cards.widget :title="__('modules.dashboard.leavesTaken')" :value="$leavesTaken"
                                            :info="__('modules.dashboard.thisMonth')" icon="sign-out-alt" />
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if (in_array('tasks', user_modules()))
                                    <div class="col-md-12 mb-4">
                                        <x-cards.data :title="__('app.menu.tasks')" padding="false">
                                            <x-pie-chart id="task-chart" :labels="$taskChart['labels']"
                                                :values="$taskChart['values']" :colors="$taskChart['colors']" height="250"
                                                width="300" />
                                        </x-cards.data>
                                    </div>
                                @endif
                                @if (in_array('tickets', user_modules()))
                                    <div class="col-md-12 mb-4">
                                        <x-cards.data :title="__('app.menu.tickets')" padding="false">
                                            <x-pie-chart id="ticket-chart" :labels="$ticketChart['labels']"
                                                :values="$ticketChart['values']" :colors="$ticketChart['colors']"
                                                height="250" width="300" />
                                        </x-cards.data>
                                    </div>
                                @endif
                            </div> --}}

                    </div>
                </div>
            </div>
            <!--  USER CARDS END -->

        </div>
        <!-- ROW END -->
    </div>
</div>
