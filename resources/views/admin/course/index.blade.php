@extends('layouts.admin')
@push('title', get_phrase('Course Manager'))
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-e-learning"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Manage Courses') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Create and manage courses across statuses') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.course.create'))
                    <a href="{{ route('admin.course.create') }}" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add New Course') }}</span>
                    </a>
                @endif
            </div>
        </div>

    <div class="row g-2 g-sm-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-4 row-cols-xl-6">
        <div class="col">
            <a href="{{ route('admin.courses', ['status' => 'active']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $active_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Active courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('admin.courses', ['status' => 'inactive']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $inactive_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Inactive courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.courses', ['status' => 'pending']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $pending_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Pending courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.courses', ['status' => 'upcoming']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $upcoming_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Upcoming courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.courses', ['price' => 'free']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $free_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Free courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('admin.courses', ['price' => 'paid']) }}" class="d-block">
                <div class="ol-card card-hover h-100">
                    <div class="ol-card-body px-3 py-12px">
                        <div class="d-flex align-items-center cg-12px">
                            <div>
                                <p class="sub-title fs-14px fw-semibold mb-2">{{ $paid_courses }}</p>
                                <h6 class="title fs-14px mb-1">{{ get_phrase('Paid courses') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Start Admin area -->
    <div class="row">
        <div class="col-12">
            <div class="ol-card">
                <div class="ol-card-body p-3 mb-5">
                    <div class="row mt-3 mb-4">
                        <div class="col-md-6 d-flex align-items-center gap-3">
                            @if (has_permission('admin.course.export'))
                                <div class="custom-dropdown ms-2">
                                    <button class="dropdown-header btn ol-btn-light">
                                        {{ get_phrase('Export') }}
                                        <i class="fi-rr-file-export ms-2"></i>
                                    </button>
                                    <ul class="dropdown-list">
                                        <li>
                                            <a class="dropdown-item export-btn" href="#" onclick="downloadPDF('.print-table', 'course-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item export-btn" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            @if (has_permission('admin.course.filter'))
                                <div class="custom-dropdown dropdown-filter @if (!isset($_GET) || (isset($_GET) && count($_GET) == 0))  @endif">
                                    <button class="dropdown-header btn ol-btn-light">
                                        <i class="fi-rr-filter me-2"></i>
                                        {{ get_phrase('Filter') }}

                                        @if (isset($_GET) && count($_GET))
                                            <span class="text-12px">
                                                ({{count($_GET)}})
                                            </span>
                                        @endif
                                    </button>
                                    <ul class="dropdown-list w-250px">
                                        <li>
                                            <form id="filter-dropdown" action="{{ route('admin.courses') }}" method="get">
                                                <div class="filter-option d-flex flex-column gap-3">
                                                    <div>
                                                        <label for="eDataList" class="form-label ol-form-label">{{ get_phrase('Category') }}</label>
                                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="category" data-placeholder="Type to search...">
                                                            <option value="all">{{ get_phrase('All') }}</option>

                                                            @foreach (App\Models\Category::where('parent_id', 0)->orderBy('title', 'desc')->get() as $category)
                                                                <option value="{{ $category->slug }}"@if (isset($parent_cat) && $parent_cat == $category->slug) selected @endif>
                                                                    {{ $category->title }}</option>

                                                                @foreach ($category->childs as $sub_category)
                                                                    <option value="{{ $sub_category->slug }}"@if (isset($child_cat) && $child_cat == $sub_category->slug) selected @endif>
                                                                        --{{ $sub_category->title }}</option>
                                                                @endforeach
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label for="eDataList" class="form-label ol-form-label">{{ get_phrase('Status') }}</label>
                                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="status" class="ol-select-2" data-placeholder="Type to search...">
                                                            <option value="all">{{ get_phrase('All') }}
                                                            </option>

                                                            <option value="active"@if (isset($status) && $status == 'active') selected @endif>{{ get_phrase('Active') }} </option>
                                                            <option value="inactive"@if (isset($status) && $status == 'inactive') selected @endif>{{ get_phrase('Inactive') }} </option>
                                                            <option value="pending"@if (isset($status) && $status == 'pending') selected @endif>{{ get_phrase('Pending') }} </option>
                                                            <option value="upcoming"@if (isset($status) && $status == 'upcoming') selected @endif>{{ get_phrase('Upcoming') }} </option>
                                                            <option value="private"@if (isset($status) && $status == 'private') selected @endif>{{ get_phrase('Private') }} </option>
                                                            <option value="draft"@if (isset($status) && $status == 'draft') selected @endif>{{ get_phrase('Draft') }} </option>
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="eDataList" class="form-label ol-form-label">{{ get_phrase('Instructor') }}</label>
                                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="instructor" class="ol-select-2" data-placeholder="Type to search...">
                                                            <option value="all">{{ get_phrase('All') }}
                                                            </option>
                                                            @foreach ($instructors as $instructorUser)
                                                                <option value="{{ $instructorUser->id }}"@if (isset($instructor) && $instructor == $instructorUser->id) selected @endif>
                                                                    {{ ucfirst($instructorUser->name) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label for="eDataList" class="form-label ol-form-label">{{ get_phrase('Price') }}</label>
                                                        <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="price" class="ol-select-2" data-placeholder="Type to search...">
                                                            <option value="all">{{ get_phrase('All') }}
                                                            </option>

                                                            <option value="free"@if (isset($price) && $price == 'free') selected @endif>
                                                                {{ get_phrase('Free') }}</option>
                                                            <option value="paid"@if (isset($price) && $price == 'paid') selected @endif>
                                                                {{ get_phrase('Paid') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="filter-button d-flex justify-content-end align-items-center mt-3">
                                                    <button type="submit" class="ol-btn-primary">{{ get_phrase('Apply') }}</button>
                                                </div>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            @if (isset($_GET) && count($_GET) > 0)
                                <a href="{{ route('admin.courses') }}" class="me-2" data-bs-toggle="tooltip" title="{{ get_phrase('Clear') }}"><i class="fi-rr-cross-circle"></i></a>
                            @endif
                        </div>
                        @if (has_permission('admin.course.search'))

                            <div class="col-md-6 mt-3 mt-md-0">
                                <form action="{{ route('admin.courses') }}" method="get">
                                    <div class="row row-gap-3">
                                        <div class="col-md-9">
                                            <div class="search-input flex-grow-1">
                                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ get_phrase('Search Title') }}" class="ol-form-control form-control" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn ol-btn-primary w-100" id="submit-button">{{ get_phrase('Search') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @if ($courses->count() > 0)
                                <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                    <p class="admin-tInfo">
                                        {{ get_phrase('Showing') . ' ' . count($courses) . ' ' . get_phrase('of') . ' ' . $courses->total() . ' ' . get_phrase('data') }}
                                    </p>
                                </div>
                                <div class="table-responsive overflow-auto course_list overflow-auto" id="course_list">
                                    <table class="table eTable eTable-2 print-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ get_phrase('Title') }}</th>
                                                <th scope="col">{{ get_phrase('Category') }}</th>
                                                <th scope="col">{{ get_phrase('السنة الدراسية') }}</th>
                                                <th scope="col">{{ get_phrase('Lesson & Section') }}</th>
                                                <th scope="col">{{ get_phrase('Enrolled Student') }}</th>
                                                <th scope="col">{{ get_phrase('Student Not Enroll') }}</th>

                                                <th scope="col" class="print-d-none">{{ get_phrase('Status') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('الصفحة الرئيسية') }}</th>
                                                <th scope="col">{{ get_phrase('Price') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('Options') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($courses as $key => $row)
                                                <tr>
                                                    <th scope="row">
                                                        <p class="row-number">{{ ++$key }}</p>
                                                    </th>
                                                    <td>
                                                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                            <div class="dAdmin_profile_name">
                                                                <h4 class="title fs-14px">
                                                                    <a href="{{ route('admin.course.edit', [$row->id, 'tab' => 'curriculum']) }}">{{ ucfirst($row->title) }}</a>
                                                                </h4>

                                                                {{-- <a href="{{ route('admin.courses', ['instructor' => $row->user_id]) }}">
                                                                    <p class="sub-title2 text-12px">
                                                                        {{ get_phrase('Instructor') }}:
                                                                        {{ $row->creator->name ?? '' }}</p>
                                                                    <p class="sub-title2 text-12px">{{ get_phrase('Email') }}:
                                                                        {{ $row->creator->email ?? '' }}</p>
                                                                </a> --}}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <a href="{{ route('admin.courses', ['category' => $row->category->slug ?? '']) }}">{{ $row->category->title ?? '' }}</a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            @php
                                                                $academicYear = ($row->category && $row->category->parent_id > 0)
                                                                    ? $row->category->parent
                                                                    : null;
                                                            @endphp
                                                            @if($academicYear)
                                                                <a href="{{ route('admin.courses', ['category' => $academicYear->slug ?? '']) }}">{{ $academicYear->title }}</a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <a href="{{ route('admin.course.edit', [$row->id, 'tab' => 'curriculum']) }}">
                                                                <p>{{ get_phrase('Lesson') }}: {{ $row->lessons_count }} </p>
                                                                <p> {{ get_phrase('Section') }}: {{ $row->sections_count }} </p>
                                                            </a>
                                                        </div>
                                                    </td>


                                                    <td>
                                                        <a href="{{ route('admin.enroll.history', ['id' => $row->id]) }}"
                                                            class="course-metric course-metric--enrolled"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ get_phrase('student enrollemt') }}">
                                                            <span class="course-metric__value">{{ $row->enrollments_count }}</span>
                                                            <span class="course-metric__label">{{ get_phrase('مسجّل') }}</span>
                                                        </a>
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('admin.course.show_users', $row->id) }}"
                                                            class="course-metric course-metric--pending"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ get_phrase('student not enrollemt') }}">
                                                            <span class="course-metric__value">{{ max(0, ($total_students ?? 0) - $row->enrollments_count) }}</span>
                                                            <span class="course-metric__label">{{ get_phrase('غير مسجّل') }}</span>
                                                        </a>
                                                    </td>
                                                    <td class="print-d-none">
                                                        <span class="badge bg-{{ $row->status }}">{{ get_phrase(ucfirst($row->status)) }}</span>
                                                    </td>
                                                    <td class="print-d-none">
                                                        <label class="home-toggle"
                                                            title="{{ get_phrase('إظهار في أحدث الكورسات بالرئيسية') }}">
                                                            <input class="home-toggle__input home-sidebar-toggle"
                                                                type="checkbox"
                                                                data-course-id="{{ $row->id }}"
                                                                @checked((int) ($row->show_on_home ?? 0) === 1)>
                                                            <span class="home-toggle__ui" aria-hidden="true"></span>
                                                            <span class="visually-hidden">
                                                                {{ get_phrase('إظهار في الصفحة الرئيسية') }}
                                                            </span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="dAdmin_info_name min-w-150px">
                                                            @if ($row->is_paid == 0)
                                                                <p class="eBadge ebg-soft-success">
                                                                    {{ get_phrase('Free') }}
                                                                </p>
                                                            @elseif($row->discount_flag == 1)
                                                                <p>{{ $row->discount_price }} <del>{{ $row->price }}</del>L.E</p>
                                                            @else
                                                                <p>{{ $row->price}} L.E</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="print-d-none">

                                                        <div class="dropdown ol-icon-dropdown ol-icon-dropdown-transparent">
                                                            <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <span class="fi-rr-menu-dots-vertical"></span>
                                                            </button>

                                                            <ul class="dropdown-menu">
                                                                {{-- @if (has_permission('admin.course.view_on_front'))
                                                                    <li>
                                                                        <a class="dropdown-item" target="_blank" href="{{ route('course.details', $row->slug) }}">{{ get_phrase('View Course On Frontend') }}</a>
                                                                    </li>
                                                                @endif --}}
                                                                {{-- @if (has_permission('admin.course.course_playing'))

                                                                    <li>
                                                                        <a class="dropdown-item" target="_blank" href="{{ route('course.player', ['slug' => $row->slug]) }}">{{ get_phrase('Go To Course Playing Page') }}</a>
                                                                    </li>

                                                                @endif --}}
                                                                @if (has_permission('admin.course.edit'))

                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('admin.course.edit', [$row->id, 'tab' => 'basic']) }}">{{ get_phrase('Edit Course') }}</a>
                                                                    </li>
                                                                @endif
                                                                {{-- @if (has_permission('admin.course.duplicate'))

                                                                    <li>
                                                                        <a class="dropdown-item" onclick="confirmModal('{{ route('admin.course.duplicate', $row->id) }}')" href="javascript:void(0)">{{ get_phrase('Duplicate Course') }}</a>
                                                                    </li>
                                                                @endif --}}
                                                                @if (has_permission('admin.course.status'))

                                                                    @if ($row->status == 'active')
                                                                        <li>
                                                                            <a class="dropdown-item" onclick="confirmModal('{{ route('admin.course.status', ['type' => 'inactive', 'id' => $row->id]) }}')" href="#">{{ get_phrase('Make As Inactive') }}</a>
                                                                        </li>
                                                                    @elseif($row->status == 'pending')
                                                                        <li>
                                                                            <a class="dropdown-item" onclick="ajaxModal('{{ route('view', ['path' => 'admin.course.course_approval', 'course_id' => $row->id]) }}', '{{ get_phrase('Write a congratulatory message') }}')" href="#">{{ get_phrase('Make As Active') }}</a>
                                                                        </li>
                                                                    @else
                                                                        <li>
                                                                            <a class="dropdown-item" onclick="confirmModal('{{ route('admin.course.status', ['type' => 'active', 'id' => $row->id]) }}')" href="#">{{ get_phrase('Make As Active') }}</a>
                                                                        </li>
                                                                    @endif

                                                                @endif
                                                                @if (has_permission('admin.course.delete'))

                                                                    <li>
                                                                        <a class="dropdown-item" onclick="confirmModal('{{ route('admin.course.delete', $row->id) }}')" href="javascript:void(0)">{{ get_phrase('Delete Course') }}</a>
                                                                    </li>

                                                                @endif
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="$('.dropdown-menu').removeClass('show'); ajaxModal('{{ route('admin.course.student_views', $row->id) }}', '{{ get_phrase('مشاهدات الطلبة') }}', 'modal-xl');">
                                                                        <i class="fi-rr-eye me-2"></i>{{ get_phrase('مشاهدات الطلبة') }}
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                    <p class="admin-tInfo">
                                        {{ get_phrase('Showing') . ' ' . count($courses) . ' ' . get_phrase('of') . ' ' . $courses->total() . ' ' . get_phrase('data') }}
                                    </p>
                                    {{ $courses->links() }}
                                </div>
                            @else
                                @include('admin.no_data')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Admin area -->
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.home-sidebar-toggle').forEach(function (el) {
                el.addEventListener('change', function () {
                    var courseId = el.getAttribute('data-course-id');
                    var previous = !el.checked;

                    fetch("{{ url('admin/course/home-sidebar') }}/" + courseId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(function (res) {
                        return res.json().then(function (data) {
                            if (!res.ok) {
                                throw new Error(data.error || 'Error');
                            }
                            return data;
                        });
                    }).then(function (data) {
                        if (typeof success === 'function') {
                            success(data.success);
                        } else if (window.toastr) {
                            toastr.success(data.success);
                        }
                    }).catch(function () {
                        el.checked = previous;
                        if (typeof error === 'function') {
                            error('{{ get_phrase('حدث خطأ أثناء التحديث') }}');
                        } else {
                            alert('{{ get_phrase('حدث خطأ أثناء التحديث') }}');
                        }
                    });
                });
            });
        });
    </script>
@endpush
