@extends('layouts.admin')
@push('title', get_phrase('Student'))
@push('meta')
@endpush
@push('css')
@endpush
@section('content')
    <div class="ol-card radius-8px">
        <div class="ol-card-body my-3 py-12px px-20px">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
                <h4 class="title fs-16px">
                    <i class="fi-rr-settings-sliders me-2"></i>
                    {{ get_phrase('Student not enroll in course ') }}
                </h4>

             
            </div>
        </div>
    </div>
    <div class="ol-card p-4">
        <div class="ol-card-body">
            <div class="row print-d-none mb-3 mt-3 row-gap-3">
                <div class="col-md-6  pt-2 pt-md-0">
                    <div class="custom-dropdown">
                        <button class="dropdown-header btn ol-btn-light">
                            {{ get_phrase('Export') }}
                            <i class="fi-rr-file-export ms-2"></i>
                        </button>
                        <ul class="dropdown-list">
                            <li>
                                <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'student-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <form class="form-inline" action="{{ route('admin.course.show_users',$id) }}" method="get">
                        <div class="row row-gap-3">
                            <div class="col-md-9">
                                <input type="text" class="form-control ol-form-control" name="search" value="{{ request('search') }}" placeholder="{{ get_phrase('Search user') }}" />
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn ol-btn-primary w-100" id="submit-button"> {{ get_phrase('Search') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Table -->
                    @if ($users->count() > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . $users->count() . ' ' . get_phrase('of') . ' ' . $users->total() . ' ' . get_phrase('data') }}
                            </p>
                        </div>
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('Name') }}</th>
                                        <th scope="col">{{ get_phrase('Phone') }}</th>
                                        <th scope="col">{{ get_phrase('category') }}</th>
                                        <th scope="col">{{ get_phrase('goverment') }}</th>
                                        <th scope="col">{{ get_phrase('رقم الإقامة') }}</th>
                                        <th scope="col">{{ get_phrase('address') }}</th>
                                        <th scope="col">{{ get_phrase('Wallet balane') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $row)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ $users->firstItem() + $key }}</p>
                                            </th>
                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-100px">
                                                    <div class="dAdmin_profile_img">
                                                        <img class="img-fluid rounded-circle image-45" width="45" height="45" src="{{ get_image($row->photo) }}" />
                                                    </div>
                                                    <div class="ms-1">
                                                        <h4 class="title fs-14px">{{ $row->name }}</h4>
                                                        <p class="sub-title2 text-12px">{{ $row->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->phone }}</p>
                                                </div>
                                            </td>

                                        
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ optional($row->get_category)->title }}</p>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->goverment }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->national_id }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->address }}</p>
                                                </div>
                                            </td>
                                      

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ number_format($row->wallet,2) }}</p>
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
                    @if ($users->count() > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . $users->count() . ' ' . get_phrase('of') . ' ' . $users->total() . ' ' . get_phrase('data') }}
                            </p>
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection


@push('js')

@endpush
