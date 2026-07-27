@extends('bookstore::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('bookstore.name') !!}</p>
@endsection
