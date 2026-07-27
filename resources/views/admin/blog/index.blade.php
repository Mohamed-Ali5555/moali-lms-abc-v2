@extends('layouts.admin')
@push('title', get_phrase('Blog'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-blog-text"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Blog') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Create and manage blog posts') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.blog.create'))
                    <a href="{{ route('admin.blog.create') }}" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new blog') }}</span>
                    </a>
                @endif
            </div>
        </div>

    <!-- Start Admin area -->
    <div class="row">
        <div class="col-12">
            <div class="ol-card">
                <div class="ol-card-body p-3">

                    <div class="row print-d-none mb-3 mt-3 row-gap-3">
                        @if (has_permission('admin.blog.export'))

                            <div class="col-md-6  pt-2 pt-md-0">
                                <div class="custom-dropdown">
                                    <button class="dropdown-header btn ol-btn-light">
                                        {{ get_phrase('Export') }}
                                        <i class="fi-rr-file-export ms-2"></i>
                                    </button>
                                    <ul class="dropdown-list">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'blog-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        @endif
                        @if (has_permission('admin.blog.search'))

                            <div class="col-md-6">
                                <form action="{{ route('admin.blogs') }}" method="get">
                                    <div class="row row-gap-3">
                                        <div class="col-md-9">
                                            <div class="search-input">
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

                    <!-- Table -->
                    @if (count($blogs) > 0)
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('Creator') }}</th>
                                        <th scope="col">{{ get_phrase('Title') }}</th>
                                        <th scope="col">{{ get_phrase('Category') }}</th>
                                        <th scope="col" class="print-d-none">{{ get_phrase('Status') }}</th>
                                        <th class="print-d-none" scope="col">{{ get_phrase('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blogs as $key => $blog)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ $key + 1 }}</p>
                                            </th>

                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                    <div class="dAdmin_profile_img">
                                                        <img class="img-fluid rounded-circle image-45" width="40" height="40" src="{{ get_image($blog->user->photo) }}" />
                                                    </div>
                                                    <div class="ms-1 mt-1">
                                                        <h4 class="title fs-14px">{{ $blog->user->name }}</h4>
                                                        <p class="sub-title2 text-12px">{{ $blog->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p><a href="{{ route('blog.details', ['slug' => slugify($blog->title)]) }}">{{ $blog->title }}</a></p>
                                                    <small class="sub-title2 text-11px">{{ date('D, d-M-Y', strtotime($blog->created_at)) }}</small>
                                                </div>
                                            </td>

                                            <td>
                                                @php
                                                    $category = DB::table('blog_categories')
                                                        ->where('id', $blog->category_id)
                                                        ->first();
                                                @endphp
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $category->title }}</p>
                                                </div>
                                            </td>

                                            <td class="print-d-none">
                                                <div class="dAdmin_info_name min-w-150px">
                                                    @if (has_permission('admin.blog.status'))
                                                        <div class="eSwitches">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input form-switch-medium" onchange="actionTo('{{ route('admin.blog.status', ['id' => $blog->id]) }}');" type="checkbox" @if ($blog->status) checked @endif>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="print-d-none">
                                                <div class="dropdown ol-icon-dropdown ol-icon-dropdown-transparent">
                                                    <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="fi-rr-menu-dots-vertical"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if (has_permission('admin.blog.edit'))
                                                           <li><a class="dropdown-item" href="{{ route('admin.blog.edit', ['id' => $blog->id]) }}">{{ get_phrase('Edit') }}</a></li>
                                                        @endif
                                                        @if (has_permission('admin.blog.delete'))
                                                            <li><a class="dropdown-item" href="#" onclick="confirmModal('{{ route('admin.blog.delete', $blog->id) }}')">{{ get_phrase('Delete') }}</a></li>
                                                        @endif
                                                        @if (has_permission('admin.blog.view_front'))
                                                            <li><a class="dropdown-item" href="{{ route('blog.details', slugify($blog->title)) }}">{{ get_phrase('View on frontend') }}</a></li>
                                                        @endif
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
                    @if (count($blogs) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($blogs) . ' ' . get_phrase('of') . ' ' . $blogs->total() . ' ' . get_phrase('data') }}
                            </p>
                            {{ $blogs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Admin area -->
    </div>
@endsection
@push('js')@endpush
