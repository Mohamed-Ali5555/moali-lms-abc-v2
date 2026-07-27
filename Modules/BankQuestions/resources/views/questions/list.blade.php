@extends('layouts.admin')
@push('title', get_phrase('Questions'))
<style>
      .dAdmin_profile {

      font-size: 18px;

      resize: none;
      direction: rtl;
    }

    .limited-text {
        display: inline-block;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }

</style>
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-interrogation"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Exams List') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage bank questions') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.bank.question.create'))
                    <a onclick="ajaxModal('{{ route('modal', ['bankquestions::questions.create-question']) }}', '{{ get_phrase('Add New Question') }}')" href="#"
                        class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add New Question') }}</span>
                    </a>
                @endif
            </div>
        </div>


    <!-- Start Admin area -->
    <div class="row">
        <div class="col-12">
            <div class="ol-card">
                <div class="ol-card-body p-3 mb-5">
                    <div class="row mt-3 mb-4">
                        <div class="col-md-6 d-flex align-items-center gap-3">
                            @if (has_permission('admin.bank.question.export'))
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
                        @if (has_permission('admin.bank.question.filter'))

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
                                        <form id="filter-dropdown" action="{{ route('admin.bank.question.index') }}" method="get">
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
                        @endif
                            @if (isset($_GET) && count($_GET) > 0)
                                <a href="{{ route('admin.bank.question.index') }}" class="me-2" data-bs-toggle="tooltip" title="{{ get_phrase('Clear') }}"><i class="fi-rr-cross-circle"></i></a>
                            @endif
                        </div>
                        @if (has_permission('admin.bank.question.search'))
                            <div class="col-md-6 mt-3 mt-md-0">
                                <form action="{{ route('admin.bank.question.index') }}" method="get">
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
                            @if ($questions->count() > 0)
                                <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                                    <p class="admin-tInfo">
                                        {{ get_phrase('Showing') . ' ' . count($questions) . ' ' . get_phrase('of') . ' ' . $questions->total() . ' ' . get_phrase('data') }}
                                    </p>
                                </div>
                                <div class="table-responsive overflow-auto course_list overflow-auto" id="course_list">
                                    <table class="table eTable eTable-2 print-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ get_phrase('Title') }}</th>
                                                <th scope="col">{{ get_phrase('Category') }}</th>
                                                <th scope="col">{{ get_phrase('Sub Category') }}</th>
                                                <th scope="col">{{ get_phrase('Quiz') }}</th>
                                                <th scope="col" class="print-d-none">{{ get_phrase('action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($questions as $key => $row)
                                                <tr>
                                                    <th scope="row">
                                                        <p class="row-number">{{ ++$key }}</p>
                                                    </th>
                                                <td style="width: 260Px;">
                                                    <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                        <div class="dAdmin_profile_name">
                                                            <h4 class="title fs-14px text-wrap limited-text" data-full-text="{{ strip_tags($row->title) }}">
                                                                {!! Str::limit(strip_tags($row->title), 100, '...') !!}
                                                            </h4>
                                                            @if(strlen(strip_tags($row->title)) > 100)
                                                                <a href="#" class="read-more text-primary" style="font-size: 13px;">قراءة المزيد</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <p class="sub-title2 text-12px">{{ $row->category->category->title ?? '--' }}</p>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <p class="sub-title2 text-12px">{{ $row->category->title ?? '--' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="sub-title2 text-12px">
                                                            <p class="sub-title2 text-12px">
                                                                @foreach ($row->quizs ?? [] as $quiz)
                                                                   {{ $quiz->title }}
                                                                   <br>
                                                                @endforeach
                                                            </p>
                                                        </div>
                                                    </td>

                                                    <td class="print-d-none">
                                                        @if (has_permission('admin.bank.question.edit'))

                                                            <a href="#" data-bs-toggle="tooltip"
                                                                onclick="ajaxModal('{{ route('modal', ['bankquestions::questions.edit-question', 'id' => $row->id]) }}', '{{ get_phrase('Edit Question') }}', 'modal-lg')"
                                                                class="edit-delete" aria-label="Edit question" data-bs-original-title="Edit quiz">
                                                                <span class="fi-rr-pencil"></span>
                                                            </a>

                                                        @endif

                                                        @if (has_permission('admin.bank.question.delete'))
                                                            <a class="ml-4" href="#" data-bs-toggle="tooltip"
                                                                onclick="confirmModal('{{ route('admin.bank.question.deleteQuestions', $row->id) }}'); event.stopPropagation();"
                                                                class="edit-delete" aria-label="Delete Question" data-bs-original-title="Delete Question">
                                                                <span class="fi-rr-trash"></span>
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
                                        {{ get_phrase('Showing') . ' ' . count($questions) . ' ' . get_phrase('of') . ' ' . $questions->total() . ' ' . get_phrase('data') }}
                                    </p>
                                    {{ $questions->links() }}
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.read-more').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const titleElement = this.previousElementSibling;
            titleElement.textContent = titleElement.getAttribute('data-full-text');
            this.remove();
        });
    });
});
</script>
