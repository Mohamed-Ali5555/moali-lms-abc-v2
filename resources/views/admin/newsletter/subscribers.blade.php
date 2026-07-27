@extends('layouts.admin')
@push('title', get_phrase('Subscriber'))

@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-users"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Subscribers') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage newsletter subscribers') }}</p>
                </div>
            </div>
        </div>


    <div class="row">
        <div class="col-xl-12">
            <div class="ol-card p-4">
                <div class="ol-card-body">

                    <div class="row print-d-none mb-3 mt-3 row-gap-3">

                        @if (has_permission('admin.subscribed_user.export'))
                            <div class="col-md-6 pt-2 pt-md-0">
                                <div class="custom-dropdown">
                                    <button class="dropdown-header btn ol-btn-light">
                                        {{ get_phrase('Export') }}
                                        <i class="fi-rr-file-export ms-2"></i>
                                    </button>
                                    <ul class="dropdown-list">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'newsletter-subscribers')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                        @if (has_permission('admin.subscribed_user.search'))
                            <div class="col-md-6">
                                <form action="{{ route('admin.subscribed_user') }}" method="get">
                                    <div class="row row-gap-3">
                                        <div class="col-md-9">
                                            <div class="search-input">
                                                <input type="text" name="search" value="{{ request('search') }}"
                                                    placeholder="{{ get_phrase('Search Email') }}" class="ol-form-control form-control" />

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

                    @if (count($subscribers) > 0)
                        <div class="table-responsive">
                            <table id="basic-datatable" class="table eTable print-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ get_phrase('Email') }}</th>
                                        <th>{{ get_phrase('User status') }}</th>
                                        <th class="print-d-none">{{ get_phrase('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscribers as $key => $subscriber)
                                        @php
                                            $user_details = App\Models\User::where('email', $subscriber->email)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>

                                            <td>{{ $subscriber->email }}</td>
                                            <td>
                                                @if ($user_details)
                                                    <span class="badge bg-success">{{ get_phrase('Registered User') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ get_phrase('Not Registered') }}</span>
                                                @endif
                                            </td>

                                            <td class="print-d-none">
                                                @if (has_permission('admin.subscribed_user.delete'))
                                                    <div class="adminTable-action">
                                                        <button type="button" class="btn ol-btn-light ol-icon-btn" data-bs-toggle="tooltip" title="{{ get_phrase('Delete') }}"
                                                            onclick="confirmModal('{{ route('admin.subscribed_user.delete', $subscriber->id) }}')">
                                                            <i class="fi-rr-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @include('admin.no_data')
                    @endif
                </div>
                @if (count($subscribers) > 0)
                    <div
                        class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                        <p class="admin-tInfo">
                            {{ get_phrase('Showing') . ' ' . count($subscribers) . ' ' . get_phrase('of') . ' ' . $subscribers->total() . ' ' . get_phrase('data') }}
                        </p>
                        {{ $subscribers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection
@push('js')@endpush
