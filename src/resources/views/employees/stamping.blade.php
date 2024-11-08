@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/stamping.css') }}">
@endsection

@section('header-nav')
    <li class="header__nav--li">
        <a href="{{ route('employee.records', ['employee_id' => $employee->id]) }}" class="header__nav--link">勤務記録</a>
    </li>
@endsection

@section('content')
    <div class="stamping__heading">
        <h3 class="heading__ttl">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
    </div>

    @if ($isBirthday)
        <div class="birthday">
            <p class="birthday-message">HAPPY BIRTHDAY</p>
        </div>
    @endif

    <div class="stamping__box">
        <div class="stamping-form">
            <form action="{{ route('employee.stamped', ['employee_id' => $employee->id]) }}" method="post" class="stamping-form__form">
                @csrf
                <input type="hidden" name="action" value="work_in">
                <button type="submit" class="stamping-form__btn" {{ $workInBtnDisable ? 'disabled' : '' }}>勤務開始</button>
            </form>
        </div>
        <div class="stamping-form">
            <form action="{{ route('employee.stamped', ['employee_id' => $employee->id]) }}" method="post" class="stamping-form__form">
                @csrf
                <input type="hidden" name="action" value="work_out">
                <button type="submit" class="stamping-form__btn" {{ $workOutBtnDisable ? 'disabled' : '' }}>勤務終了</button>
            </form>
        </div>
        <div class="stamping-form">
            <form action="{{ route('employee.stamped', ['employee_id' => $employee->id]) }}" method="post" class="stamping-form__form">
                @csrf
                <input type="hidden" name="action" value="breaking_in">
                <button type="submit" class="stamping-form__btn" {{ $breakingInBtnDisable ? 'disabled' : '' }}>休憩開始</button>
            </form>
        </div>
        <div class="stamping-form">
            <form action="{{ route('employee.stamped', ['employee_id' => $employee->id]) }}" method="post" class="stamping-form__form">
                @csrf
                <input type="hidden" name="action" value="breaking_out">
                <button type="submit" class="stamping-form__btn" {{ $breakingOutBtnDisable ? 'disabled' : '' }}>休憩終了</button>
            </form>
        </div>
    </div>
    <div class="top-page">
        <a href="/" class="top-page__link">⇦ TOP PAGE</a>
    </div>
@endsection
