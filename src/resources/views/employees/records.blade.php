@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/records.css') }}">
@endsection

@section('header-nav')
    <li class="header__nav--li">
        <a href="" class="header__nav--link">変更申請</a>
    </li>
@endsection

@section('content')
    <div class="records__heading">
        <h3 class="records__ttl">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
        <h4 class="records__month">
            <a href="{{ route('employee.records',['employee_id' => $employee->id, 'month' => $preMonth->format('Y-m')]) }}" class="month__change">&lt;</a>
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
                    <th class="records__label">操作</th>
                </tr>
                @foreach ($records as $record)
                    <tr class="records__row">
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
                            <form action="{{ route('employee.changeRequest', ['employee_id' => $employee->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="record" value="{{ $record['work_id'] }}">
                                <button type="submit" class="record__change">変更申請</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
        </table>
    </div>
@endsection
