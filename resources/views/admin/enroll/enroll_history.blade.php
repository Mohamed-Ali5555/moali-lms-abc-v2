@extends('layouts.admin')
@push('title', get_phrase('Enroll History'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    @php
        $activeDateFilter = $date_filter ?? 'any_time';
        $hasCourseFilter = isset($course_id) && $course_id;
        $hasSearchFilter = !empty($search);
        $hasDateFilter = $activeDateFilter !== 'any_time';
        $hasAnyFilter = $hasCourseFilter || $hasSearchFilter || $hasDateFilter;
        $dateRangeValue = $hasDateFilter
            ? (date('m/d/Y', $start_date) . ' - ' . date('m/d/Y', $end_date))
            : '';
        $totalEnrollments = $enroll_history->total();
    @endphp

    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-user-add"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Enroll History') }}
                        <span class="admin-toolbar__count">{{ number_format($totalEnrollments) }}</span>
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Track and manage student enrollments') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (isset($course_for_extend) && $course_for_extend && has_permission('admin.enroll.history.edit'))
                    <button type="button" class="admin-btn admin-btn--ghost"
                        onclick="ajaxModal('{{ route('modal', ['admin.enroll.extend_course_period', 'course_id' => $course_id]) }}', '{{ get_phrase('Extend period for all subscribers') }}', 'modal-md')">
                        <span class="fi-rr-calendar"></span>
                        <span>{{ get_phrase('Extend period for all in this course') }}</span>
                    </button>
                @endif
                @if ($hasCourseFilter && has_permission('admin.enroll.history.delete') && $totalEnrollments > 0)
                    <button type="button" class="admin-btn admin-btn--ghost"
                        onclick="confirmModal('{{ route('admin.enroll.history.unenroll_all', $course_id) }}')">
                        <span class="fi-rr-user-remove"></span>
                        <span>{{ get_phrase('Unenroll all students from this course') }}</span>
                    </button>
                @endif
                @if (has_permission('admin.student.enroll'))
                    <a href="{{ route('admin.student.enroll') }}" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new enrollment') }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if (has_permission('admin.student.search'))
            <div class="eh-filters print-d-none">
                <div class="eh-filters__head">
                    <div class="eh-filters__title">
                        <i class="fi-rr-filter"></i>
                        <span>{{ get_phrase('Filters') }}</span>
                    </div>
                    <div class="eh-filters__head-actions">
                        @if (has_permission('admin.student.export') && count($enroll_history) > 0)
                            <div class="custom-dropdown">
                                <button type="button" class="dropdown-header admin-btn admin-btn--ghost eh-export-btn">
                                    <span class="fi-rr-file-export"></span>
                                    <span>{{ get_phrase('Export') }}</span>
                                </button>
                                <ul class="dropdown-list">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'enroll-history')">
                                            <i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="window.print();">
                                            <i class="fi-rr-print"></i> {{ get_phrase('Print') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        @if ($hasAnyFilter)
                            <a href="{{ route('admin.enroll.history') }}" class="eh-reset-link">
                                <i class="fi-rr-cross-small"></i>
                                {{ get_phrase('Reset filters') }}
                            </a>
                        @endif
                    </div>
                </div>

                <form action="{{ route('admin.enroll.history') }}" method="get" class="eh-filters__form" id="enrollHistoryFilters">
                    <div class="eh-filters__grid">
                        @if (isset($courses_for_filter) && $courses_for_filter->isNotEmpty())
                            <div class="eh-field eh-field--course">
                                <label class="eh-label" for="eh-course">{{ get_phrase('Course') }}</label>
                                <select class="form-select ol-form-control" name="id" id="eh-course">
                                    <option value="">{{ get_phrase('All courses') }}</option>
                                    @foreach ($courses_for_filter as $c)
                                        <option value="{{ $c->id }}" {{ $hasCourseFilter && $course_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="eh-field eh-field--search">
                            <label class="eh-label" for="eh-search">{{ get_phrase('Student') }}</label>
                            <div class="eh-search">
                                <i class="fi-rr-search"></i>
                                <input type="text" class="form-control ol-form-control" name="search" id="eh-search"
                                    value="{{ old('search', $search ?? '') }}"
                                    placeholder="{{ get_phrase('Name, phone or email...') }}" />
                            </div>
                        </div>

                        <div class="eh-field eh-field--type">
                            <label class="eh-label" for="date-filter-type">{{ get_phrase('Period') }}</label>
                            <select class="form-select ol-form-control" name="date_filter" id="date-filter-type" onchange="toggleEnrollDateRange()">
                                <option value="range" {{ $activeDateFilter !== 'any_time' ? 'selected' : '' }}>{{ get_phrase('Date range') }}</option>
                                <option value="any_time" {{ $activeDateFilter === 'any_time' ? 'selected' : '' }}>{{ get_phrase('Any time') }}</option>
                            </select>
                        </div>

                        <div class="eh-field eh-field--range" id="eh-date-range-wrap">
                            <label class="eh-label" for="enroll-date-range">{{ get_phrase('Date range') }}</label>
                            <div class="eh-date">
                                <i class="fi-rr-calendar"></i>
                                <input type="text" class="form-control ol-form-control eh-daterange" name="eDateRange"
                                    id="enroll-date-range"
                                    value="{{ $dateRangeValue }}"
                                    placeholder="{{ get_phrase('Select date range') }}"
                                    autocomplete="off" />
                            </div>
                        </div>

                        <div class="eh-field eh-field--actions">
                            <label class="eh-label eh-label--ghost">&nbsp;</label>
                            <button type="submit" class="admin-btn admin-btn--primary eh-apply-btn" id="submit-button" onclick="update_date_range();">
                                <i class="fi-rr-filter"></i>
                                <span>{{ get_phrase('Apply') }}</span>
                            </button>
                        </div>
                    </div>
                </form>

                @if ($hasAnyFilter)
                    <div class="eh-chips">
                        <span class="eh-chips__label">{{ get_phrase('Active') }}:</span>
                        @if ($hasCourseFilter)
                            @php
                                $activeCourseTitle = optional($courses_for_filter->firstWhere('id', $course_id))->title
                                    ?? get_phrase('Course');
                            @endphp
                            <a class="eh-chip"
                                href="{{ route('admin.enroll.history', request()->except('id')) }}">
                                <i class="fi-rr-e-learning"></i>
                                <span>{{ $activeCourseTitle }}</span>
                                <i class="fi-rr-cross-small"></i>
                            </a>
                        @endif
                        @if ($hasSearchFilter)
                            <a class="eh-chip"
                                href="{{ route('admin.enroll.history', request()->except('search')) }}">
                                <i class="fi-rr-search"></i>
                                <span>{{ $search }}</span>
                                <i class="fi-rr-cross-small"></i>
                            </a>
                        @endif
                        @if ($hasDateFilter)
                            <a class="eh-chip"
                                href="{{ route('admin.enroll.history', array_merge(request()->except(['eDateRange']), ['date_filter' => 'any_time'])) }}">
                                <i class="fi-rr-calendar"></i>
                                <span>{{ $dateRangeValue }}</span>
                                <i class="fi-rr-cross-small"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div class="ol-card eh-table-card">
            <div class="ol-card-body p-3">
                <div class="row">
                    <div class="col-md-12">
                        @if (count($enroll_history) > 0)
                            <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                <p class="admin-tInfo">
                                    {{ get_phrase('Showing') . ' ' . count($enroll_history) . ' ' . get_phrase('of') . ' ' . $totalEnrollments . ' ' . get_phrase('data') }}
                                </p>
                            </div>
                            <div class="table-responsive enroll_history overflow-auto" id="enroll_history">
                                <table class="table eTable eTable-2 print-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ get_phrase('Name') }}</th>
                                            <th scope="col">{{ get_phrase('Phone') }}</th>
                                            <th scope="col">{{ get_phrase('Enrolled Course') }}</th>
                                            <th scope="col">{{ get_phrase('Enrolled Date') }}</th>
                                            <th scope="col">{{ get_phrase('Expiry Date') }}</th>
                                            <th class="print-d-none" scope="col">{{ get_phrase('Option') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($enroll_history as $key => $row)
                                            <tr>
                                                <th scope="row">
                                                    <p class="row-number">{{ ++$key }}</p>
                                                </th>
                                                <td>
                                                    <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                        <div class="dAdmin_profile_img">
                                                            <img class="img-fluid rounded-circle image-45" width="45" height="45" src="{{ get_image($row->photo) }}" />
                                                        </div>
                                                        <div class="ms-1">
                                                            <h4 class="title fs-14px">{{ get_user_info($row->user_id)->name }}</h4>
                                                            <p class="sub-title2 text-12px">{{ get_user_info($row->user_id)->email }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="dAdmin_info_name min-w-250px">
                                                        <h4 class="title fs-14px">{{ get_user_info($row->user_id)->phone }}</h4>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="dAdmin_info_name min-w-250px">
                                                        <p><a href="{{ route('admin.course.edit', $row->course_id) }}" target="_blank">{{ get_course_info($row->course_id)->title }}</a></p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="dAdmin_info_name min-w-250px">
                                                        <p>{{ date('F d Y', $row->entry_date) }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="dAdmin_info_name min-w-250px">
                                                        @if ($row->expiry_date)
                                                            @if ($row->expiry_date >= time())
                                                                <p><span class="badge bg-success text-white">{{ date('d M Y', $row->expiry_date) . ' ' . date('h:i A', $row->expiry_date) }}</span></p>
                                                            @else
                                                                <p><span class="badge bg-danger text-white">{{ date('d M Y', $row->expiry_date) . ' ' . date('h:i A', $row->expiry_date) }}</span></p>
                                                            @endif
                                                        @else
                                                            <p><span class="badge bg-success text-white">{{ get_phrase('Lifetime access') }}</span></p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="print-d-none">
                                                    <div class="adminTable-action">
                                                        @if (has_permission('admin.enroll.history.edit'))
                                                            <button type="button" class="btn ol-btn-light ol-icon-btn" data-bs-toggle="tooltip" title="{{ get_phrase('Edit Expiry Date') }}"
                                                                onclick="ajaxModal('{{ route('modal', ['admin.enroll.edit_expiry_date', 'id' => $row->id]) }}', '{{ get_phrase('Edit Expiry Date') }}', 'modal-md')">
                                                                <i class="fi-rr-calendar"></i>
                                                            </button>
                                                        @endif
                                                        @if (has_permission('admin.enroll.history.delete'))
                                                            <button type="button" class="btn ol-btn-light ol-icon-btn" data-bs-toggle="tooltip" title="{{ get_phrase('Delete') }}"
                                                                onclick="confirmModal('{{ route('admin.enroll.history.delete', $row->id) }}')">
                                                                <i class="fi-rr-trash"></i>
                                                            </button>
                                                        @endif
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

                        @if (count($enroll_history) > 0)
                            <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                <p class="admin-tInfo">
                                    {{ get_phrase('Showing') . ' ' . count($enroll_history) . ' ' . get_phrase('of') . ' ' . $totalEnrollments . ' ' . get_phrase('data') }}
                                </p>
                                {{ $enroll_history->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script type="text/javascript">
    "use strict";

    $(function () {
        var $range = $('#enroll-date-range');
        if ($range.length && !$range.hasClass('inited')) {
            $range.daterangepicker({
                autoUpdateInput: false,
                locale: { cancelLabel: 'Clear' }
            });

            $range.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(
                    picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY')
                );
            });

            $range.on('cancel.daterangepicker', function () {
                $(this).val('');
            });

            @if ($hasDateFilter)
                $range.data('daterangepicker').setStartDate('{{ date('m/d/Y', $start_date) }}');
                $range.data('daterangepicker').setEndDate('{{ date('m/d/Y', $end_date) }}');
                $range.val(@json($dateRangeValue));
            @endif

            $range.addClass('inited');
        }

        toggleEnrollDateRange();
    });

    function toggleEnrollDateRange() {
        var isAnyTime = $('#date-filter-type').val() === 'any_time';
        $('#enroll-date-range').prop('disabled', isAnyTime);
        $('#eh-date-range-wrap').toggleClass('is-disabled', isAnyTime);
    }

    function Export() {
        const element = document.getElementById("enroll_history");
        var clonedElement = element.cloneNode(true);
        $(clonedElement).css("display", "block");

        var opt = {
            margin: 1,
            filename: 'enroll_history_{{ date('y-m-d') }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 }
        };

        html2pdf().set(opt).from(clonedElement).save();
        clonedElement.remove();
    }

    function printableDiv(printableAreaDivId) {
        var printContents = document.getElementById(printableAreaDivId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function update_date_range() {
        var x = $("#selectedValue").html();
        $("#date_range").val(x);
    }
</script>
@endpush
