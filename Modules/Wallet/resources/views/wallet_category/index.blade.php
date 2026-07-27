@extends('layouts.admin')
@push('title', get_phrase('wallet-categories'))
@push('meta')
@endpush
@push('css')
@endpush
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-apps"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('wallets List') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage wallet balances by category') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                {{-- <a href="{{ route('admin.wallet.create') }}" class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('Add new wallet by category') }}</span>
                </a> --}}
                @if (has_permission('admin.wallet_category.create'))
                    <a onclick="ajaxModal('{{ route('modal', ['wallet::wallet_category.create', 'parent_id' => 0]) }}', '{{ get_phrase('Add new balance by category') }}')" href="#" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new balance by category') }}</span>
                    </a>
                @endif
            </div>
        </div>
    <div class="ol-card p-4">
        <div class="ol-card-body">

            <div class="row print-d-none mb-3 mt-3 row-gap-3">
                @if (has_permission('admin.wallet_category.export'))

                    <div class="col-md-6  pt-2 pt-md-0">
                        <div class="custom-dropdown">
                            <button class="dropdown-header btn ol-btn-light">
                                {{ get_phrase('Export') }}
                                <i class="fi-rr-file-export ms-2"></i>
                            </button>
                            <ul class="dropdown-list">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="downloadPDF('.print-table', 'wallet-list')"><i class="fi-rr-file-pdf"></i> {{ get_phrase('PDF') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="window.print();"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                @endif

                @if (has_permission('admin.wallet_category.search'))

                    <div class="col-md-6">
                        <form class="form-inline" action="{{ route('admin.wallet_category.index') }}" method="get">
                            <div class="row row-gap-3">
                                <div class="col-md-9">
                                    <input type="text" class="form-control ol-form-control" name="search" value="{{ request('search') }}" placeholder="{{ get_phrase('Search category') }}" />
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn ol-btn-primary w-100" id="submit-button"> {{ get_phrase('Search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>

                @endif
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Table -->
                    @if (count($wallet_categories) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($categories) . ' ' . get_phrase('of') . ' ' .  ' ' . get_phrase('data') }}
                            </p>
                        </div>
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('balance') }}</th>
                                        <th scope="col">{{ get_phrase('category') }}</th>
                                        <th scope="col">{{ get_phrase('type') }}</th>
                                        <th scope="col">{{ get_phrase('note') }}</th>

                                        <th class="print-d-none" scope="col">{{ get_phrase('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wallet_categories as $key => $row)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ ++$key }}</p>
                                            </th>
                                            
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $row->balance }}</p>
                                                </div>
                                              
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{optional($row->category)->title }}</p>
                                                </div>
                                              
                                            </td>

                                            <td>
                                                <span class="badge @if($row->type == 'by_hand') bg-danger @elseif($row->type == 'fawry') bg-warning @else bg-success  @endif">
                                                    @if($row->type == 'by_hand') نقدي @elseif($row->type == 'fawry') فوري@else هديه  @endif
                                                </span>
                                            </td>

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{$row->note }}</p>
                                                </div>
                                              
                                            </td>
                                            <td class="print-d-none">
                                                <div class="dropdown ol-icon-dropdown ol-icon-dropdown-transparent">
                                                    <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="fi-rr-menu-dots-vertical"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                    
                                                        @if (has_permission('admin.wallet_category.destroy'))
                                                            <li>
                                                                <a class="dropdown-item delete-btn" data-bs-toggle="modal" data-bs-target="#confirmModal_{{$row->id}}"
                                                                data-url="{{ route('admin.wallet_category.destroy', $row->id) }}" href="javascript:void(0)">
                                                                    {{ get_phrase('Delete') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>

                                            <div class="modal fade" id="confirmModal_{{$row->id}}" aria-labelledby="ajaxModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content pt-2">
                                                        <div class="modal-body text-center">
                                                            <div class="icon icon-confirm">
                                                                <i class="fi-rr-exclamation"></i>
                                                            </div>
                                                            <p class="title">{{ get_phrase('Are you sure?') }}</p>
                                                            <p class="text-muted">{{ get_phrase("You can't bring it back!") }}</p>
                                            
                                                        </div>
                                                        <div class="modal-footer justify-content-center">
                                                            <button type="button" class="btn ol-btn-secondary fw-500" data-bs-dismiss="modal">{{ get_phrase('Cancel') }}</button>
                                                            <form class="deleteForm" method="POST" action="{{ route('admin.wallet_category.destroy', $row->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn ol-btn-success fw-500">{{ get_phrase('Confirm') }}</button>
                                                            </form>                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @include('admin.no_data')
                    @endif

                    <!-- Data info and Pagination -->
                    @if (count($wallet_categories) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($wallet_categories) . ' ' . get_phrase('of') . ' ' .  ' ' . get_phrase('data') }}
                            </p>
                            {{-- {{ $wallet_categories->links() }} --}}
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
