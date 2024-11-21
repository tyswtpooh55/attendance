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
        <h3 class="employee__name">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
    </div>

    @if ($isBirthday)
        <div class="birthday">
            <p class="birthday-message">HAPPY BIRTHDAY</p>
        </div>
    @endif

    <div class="paid-holiday">
        <a href="{{ route('employee.applyPaidHoliday', ['employee_id' => $employee->id]) }}" class="holiday__link">有給休暇申請</a>
    </div>
    <div class="unstamped-msg">
        <p class="error">{!! $missStampingMsg !!}</p>
    </div>
    <div class="stamping__box">

        <div class="working__btn">
            <div class="stamping-form">
                <form action="{{ route('employee.stamped') }}" method="post" class="stamping-form__form">
                    @csrf
                    <input type="hidden" name="employee_id" value={{ $employee->id }}>
                    <input type="hidden" name="action" value="work_in">
                    <button type="submit" class="stamping-form__btn" {{ $workInBtnDisable ? 'disabled' : '' }}>勤務開始</button>
                </form>
            </div>
            <div class="stamping-form">
                <form action="{{ route('employee.stamped') }}" method="post" class="stamping-form__form">
                    @csrf
                    <input type="hidden" name="employee_id" value={{ $employee->id }}>
                    <input type="hidden" name="action" value="work_out">
                    <button type="submit" class="stamping-form__btn" {{ $workOutBtnDisable ? 'disabled' : '' }}>勤務終了</button>
                </form>
            </div>
        </div>

        <div class="stamping-form lunch-breaking__btn">
            <form action="{{ route('employee.stamped') }}" method="post" class="stamping-form__form">
                @csrf
                <input type="hidden" name="employee_id" value={{ $employee->id }}>
                <input type="hidden" name="action" value="lunch_breaking">
                <button type="submit" class="stamping-form__btn" {{ $lunchTimeBtnDisable ? 'disabled' : '' }}>昼休み</button>
            </form>
        </div>

        <div class="breaking__btn">
            <div class="stamping-form">
                <form action="{{ route('employee.stamped') }}" method="post" class="stamping-form__form">
                    @csrf
                    <input type="hidden" name="employee_id" value={{ $employee->id }}>
                    <input type="hidden" name="action" value="breaking_in">
                    <button type="submit" class="stamping-form__btn" {{ $goOutBtnDisable ? 'disabled' : '' }}>外出</button>
                </form>
            </div>
            <div class="stamping-form">
                <form action="{{ route('employee.stamped') }}" method="post" class="stamping-form__form">
                    @csrf
                    <input type="hidden" name="employee_id" value={{ $employee->id }}>
                    <input type="hidden" name="action" value="breaking_out">
                    <button type="submit" class="stamping-form__btn" {{ $comeBackBtnDisable ? 'disabled' : '' }}>帰院</button>
                </form>
            </div>
        </div>

    </div>
    <div class="top-page">
        <a href="/" class="top-page__link">⇦ TOP PAGE</a>
    </div>
@endsection
