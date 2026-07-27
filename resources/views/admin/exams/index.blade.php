@extends('layouts.admin')
@push('title', get_phrase('Exams List'))
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-clipboard-list-check"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Exams List') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage course exams and quiz status') }}</p>
                </div>
            </div>
        </div>

        <div class="row g-2 g-sm-3 mb-3 row-cols-1 row-cols-md-2">
            <div class="col">
                <a href="{{ route('admin.exams.list', ['status' => '1']) }}" class="d-block">
                    <div class="ol-card card-hover h-100">
                        <div class="ol-card-body px-3 py-12px">
                            <div class="d-flex align-items-center cg-12px">
                                <div>
                                    <p class="sub-title fs-14px fw-semibold mb-2">{{ \App\Models\Lesson::where(['lesson_type' => 'quiz', 'status' => '1', 'type' => '1'])->count() }}</p>
                                    <h6 class="title fs-14px mb-1">{{ get_phrase('Active exams') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.exams.list', ['status' => '0']) }}" class="d-block">
                    <div class="ol-card card-hover h-100">
                        <div class="ol-card-body px-3 py-12px">
                            <div class="d-flex align-items-center cg-12px">
                                <div>
                                    <p class="sub-title fs-14px fw-semibold mb-2">{{ \App\Models\Lesson::where(['lesson_type' => 'quiz', 'status' => '0', 'type' => '1'])->count() }}</p>
                                    <h6 class="title fs-14px mb-1">{{ get_phrase('Inactive exams') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="ol-card">
                    <div class="ol-card-body p-3 mb-5">
                        <div class="row mt-3 mb-4">
                            <div class="col-md-6 d-flex align-items-center gap-3">
                                @if (has_permission('admin.exams.export'))
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
                                @if (has_permission('admin.exams.filter'))
                                    <div class="custom-dropdown dropdown-filter">
                                        <button class="dropdown-header btn ol-btn-light">
                                            <i class="fi-rr-filter me-2"></i>
                                            {{ get_phrase('Filter') }}
                                            @if (isset($_GET) && count($_GET))
                                                <span class="text-12px">({{ count($_GET) }})</span>
                                            @endif
                                        </button>
                                        <ul class="dropdown-list w-250px">
                                            <li>
                                                <form id="filter-dropdown" action="{{ route('admin.exams.list') }}" method="get">
                                                    <div class="filter-option d-flex flex-column gap-3">
                                                        <div>
                                                            <label class="form-label ol-form-label">{{ get_phrase('Category') }}</label>
                                                            <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="category" data-placeholder="Type to search...">
                                                                <option value="all">{{ get_phrase('All') }}</option>
                                                                @foreach (App\Models\Category::where('parent_id', 0)->orderBy('title', 'desc')->get() as $category)
                                                                    <option value="{{ $category->slug }}" @if (isset($parent_cat) && $parent_cat == $category->slug) selected @endif>
                                                                        {{ $category->title }}
                                                                    </option>
                                                                    @foreach ($category->childs as $sub_category)
                                                                        <option value="{{ $sub_category->slug }}" @if (isset($child_cat) && $child_cat == $sub_category->slug) selected @endif>
                                                                            --{{ $sub_category->title }}
                                                                        </option>
                                                                    @endforeach
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="form-label ol-form-label">{{ get_phrase('Status') }}</label>
                                                            <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="status" data-placeholder="Type to search...">
                                                                <option value="all">{{ get_phrase('All') }}</option>
                                                                <option value="1" @if (isset($status) && $status == '1') selected @endif>{{ get_phrase('Active') }}</option>
                                                                <option value="0" @if (isset($status) && $status == '0') selected @endif>{{ get_phrase('inactive') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="form-label ol-form-label">{{ get_phrase('Instructor') }}</label>
                                                            <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="instructor" data-placeholder="Type to search...">
                                                                <option value="all">{{ get_phrase('All') }}</option>
                                                                @foreach (App\Models\Course::select('user_id')->distinct()->get() as $course)
                                                                    <option value="{{ $course->user_id }}" @if (isset($instructor) && $instructor == $course->user_id) selected @endif>
                                                                        {{ ucfirst(get_user_info($course->user_id)->name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="filter-button d-flex justify-content-end align-items-center mt-3">
                                                            <button type="submit" class="ol-btn-primary">{{ get_phrase('Apply') }}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                                @if (isset($_GET) && count($_GET) > 0)
                                    <a href="{{ route('admin.exams.list') }}" class="me-2" data-bs-toggle="tooltip" title="{{ get_phrase('Clear') }}"><i class="fi-rr-cross-circle"></i></a>
                                @endif
                            </div>
                            @if (has_permission('admin.exams.search'))
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <form action="{{ route('admin.exams.list') }}" method="get">
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
                                @if ($quizs->count() > 0)
                                    <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                        <p class="admin-tInfo">
                                            {{ get_phrase('Showing') . ' ' . $quizs->count() . ' ' . get_phrase('of') . ' ' . $quizs->total() . ' ' . get_phrase('data') }}
                                        </p>
                                    </div>

                                    <div class="table-responsive exams-table-wrap" id="course_list">
                                        <table class="table eTable eTable-2 print-table exams-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">{{ get_phrase('Title') }}</th>
                                                    <th scope="col">{{ get_phrase('Category') }}</th>
                                                    <th scope="col">{{ get_phrase('Duration') }}</th>
                                                    <th scope="col">{{ get_phrase('Marks') }}</th>
                                                    <th scope="col">{{ get_phrase('retake') }}</th>
                                                    <th scope="col" class="print-d-none">{{ get_phrase('النتائج') }}</th>
                                                    <th scope="col" class="print-d-none">{{ get_phrase('الحالة') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($quizs as $key => $row)
                                                    @php
                                                        $resultsCount = $row->deliverables->count();
                                                        $isActive = (int) $row->status === 1;
                                                    @endphp
                                                    <tr>
                                                        <th scope="row">
                                                            <span class="exams-table__index">{{ $quizs->firstItem() + $key }}</span>
                                                        </th>
                                                        <td>
                                                            <div class="exams-table__title">
                                                                <strong>{{ ucfirst($row->title) }}</strong>
                                                                <a href="{{ route('admin.courses', ['instructor' => $row->user_id]) }}" class="exams-table__course">
                                                                    <i class="fi-rr-book-alt"></i>
                                                                    {{ $row->course->title ?? 'N/A' }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="exams-table__chip">{{ $row->course->category->title ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="exams-table__meta">
                                                                <i class="fi-rr-clock"></i>
                                                                {{ $row->duration }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="exams-table__marks">
                                                                <span><em>{{ get_phrase('Total') }}</em> {{ $row->total_mark }}</span>
                                                                <span><em>{{ get_phrase('Pass') }}</em> {{ $row->pass_mark }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="exams-table__retake">{{ get_phrase($row->retake) }}</span>
                                                        </td>
                                                        <td class="print-d-none">
                                                            <a href="#"
                                                                class="exams-table__results"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ get_phrase('Result') }}"
                                                                onclick="ajaxModal('{{ route('modal', ['admin.quiz_result.index', 'id' => $row->id]) }}', '{{ get_phrase('Result') }}', 'modal-xl')">
                                                                <i class="fi-rr-chart-histogram"></i>
                                                                <strong>{{ $resultsCount }}</strong>
                                                                <small>{{ get_phrase('نتيجة') }}</small>
                                                            </a>
                                                        </td>
                                                        <td class="print-d-none">
                                                            @if (has_permission('admin.exams.activation'))
                                                                <a href="#"
                                                                    onclick="confirmModal('{{ route('admin.exams.activation', $row->id) }}')"
                                                                    class="exams-table__status {{ $isActive ? 'is-on' : 'is-off' }}"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ $isActive ? get_phrase('Active') : get_phrase('Inactive') }}">
                                                                    <span class="exams-table__status-icon">
                                                                        <i class="{{ $isActive ? 'fi-rr-eye' : 'fi-rr-eye-crossed' }}"></i>
                                                                    </span>
                                                                    <span class="exams-table__status-text">
                                                                        {{ $isActive ? get_phrase('مفعّل') : get_phrase('موقوف') }}
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                        <p class="admin-tInfo">
                                            {{ get_phrase('Showing') . ' ' . $quizs->count() . ' ' . get_phrase('of') . ' ' . $quizs->total() . ' ' . get_phrase('data') }}
                                        </p>
                                        {{ $quizs->links() }}
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
    </div>
@endsection
