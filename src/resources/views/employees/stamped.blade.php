@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/stamped.css') }}">
@endsection

@section('header-nav')
    <li class="header__nav--li">
        <a href="{{ route('employee.records', ['employee_id' => $employee->id]) }}" class="header__nav--link">勤務記録</a>
    </li>
@endsection

@section('content')
    <div class="stamped__msg">
        <h3 class="msg__ttl">{{ $employee->last_name }}さん</h3>
        <p class="msg__msg">{{ $stampedMsg }}</p>
        <p class="msg__time">{!! $timestampMsg !!}</p>
    </div>

    <div class="top-page">
        <a href="/" class="top-page__link">⇦ TOP PAGE</a>
    </div>
@endsection
