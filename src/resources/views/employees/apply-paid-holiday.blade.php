@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/apply-paid-holiday.css') }}">
@endsection

@section('content')
<div class="paid-holiday__heading">
    <h3 class="employee__name">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
    <h4 class="paid-holiday__ttl">有給休暇申請</h4>
</div>
<div class="paid-holiday__form">
    <form action="{{ route('employee.submitPaidHoliday') }}" method="POST" class="paid-holiday__form--form">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
        <div class="paid-holiday__wrap">
            <label for="date" class="paid-holiday__label">取得日付</label>
            <input type="date" id="date" name="date" value="" required class="paid-holiday__date">
        </div>
        <div class="paid-holiday__wrap">
            <label for="type" class="paid-holiday__label">取得内容</label>
            <div class="radio__wrap">
                <input type="radio" name="type" id="full" value="full" class="paid-holiday__type">
                <label for="full" class="holiday-type__radio">一日休み</label>
            </div>
            <div class="radio__wrap">
                <input type="radio" name="type" id="morning" value="morning" class="paid-holiday__type">
                <label for="morning" class="holiday-type__radio">午前休み</label>
            </div>
            <div class="radio__wrap">
                <input type="radio" name="type" id="afternoon" value="afternoon" class="paid-holiday__type">
                <label for="afternoon" class="holiday-type__radio">午後休み</label>
            </div>
        </div>
        <div class="paid-holiday__wrap">
            <label for="reason" class="paid-holiday__label">取得理由</label>
            <textarea name="reason" id="reason" class="paid-holiday__text"></textarea>
        </div>
        <button type="submit" class="paid-holiday__btn">申請する</button>


    </form>
</div>
@endsection
