@extends('layouts.admin')
@push('title', get_phrase('Exams List'))
@section('content')
    <style>
        .bq-qcount {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 6px 12px 6px 10px;
            border-radius: 999px;
            border: 1.5px solid #c7d2fe;
            background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 100%);
            color: #3730a3;
            text-decoration: none !important;
            font-weight: 700;
            transition: .18s ease;
            white-space: nowrap;
        }

        .bq-qcount:hover {
            border-color: #818cf8;
            background: linear-gradient(180deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #312e81;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(79, 70, 229, 0.16);
        }

        .bq-qcount__icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #4f46e5;
            font-size: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .bq-qcount__num {
            font-size: 15px;
            font-weight: 800;
            line-height: 1;
            min-width: 1.2em;
            text-align: center;
        }

        .bq-qcount__label {
            font-size: 12px;
            font-weight: 700;
            opacity: .85;
        }

        .bq-qcount.is-empty {
            border-color: #e2e8f0;
            background: #f8fafc;
            color: #64748b;
        }

        .bq-qcount.is-empty .bq-qcount__icon {
            color: #94a3b8;
        }

        .bq-view {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 6px 14px;
            border-radius: 12px;
            border: 1.5px solid #86efac;
            background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%);
            color: #166534 !important;
            text-decoration: none !important;
            font-size: 13px;
            font-weight: 700;
            transition: .18s ease;
            white-space: nowrap;
        }

        .bq-view i {
            font-size: 16px;
            color: #16a34a;
        }

        .bq-view:hover {
            border-color: #4ade80;
            background: linear-gradient(180deg, #dcfce7 0%, #bbf7d0 100%);
            color: #14532d !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(22, 163, 74, 0.16);
        }
    </style>

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
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage bank question quizzes') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                <a onclick="ajaxModal('{{ route('modal', ['bankquestions::quiz.create']) }}', '{{ get_phrase('Add New Quiz') }}', 'modal-lg')" href="#"
                    class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('Add New Quiz') }}</span>
                </a>
            </div>
        </div>

    <div class="row">
        <div class="col-12">
            <div class="ol-card">
                <div class="ol-card-body p-3 mb-5">
                    <div class="row mt-3 mb-4">
                        <div class="col-md-6 d-flex align-items-center gap-3">
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
                                        <form id="filter-dropdown" action="{{ route('admin.bank.quizs.index') }}" method="get">
                                            <div class="filter-option d-flex flex-column gap-3">
                                                <div>
                                                    <label for="eDataList" class="form-label ol-form-label">{{ get_phrase('Category') }}</label>
                                                    <select class="form-control ol-form-control ol-select2" data-toggle="select2" name="category" data-placeholder="Type to search...">
                                                        <option value="all">{{ get_phrase('All') }}</option>

                                                        @foreach (App\Models\Category::where('parent_id', 0)->orderBy('title', 'desc')->get() as $category)
                                                            <option value="main_{{ $category->id }}"@if (isset($parent_cat) && $parent_cat == $category->id) selected @endif>
                                                                {{ $category->title }}</option>

                                                            @foreach ($category->bank_category as $sub_category)
                                                                <option value="sub_{{ $sub_category->id }}"@if (isset($child_cat) && $child_cat == $sub_category->id) selected @endif>
                                                                    --{{ $sub_category->title }}</option>
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                            <div class="filter-button d-flex justify-content-end align-items-center mt-3">
                                                <button type="submit" class="ol-btn-primary">{{ get_phrase('Apply') }}</button>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            @if (isset($_GET) && count($_GET) > 0)
                                <a href="{{ route('admin.bank.quizs.index') }}" class="me-2" data-bs-toggle="tooltip" title="{{ get_phrase('Clear') }}"><i class="fi-rr-cross-circle"></i></a>
                            @endif
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <form action="{{ route('admin.bank.quizs.index') }}" method="get">
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
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @if ($quizs->count() > 0)
                                <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                    <p class="admin-tInfo">
                                        {{ get_phrase('Showing') . ' ' . count($quizs) . ' ' . get_phrase('of') . ' ' . $quizs->total() . ' ' . get_phrase('data') }}
                                    </p>
                                </div>
                                <div class="table-responsive overflow-auto course_list overflow-auto" id="course_list">
                                    <table class="table eTable eTable-2 print-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ get_phrase('Title') }}</th>
                                                <th scope="col">{{ get_phrase('Duration') }}</th>
                                                <th scope="col">{{ get_phrase('Total Mark') }}</th>
                                                <th scope="col">{{ get_phrase('retake') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('الأسئلة') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('عرض') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('Options') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($quizs as $key => $row)
                                                @php $qCount = $row->questions->count(); @endphp
                                                <tr>
                                                    <th scope="row">
                                                        <p class="row-number">{{ ++$key }}</p>
                                                    </th>
                                                    <td>
                                                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                            <div class="dAdmin_profile_name">
                                                                <h4 class="title fs-14px">
                                                                    {{ ucfirst($row->title) }}
                                                                </h4>
                                                                <p class="sub-title2 text-12px">
                                                                    {{ get_phrase('Category') }}:
                                                                    {{ $row->category->category->title ?? get_phrase('not found!') }}
                                                                </p>
                                                                <p class="sub-title2 text-12px">
                                                                    {{ get_phrase('Sub Category') }}:
                                                                    {{ $row->category->title ?? get_phrase('not found!') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <p class="sub-title2 text-12px">{{ $row->duration }}</p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <h4 class="title fs-12px">
                                                                {{ get_phrase('Total Mark') }}:
                                                                {{ ucfirst($row->total_mark) }}
                                                            </h4>
                                                            <h4 class="title fs-12px">
                                                                {{ get_phrase('Pass Mark') }}:
                                                                {{ ucfirst($row->pass_mark) }}
                                                            </h4>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <p class="sub-title2 text-12px">{{ $row->retake }}</p>
                                                        </div>
                                                    </td>

                                                    <td class="print-d-none">
                                                        <a href="#"
                                                            class="bq-qcount {{ $qCount === 0 ? 'is-empty' : '' }}"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ get_phrase('إدارة أسئلة الاختبار') }}"
                                                            onclick="ajaxModal('{{ route('modal', ['bankquestions::questions.index', 'id' => $row->id]) }}', '{{ get_phrase('الأسئلة') }}', 'modal-xl')">
                                                            <span class="bq-qcount__icon"><i class="fi-rr-list-check"></i></span>
                                                            <span class="bq-qcount__num">{{ $qCount }}</span>
                                                            <span class="bq-qcount__label">{{ get_phrase('سؤال') }}</span>
                                                        </a>
                                                    </td>

                                                    <td class="print-d-none">
                                                        <a href="{{ route('admin.bank.quizs.show', $row->id) }}"
                                                            class="bq-view"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ get_phrase('عرض الأسئلة') }}">
                                                            <i class="fi-rr-eye"></i>
                                                            <span>{{ get_phrase('عرض') }}</span>
                                                        </a>
                                                    </td>

                                                    <td>
                                                        <div class="dropdown ol-icon-dropdown">
                                                            <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <span class="fi-rr-menu-dots-vertical"></span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="ajaxModal('{{ route('modal', ['bankquestions::quiz.edit', 'id' => $row->id]) }}', '{{ get_phrase('Edit Quiz') }}', 'modal-lg')">{{ get_phrase('Edit') }}</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="confirmModal('{{ route('admin.bank.quizs.destroy', $row->id) }}')">{{ get_phrase('Delete') }}</a></li>
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
                                        {{ get_phrase('Showing') . ' ' . count($quizs) . ' ' . get_phrase('of') . ' ' . $quizs->total() . ' ' . get_phrase('data') }}
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
