@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header-nav')
    <li class="header__nav--li">
        <a href="" class="header__nav--link">管理画面</a>
    </li>
@endsection

@section('content')
    <div class="index__heading">
        <h3 class="heading__ttl">{{ $greeting }}</h3>
    </div>
    <div class="index__staff">
        <ul class="staff__ul">
            @foreach ($employees as $employee)
                <li class="staff__li">
                    <form action="{{ route('employee.attendance') }}" class="staff__form" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <button type="submit" class="staff__form--btn">{{ $employee->last_name }} {{ $employee->first_name }}</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

@endsection
