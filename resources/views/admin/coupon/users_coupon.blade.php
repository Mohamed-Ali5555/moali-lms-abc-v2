@extends('layouts.admin')
@push('title', get_phrase('Coupon'))
@push('meta')@endpush
@push('css')@endpush


@section('content')
    <!-- Mani section header and breadcrumb -->
    <div class="ol-card radius-8px">
        <div class="ol-card-body my-3 py-12px px-20px">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
                <h4 class="title fs-16px">
                    <i class="fi-rr-settings-sliders me-2"></i>
                    <span>{{ get_phrase('Coupon') }}</span>
                </h4>
                @if (has_permission('admin.coupon.create'))
                    <a href="{{ route('admin.coupon.create') }}" class="btn ol-btn-outline-secondary d-flex align-items-center cg-10px">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add Coupon') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Start Admin area -->
    <div class="row">
        <div class="col-12">
            <div class="ol-card">
                <div class="ol-card-body p-3">
                    <div class="row print-d-none mb-3 mt-3 row-gap-3">
                        <div class="col-md-6 pt-2 pt-md-0">
                            <div class="custom-dropdown">
                                <button class="dropdown-header btn ol-btn-light">
                                    {{ get_phrase('Export') }}
                                    <i class="fi-rr-file-export ms-2"></i>
                                </button>
                                <ul class="dropdown-list">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'coupon-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3 mt-md-0">
                            <form action="{{ route('admin.coupon.users_coupon',$id) }}" method="get" class="d-flex gap-3 justify-content-end">
                                <div class="search-input flex-grow-1">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ get_phrase('Search coupon') }}" class="ol-form-control form-control" />

                                </div>
                                <button type="submit" class="btn ol-btn-primary" id="submit-button">{{ get_phrase('Search') }}</button>
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                      <!-- Table -->
                    @if(count($log) > 0)
                    {{-- @if (count($users) > 0) --}}
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($log) . ' ' . get_phrase('of') . ' ' . ' ' . get_phrase('data') }}
                            </p>
                        </div>
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('Name') }}</th>
                                        <th scope="col">{{ get_phrase('Phone') }}</th>
                                        <th scope="col">{{ get_phrase('Used Amount') }}</th>
                                        <th scope="col">{{ get_phrase('Remaining Amount') }}</th>
                                        <th scope="col">{{ get_phrase('Used At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($log as $key => $row)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ ++$key }}</p>
                                            </th>
                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-100px">
                                                    <div class="dAdmin_profile_img">
                                                        <img class="img-fluid rounded-circle image-45" width="45" height="45" src="{{ get_image($row->photo) }}" />
                                                    </div>
                                                    <div class="ms-1">
                                                        <h4 class="title fs-14px">{{ $row->user->name }}</h4>
                                                        <p class="sub-title2 text-12px">{{ $row->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->user->phone }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->used_amount }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->remaining_after }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->created_at }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @include('admin.no_data')
                    @endif

                    <!-- Data info and Pagination -->
                    @if (count($log) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($log) . ' ' . get_phrase('of') . ' ' .  ' ' . get_phrase('data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Admin area -->
@endsection
@push('js')@endpush
