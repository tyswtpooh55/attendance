@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employees/request.css') }}">
@endsection

@section('content')
    <h3 class="employee__name">{{ $employee->last_name }} {{ $employee->first_name }}さん</h3>
    <button onclick="history.back()" class="back-btn">←戻る</button>
    <div class="request__heading">
        <h4 class="heading__ttl">勤務情報{{ $updateWork ? '変更' : '追加' }}申請</h4>
    </div>
    <div class="request__form">
        <form action="{{ route('employee.confirmRequest') }}" method="POST" class="request__form--form">
            @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">

            @if ($requestedWork)
                <input type="hidden" name="work_id" value="{{ $requestedWork->id }}">
            @endif

                <input type="date" name="date" value="{{ $requestedDate }}" readonly class="form__date"><br>
        @if ($requestedWork)
            <p class="form__label">勤務</p>
            <div class="form__wrap">
                <input type="time" name="work_in" value="{{ $requestedWork->work_in->format('H:i') }}" class="form__record">
                <span class="hyphen">-</span>
                <input type="time" name="work_out" value="{{ $requestedWork->work_out ? $requestedWork->work_out->format('H:i') : '' }}" class="form__record">
                <input type="checkbox" name="delete-work" id="delete-work" value="{{ $requestedWork->id }}" class="delete-record__check">
                <label for="delete-work" class="delete-record__label">
                    DELETE
                </label>

            </div>
            @error('work_out')
                <p class="error">{{ $message }}</p>
            @enderror
            <p class="form__label form__label--breaking">休憩</p>
            <div class="add__breaking">
                <button id="addBreakingBtn" class="add__breaking--btn">休憩を追加</button>
            </div>
            @foreach ($requestedWork->breakings as $index => $breaking)
            <div class="form__wrap">
                <input type="hidden" name="breaking_id[]" value="{{ $breaking ?  $breaking->id : null }}">
                <input type="time" name="breaking_in[]" value="{{ $breaking ? $breaking->breaking_in->format('H:i') : '' }}" class="form__record">
                <span class="hyphen">-</span>
                <input type="time" name="breaking_out[]" value="{{ $breaking->breaking_out ? $breaking->breaking_out->format('H:i') : '' }}" class="form__record">
                <input type="checkbox" name="delete-breaking[]" id="delete-breaking-{{ $index }}" value="{{ $breaking->id }}" class="delete-record__check">
                <label for="delete-breaking-{{ $index }}" class="delete-record__label">
                    DELETE
                </label>
            </div>
            @error('breaking_in' . $index)
                <p class="error">{{ $message }}</p>
            @enderror
            @error('breaking_out' . $index)
                <p class="error">{{ $message }}</p>
            @enderror
            @endforeach
        @else
                <p class="form__label">勤務</p>
                <div class="form__wrap">
                    <input type="time" name="work_in" class="form__record">
                    <span class="hyphen">-</span>
                    <input type="time" name="work_out" class="form__record">
                </div>

                <p class="form__label">休憩</p>
                <div class="add__breaking">
                    <button id="addBreakingBtn" class="add__breaking--btn">休憩を追加</button>
                </div>
        @endif

            <div id="addBreakingContainer" class="add__breaking--container"></div>

            <div class="form__reason">
                <select name="reason" id="reason" class="form__select">
                    <option value="" hidden>{{ $updateWork ? '変更' : '追加' }}理由を選択</option>
                    <option value="打刻忘れ">打刻忘れ</option>
                    <option value="other" id="reasonForChangeIsOther">その他</option>
                </select>
                <div id="reasonForChangeIsOtherContainer" class="reason__container"></div>
                @error('reason')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form__btn">
                <button type="submit" class="form__btn--btn">内容確認</button>
            </div>

        </form>

    </div>
    <script src="{{ asset('js/change-record.js') }}"></script>
    <script src="{{ asset('js/request-reason.js') }}"></script>
@endsection
