@extends('layouts.default')
@push('title', get_phrase('wallet payment History'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    <section class="wishlist-content">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('frontend.default.student.left_sidebar')

                <div class="col-lg-9">
                    <h4 class="g-title mb-5">{{ get_phrase('wallet payment History') }}</h4>
                    <div class="my-panel purchase-history-panel">
                        <form class="form-inline" action="#" method="get">
                                @csrf
                                <div class="row row-gap-3">
                                    <div class="col-md-9">
                                            <input type="number" class="form-control ol-form-control" name="balance" id="balance" style="background-color:rgba(var(--c-bg-rgb)) !important" oninput="this.value = Math.max(1, Math.abs(this.value))" required placeholder="ادخل المبلغ">
                                        </div>
                                    @error('balance')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                <div class="col-md-3">
                                    <button type="submit" id="charge_button" class="btn ol-btn-primary w-100">اشحن</button>
                                </div>
                            </div>
                        </form>



                        @if ($user_wallets->count() > 0)
                            <div class="table-responsive">
                                <table class="table eTable">
                                    <thead>
                                        <tr>
                                            {{-- <th>{{ get_phrase('student Name') }}</th> --}}
                                            <th>{{ get_phrase('balance') }}</th>
                                            <th>{{ get_phrase('Method') }}</th>
                                            <th>{{ get_phrase('uuid') }}</th>
                                            <th>{{ get_phrase('payment_id') }}</th>
                                            <th>{{ get_phrase('date') }}</th>
                                            <th>{{ get_phrase('added') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user_wallets as $log)
                                            <tr>
                                                <td>{{ $log->balance }}</td>
                                                <td>{{ ucfirst($log->type) }}</td>
                                                <td>{{ $log->uuid }}</td>
                                                <td>{{ $log->payment_id }}</td>
                                                <td>{{ date('Y-m-d', strtotime($log->created_at)) }}</td>
                                                <td>{{ $log->added->name }}</td>
                                                {{-- <td>
                                                    <a href="{{ route('invoice', $log->id) }}"
                                                        class="d-flex align-items-center justify-content-center btn btn-primary text-18 text-white py-3" data-bs-toggle="tooltip" data-bs-title="{{get_phrase('Print Invoice')}}">
                                                        <i class="fi fi-rr-print d-inline-flex"></i>
                                                    </a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="row bg-white radius-10 mx-2">
                                <div class="com-md-12">
                                    @include('frontend.default.empty')
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if (count($user_wallets) > 0)
                <div class="entry-pagination">
                    <nav aria-label="Page navigation example">
                        {{ $user_wallets->links() }}
                    </nav>
                </div>
            @endif
            <!-- Pagination -->
        </div>
    </section>
    <!------------ purchase history area End  ------------>
@endsection
@push('js')@endpush
