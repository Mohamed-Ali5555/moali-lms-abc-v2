@extends('layouts.admin')
@push('title', get_phrase('Coupon'))
@push('meta')@endpush
@push('css')
    <style>
        .btn-link i {
            transition: color 0.2s ease;
        }
        .btn-link:hover i {
            color: #0d6efd; /* أزرق هادي */
        }

    </style>
@endpush


@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-badge-percent"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Coupon') }}
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Create and manage payment coupons') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.coupon.create'))
                    <a href="{{ route('admin.coupon.create') }}" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add Coupon') }}</span>
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
                        @if (has_permission('admin.coupon.export'))
                        <div class="col-md-3 pt-2 pt-md-0">
                            <div class="custom-dropdown">
                                <button class="dropdown-header btn ol-btn-light">
                                    {{ get_phrase('Export') }}
                                    <i class="fi-rr-file-export ms-2"></i>
                                </button>
                                <ul class="dropdown-list">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.coupon.print.all',['type'=>'payment']) }}"><i class="fi-rr-print"></i> {{ get_phrase('Print') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    @if (has_permission('admin.coupon.search'))

                        <div class="col-md-9 mt-3 mt-md-0">
                            <form action="{{ route('admin.coupons') }}" method="get" class="d-flex align-items-center gap-3 justify-content-end flex-wrap">

                                <!-- حقل البحث -->
                                <div class="flex-grow-1" style="max-width: 200px;">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           placeholder="{{ get_phrase('Search coupon') }}"
                                           class="form-control">
                                </div>
                            
                                <!-- نوع الكوبون -->
                                <div class="flex-grow-1" style="max-width: 200px;">
                                    <select class="form-control" name="type" id="type" onchange="toggleCouponFields()" required>
                                        <option value="" disabled selected>{{ get_phrase('Choose coupon type ...') }}</option>
                                        <option value="recharge" {{ request('type') == 'recharge' ? 'selected' : '' }}>{{ get_phrase('Recharge Coupon') }}</option>
                                        <option value="discount" {{ request('type') == 'discount' ? 'selected' : '' }}>{{ get_phrase('Discount Coupon') }}</option>
                                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>{{ get_phrase('Payment Coupon') }}</option>
                                    </select>
                                </div>
                            
                                <!-- زر البحث -->
                                <button type="submit" class="btn ol-btn-primary d-flex align-items-center">
                                    <i class="fas fa-search me-1"></i> {{ get_phrase('Search') }}
                                </button>
                            
                            </form>
                            
                        </div>
                    </div>

                    @endif
                    <!-- Table -->
                    @if (count($coupons) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($coupons) . ' ' . get_phrase('of') . ' ' . $coupons->total() . ' ' . get_phrase('data') }}
                            </p>
                        </div>
                        <div class="table-responsive course_list" id="course_list">
                            <table class="table eTable eTable-2 print-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ get_phrase('code') }}</th>
                                        <th scope="col">{{ get_phrase('Value') }}</th>
                                        <th scope="col">{{ get_phrase('used') }}</th>
                                        <th scope="col">{{ get_phrase('users') }}</th>

                                        <th scope="col">{{ get_phrase('Status') }}</th>

                                        <th scope="col">{{ get_phrase('start-date') }}</th>
                                        <th scope="col">{{ get_phrase('Expiry') }}</th>
                                        <th scope="col" class="print-d-none">{{ get_phrase('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($coupons as $key => $coupon)
                                        <tr>
                                            <th scope="row">
                                                <p class="row-number">{{ ++$key }}</p>
                                            </th>
                                            <td>
                                                <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                    <div class="dAdmin_profile_name d-flex align-items-center gap-2">
                                                        <h4 class="title fs-14px mb-0">{{ $coupon->code }}</h4>
                                                        <button type="button"
                                                                class="btn btn-link p-0 text-muted"
                                                                onclick="copyToClipboard('{{ $coupon->code }}')"
                                                                title="{{ get_phrase('Copy Code') }}">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>
                                                        {{ $coupon->value }}
                                                    </p>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                @if ($coupon->coupons_log()->count() > 0)
                                                    <i class="fas fa-check-circle text-success" title="{{ get_phrase('Used before') }}"></i>
                                                @else
                                                    <i class="fas fa-circle text-secondary" title="{{ get_phrase('Not used yet') }}"></i>
                                                @endif
                                            </td>
                                            
                                            

                                            <td>
                                                <a href="{{route('admin.coupon.users_coupon',$coupon->id)}}">
                                                   <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                                       <div class="dAdmin_profile_name">
                                                           <span class="btn btn-primary">{{ \App\Models\CouponLog::where('coupon_id',$coupon->id)->distinct('user_id')->count()}}</span>
                                                       </div>
                                                   </div>

                                               </a>
                                            </td>

                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p><span class="badge {{ $coupon->status ? 'bg-success' : 'bg-danger' }} text-white">{{ get_phrase($coupon->status ? 'Active' : 'Inactive') }}</span></p>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $coupon->start_date ? \Carbon\Carbon::parse($coupon->start_date)->format('d-M-Y') : '-' }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dAdmin_info_name min-w-150px">
                                                    <p>{{ $coupon->expiry ? \Carbon\Carbon::parse($coupon->expiry)->format('d-M-Y') : '-' }}</p>
                                                </div>
                                            </td>
                                            <td class="print-d-none">
                                                <div class="dropdown ol-icon-dropdown ol-icon-dropdown-transparent">
                                                    <button class="btn ol-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="fi-rr-menu-dots-vertical"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if (has_permission('admin.coupon.status'))
                                                            <li><a class="dropdown-item" href="#" onclick="confirmModal('{{ route('admin.coupon.status', $coupon->id) }}')">{{ get_phrase($coupon->status ? 'InActive' : 'Activate') }}</a></li>
                                                        @endif 
                                                        @if (has_permission('admin.coupon.delete'))
                                                            <li><a class="dropdown-item" href="javascript:void(0);" onclick="confirmModal('{{ route('admin.coupon.delete', $coupon->id) }}')">{{ get_phrase('Delete') }}</a></li>
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
                    @if (count($coupons) > 0)
                        <div class="admin-tInfo-pagi d-flex justify-content-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' ' . count($coupons) . ' ' . get_phrase('of') . ' ' . $coupons->total() . ' ' . get_phrase('data') }}
                            </p>
                            {{ $coupons->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'تم النسخ بنجاح ✅',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    background: '#f8f9fa',
                    color: '#333',
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'حدث خطأ أثناء النسخ 😅',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    </script>
    <!-- End Admin area -->
@endsection
@push('js')@endpush
