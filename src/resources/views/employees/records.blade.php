@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/records.css') }}">
@endsection

@section('header-nav')
    <li class="header__nav--li">
        <form action="{{ route('employee.attendance') }}" class="staff__form" method="POST">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <button type="submit" class="staff__form--btn">{{ $employee->last_name }} {{ $employee->first_name }}</button>
        </form>
    </li>
@endsection

@section('content')
    <div class="records__heading">
        <h3 class="employee__name">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
        <h4 class="records__month">
            <a href="{{ route('employee.records', ['employee_id' => $employee->id, 'month' => $preMonth->format('Y-m')]) }}" class="month__change">&lt;</a>
            {{ $thisMonth->format('Y月m月') }}
            <a href="{{ route('employee.records',['employee_id' => $employee->id, 'month' => $nextMonth->format('Y-m')]) }}" class="month__change">&gt;</a>
        </h4>
    </div>
    <div class="records__content">
        <table class="records__table">
                <tr class="records__row">
                    <th class="records__label">Date</th>
                    <th class="records__label">勤務</th>
                    <th class="records__label">休憩</th>
                    <th class="records__label">勤務時間</th>
                    <th class="records__label"></th>
                    <th class="records__label">メモ</th>
                </tr>
                @foreach ($records as $record)

                    @php
                        $rowClass = 'records__row'; //records__rowのclass
                        if ($record['is_today']) {
                            $rowClass .= ' today-row';  //今日
                        }
                        if ($record['is_sunday'] || ($record['is_holiday'] && $record['holiday_type'] == 'full')) {
                            $rowClass .= ' holiday-row';    //一日休、日曜日、祝祭日
                        }
                        if ($record['is_holiday'] && $record['holiday_type'] == 'half') {
                            $rowClass .= ' half-day-off-row';   //企業休暇の半日休
                        }
                        if ($record['is_paid_holiday'] && $record['paid_holiday_type'] == 'full') {
                            $rowClass .= ' full-paid-holiday';  //有給一日休
                        }
                        if ($record['is_paid_holiday'] && $record['paid_holiday_type'] == 'morning') {
                            $rowClass .= ' morning-paid-holiday';   //有給午前休
                        }
                        if ($record['is_paid_holiday'] && $record['paid_holiday_type'] == 'afternoon') {
                            $rowClass .= ' afternoon-paid-holiday';     //有給午後休
                        }
                    @endphp

                    <tr class="{{ $rowClass }}">
                        <td class="records__data">{{ $record['date'] }}</td>
                        <td class="records__data">
                        @if ($record['work_in'])
                            {{ $record['work_in'] ? \Carbon\Carbon::parse($record['work_in'])->format('H:i') :
                                '-' }} - {{ $record['work_out'] ? \Carbon\Carbon::parse($record['work_out'])->format('H:i')
                                : '-' }}
                        @else
                            -
                        @endif
                        </td>
                        <td class="records__data">
                            @if(count($record['breakings']) > 0)
                                @foreach ($record['breakings'] as $breaking)
                                {{ $breaking['breaking_in'] ? \Carbon\Carbon::parse($breaking['breaking_in'])->format('H:i') : '-' }} - {{ $breaking['breaking_out'] ? \Carbon\Carbon::parse($breaking['breaking_out'])->format('H:i') : '-'}}<br>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td class="records__data">{{ $record['trueWorkTime'] !== null ? $record['trueWorkTime'] : '-' }}</td>
                        <td class="records__data">
                            @if ($record['withInOneWeek'] && !($record['is_sunday'] || $record['is_holiday']))

                                @if ($record['submit_status'] == 'pending')
                                申請中
                                @elseif($record['submit_status'] == 'approved')
                                承認済み
                                @elseif($record['submit_status'] == 'rejected')
                                拒否
                                @else
                                    <form action="{{ route('employee.changeRequest', ['employee_id' => $employee->id]) }}" method="GET">
                                    @csrf
                                    @if ($record['work_in'])
                                    <input type="hidden" name="record" value="{{ $record['work_id'] }}">
                                    <button type="submit" class="record__change">変更申請</button>
                                    @else
                                    <input type="hidden" name="date" value="{{ $thisMonth->format('Y-m') . '-' . $record['date'] }}">
                                    <button type="submit" class="record__change">追加</button>
                                    @endif
                                @endif

                            @else
                                <span>-</span>
                            @endif
                            </form>
                        </td>
                        <td class="records__data">

                        </td>
                    </tr>
                @endforeach
        </table>
    </div>
@endsection
