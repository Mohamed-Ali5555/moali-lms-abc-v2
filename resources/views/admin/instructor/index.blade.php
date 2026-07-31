@extends('layouts.admin')

@push('title', get_phrase('Instructor'))

@push('meta')
@endpush

@push('css')
@endpush

@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-chalkboard-user"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('قائمة المدرسين') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('عرض وإدارة المدرسين — يمكنهم إضافة الكورسات من لوحة المدرس') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                <a href="{{ route('admin.instructor.create') }}" class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('إضافة مدرس جديد') }}</span>
                </a>
            </div>
        </div>

    <div class="ol-card p-4">
        <div class="ol-card-body">

            <div class="row print-d-none mb-3 mt-3 row-gap-3">
                <div class="col-md-6 pt-2 pt-md-0">
                    <div class="custom-dropdown">
                        <button class="dropdown-header btn ol-btn-light">
                            {{ get_phrase('تصدير') }}
                            <i class="fi-rr-file-export ms-2"></i>
                        </button>
                        <ul class="dropdown-list">
                            <li>
                                <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'instructor-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('طباعة') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <form class="form-inline" action="{{ route('admin.instructor.index') }}" method="get">
                        <div class="row row-gap-3">
                            <div class="col-md-9">
                                <input type="text" class="form-control ol-form-control" name="search" value="{{ request('search') }}" placeholder="{{ get_phrase('بحث بالاسم، البريد، الجوال، الإقامة، المنطقة') }}" />
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn ol-btn-primary w-100" id="submit-button"> {{ get_phrase('بحث') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Table -->
                    @if (count($instructors) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('عرض') . ' ' . count($instructors) . ' ' . get_phrase('من') . ' ' . $instructors->total() . ' ' . get_phrase('سجل') }}
                            </p>
                        </div>
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('الاسم') }}</th>
                                        <th scope="col">{{ get_phrase('الجوال') }}</th>
                                        <th scope="col">{{ get_phrase('رقم الإقامة') }}</th>
                                        <th scope="col">{{ get_phrase('المنطقة') }}</th>
                                        <th scope="col">{{ get_phrase('عدد الكورسات') }}</th>
                                        <th class="print-d-none" scope="col">{{ get_phrase('الخيارات') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($instructors as $key => $row)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ ++$key }}</p>
                                            </th>
                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                    <div class="dAdmin_profile_img">
                                                        <img class="img-fluid rounded-circle object-fit-cover" width="45" height="45" src="{{ get_image($row->photo) }}" />
                                                    </div>
                                                    <div class="ms-1">
                                                        <h4 class="title fs-14px">{{ $row->name }}</h4>
                                                        <p class="sub-title2 text-12px">{{ $row->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->phone ?: '—' }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-120px">
                                                    <p>{{ $row->national_id ?: '—' }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-120px">
                                                    <p>{{ $row->goverment ?: '—' }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                {{ App\Models\Course::where('user_id', $row->id)->count() }}
                                                {{ get_phrase('كورس') }}
                                            </td>
                                            <td class="print-d-none">
                                                <div class="dropdown ol-icon-dropdown ol-icon-dropdown-transparent">
                                                    <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="fi-rr-menu-dots-vertical"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.courses', ['instructor' => $row->id]) }}">{{ get_phrase('عرض الكورسات') }}</a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.instructor.edit', $row->id) }}">{{ get_phrase('تعديل') }}</a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item" onclick="confirmModal('{{ route('admin.instructor.delete', $row->id) }}')"
                                                                href="javascript:void(0)">{{ get_phrase('حذف') }}</a>
                                                        </li>
                                                    </ul>
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
                    @if (count($instructors) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('عرض') . ' ' . count($instructors) . ' ' . get_phrase('من') . ' ' . $instructors->total() . ' ' . get_phrase('سجل') }}
                            </p>
                            {{ $instructors->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection


@push('js')
@endpush
