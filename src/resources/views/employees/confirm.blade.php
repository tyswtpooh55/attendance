@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/confirm.css') }}">
@endsection

@section('content')
    <div class="confirm__content">
        <h3 class="employee__name">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
        <h4 class="confirm__ttl">勤務情報の{{ $updateWork ? '変更' : '追加' }}</h4>
        <div class="confirm__form">
            <form action="{{ route('employee.submitRequest') }}" method="POST"  class="confirm__form--form">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="work_id" value="{{ $requestData['work_id'] }}">
                <input type="hidden" name="delete_work" value="{{ $deleteWork ? 'true' : 'false' }}">
                @foreach ($requestData['breaking_in'] as $index => $breakingIn)
                    @if (in_array($breakingIn, $deleteBreakings))
                        <input type="hidden" name="delete_breaking[]" value="{{ $breakingIn }}">
                    @endif
                @endforeach

                <table class="confirm__table">
                    <tr class="confirm__row">
                        <th class="confirm__label">日付</th>
                        <td colspan="2" class="confirm__date">
                            <input type="date" name="date" value="{{ $requestData['date'] }}" readonly class="confirm__input">
                        </td>
                    </tr>

                    <tr class="confirm__row">
                        <th class="confirm__label">勤務</th>
                        @if (!$deleteWork)
                            <td class="confirm__data">
                                <input type="time" name="work_in" value="{{ $requestData['work_in'] }}" readonly class="confirm__input">
                            </td>
                            <td class="confirm__data">
                                <input type="time" name="work_out" value="{{ $requestData['work_out'] ?? '' }}" readonly class="confirm__input">
                            </td>
                        @else
                            <td colspan="2" class="confirm__data">-</td>
                        @endif
                    </tr>

                    <tr class="confirm__row">
                        <th class="confirm__label" rowspan="{{ max(1,count($requestData['breaking_in'])) }}">休憩</th>
                        @if ($deleteWork || $breakingsForm->isEmpty())
                            <td colspan="2" class="confirm__data">-</td>
                        @else
                            @foreach ($requestData['breaking_in'] as $index => $breakingIn)
                                @if (!isset($requestData['breaking_id'][$index]) ||  !in_array($requestData['breaking_id'][$index], $deleteBreakings))
                                    @if ($index > 0)
                                        </tr><tr class="confirm__row">
                                    @endif
                                    <td class="confirm__data">
                                        <input type="time" name="breaking_in[]" value="{{ $breakingIn }}" readonly class="confirm__input">
                                    </td>
                                    <td class="confirm__data">
                                        <input type="time" name="breaking_out[]" value="{{ $requestData['breaking_out'][$index] ?? '-' }}" readonly class="confirm__input">
                                    </td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                    <tr class="confirm__row">
                        <th class="confirm__label">{{ $updateWork ? '変更' : '追加' }}理由</th>
                        <td colspan="2" class="confirm__data">{{ $requestData['reason'] }}</td>
                        <input type="hidden" name="reason" value="{{ $requestData['reason'] }}">
                    </tr>
                </table>
                <button class="confirm__btn--submit">{{ $updateWork ? '変更' : '追加' }}を申請する</button>
                <button type="button" onclick="history.back()" class="confirm__btn--fix">修正</button>

            </form>
        </div>
    </div>
@endsection
