<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeRequestForm;
use App\Models\Breaking;
use App\Models\ChangeRequest;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\PaidHoliday;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yasumi\Yasumi;

class EmployeesController extends Controller
{
    //従業員選択ページ
    public function index()
    {
        $currentHour = Carbon::now()->hour;
        $greeting = '';

        if ($currentHour >= 0 && $currentHour < 11) {
            $greeting = 'おはようございます';
        } elseif ($currentHour >= 11 && $currentHour <15) {
            $greeting = 'こんにちは';
        } else {
            $greeting = 'お疲れさまです';
        }


        $today = Carbon::today()->format('Y-m-d');
        $employees = Employee::with(['works' => function($query) use ($today) {
            $query->where('date', $today);
        }, 'works.breakings'])->get();

        foreach ($employees as $employee) {
            $todayWork = $employee->works->first();

            if ($todayWork) {
                $now = Carbon::now();
                $workIn = Carbon::parse($todayWork->work_in);
                $workOut = $todayWork->work_out ? Carbon::parse($todayWork->work_out) : null;

                $isBreaking = $todayWork->breakings->some(function ($breaking) use ($now) {
                    $breakingIn = Carbon::parse($breaking->breaking_in);
                    return !$breaking->breaking_out && $now->greaterThan($breakingIn);
                });

                if ($isBreaking) {
                    $employee->status = 'nowBreaking';
                } elseif ($now->greaterThan($workIn) && !$todayWork->work_out) {
                    $employee->status = 'nowWorking';
                } else {
                    $employee->status = 'off';
                }
            } else {
                $employee->status = 'off';
            }
        }


        return view('index', compact(
            'greeting',
            'employees'
        ));
    }

    //打刻ページ
    public function attendance(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $employee = Employee::where('id', $employee_id)->first();

        //打刻ボタンの無効化
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        $thisDayWork = Work::where('employee_id', $employee_id)
            ->where('date', $today)
            ->latest('work_in')
            ->first();  //当日の勤務記録

        $thisDayWorkBreaking =  //当日の勤務記録の休憩のうち最新のもの
            $thisDayWork
                ? Breaking::where('work_id', $thisDayWork->id)
                    ->latest('breaking_in')
                    ->first()
                : null;

        //最新の休憩記録が存在し、現在時刻がその間にある場合休憩中と判断
        $isBreakingNow = $thisDayWorkBreaking &&
            ($now->greaterThanOrEqualTo(Carbon::parse($thisDayWorkBreaking->breaking_in)) &&
            $now->lessThanOrEqualTo(Carbon::parse($thisDayWorkBreaking->breaking_out))
            ||
            empty($thisDayWorkBreaking->breaking_out));

            //勤務開始ボタン無効化
        $workInBtnDisable =
            $thisDayWork !== null && empty($thisDayWork->work_out);    //当日の勤務記録が未退勤

            //勤務終了ボタン無効化
        $workOutBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            $isBreakingNow;

            //昼休憩ボタン無効化
        $lunchTimeBtnDisable =
            !$thisDayWork ||
            !empty($thisDayWork->work_out) ||
            $isBreakingNow ||
            $now->lessThanOrEqualTo(Carbon::createFromTimeString('12:30:00')) ||
            $now->greaterThanOrEqualTo(Carbon::createFromTimeString('14:00:00'));

            //外出ボタン無効化
        $goOutBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            $isBreakingNow;

            //帰院ボタンの無効化
        $comeBackBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            !$thisDayWorkBreaking ||    //当日の休憩記録がない
            !$isBreakingNow;


        // お誕生日おめでとう
        $isBirthday = false;
        if ($employee && $employee->birthday) {
            $today = now()->format('m-d');
            $birthday = Carbon::parse($employee->birthday)->format('m-d');
            $isBirthday = ($today === $birthday);
        }

        //前勤務の退勤打刻がない
        $today = Carbon::today()->format('Y-m-d');

        $changeRequestedWorkIds = ChangeRequest::where('employee_id', $employee_id)
            ->pluck('work_id');

        $incompleteWorks = Work::where('employee_id', $employee_id)
            ->where('date', '<', $today)
            ->whereNull('work_out')
            ->whereNotIn('id', $changeRequestedWorkIds)
            ->orderBy('date', 'desc')
            ->get();

        $missStampingMsg = '';
        if (!$incompleteWorks->isEmpty()) {
            $incompleteWorkDates = $incompleteWorks->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->isoFormat('MM月DD日(ddd)');
            })->implode(',');
            $missStampingMsg = $incompleteWorkDates . 'の勤務打刻が完了していません。<br>勤務記録で変更申請を行ってください。';
        }

        return view('employees/stamping', compact(
            'employee',
            'workInBtnDisable',
            'workOutBtnDisable',
            'lunchTimeBtnDisable',
            'goOutBtnDisable',
            'comeBackBtnDisable',
            'isBirthday',
            'missStampingMsg',
        ));
    }

    //打刻機能
    public function clickedBtn(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $employee = Employee::findOrFail($employee_id);

        $action = $request->input('action');

        $today = Carbon::today();
        $thisDayEndWork = Work::where('employee_id', $employee_id)
            ->where('date', $today)
            ->whereNotNull('work_out')
            ->latest('work_in')
            ->first();  //当日の勤務記録

        switch ($action) {
            case 'work_in':

                //勤務開始
                if ($thisDayEndWork) {
                    $newBreaking = Breaking::create([
                        'work_id' => $thisDayEndWork->id,
                        'breaking_in' => $thisDayEndWork->work_out,
                        'breaking_out' => Carbon::now()->format('H:i'),
                    ]);
                    $thisDayEndWork->update([
                        'work_out' => null,
                    ]);
                } else {
                    $nowWork = Work::create([
                        'employee_id' => $employee_id,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'work_in' => Carbon::now()->format('H:i'),
                    ]);
                }
                break;

            case 'work_out':

                //勤務終了
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest('date')
                    ->first();
                $nowWork->update([
                    'work_out' => Carbon::now()->format('H:i'),
                ]);
                break;

            case 'lunch_breaking':
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest('date')
                    ->first();
                $nowBreaking = Breaking::create([
                    'work_id' => $nowWork->id,
                    'breaking_in' => Carbon::now()->format('H:i'),
                    'breaking_out' => '14:00:00'
                ]);
                break;

            case 'breaking_in':

                //休憩開始
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest('date')
                    ->first();
                $nowBreaking = Breaking::create([
                    'work_id' => $nowWork->id,
                    'breaking_in' => Carbon::now()->format('H:i'),
                ]);
                break;
            case 'breaking_out':

                //休憩終了
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest('date')
                    ->first();
                $nowBreaking = Breaking::where('work_id', $nowWork->id)
                    ->latest('breaking_in')
                    ->first();
                $nowBreaking->update([
                    'breaking_out' => Carbon::now()->format('H:i'),
                ]);
                break;
        }

        return redirect('/');
    }

    //月別勤務記録ページ
    public function records(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);     //従業員情報取得

        $today = Carbon::today();

        //月の選択
        $thisMonth = $request->query('month') ? new Carbon($request->query('month')) : Carbon::parse(Carbon::now()->format('Y-m'));
        $preMonth = $thisMonth->copy()->subMonth();
        $nextMonth = $thisMonth->copy()->addMonth();

        $startOfMonth = $thisMonth->copy()->startOfMonth(); //月の最初の日(YYYY-MM-01 00:00:00.0)
        $endOfMonth = $thisMonth->copy()->endOfMonth(); //月の最後の日(YYYY-MM-28~31? 00:00:00.0)

        //今月の勤務情報を日付をキーにして取得
        $works = Work::with('breakings')
            ->where('employee_id', $employee_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('date');

        //勤務情報変更・追加申請情報を取得
        $changeRequests = ChangeRequest::where('employee_id', $employee_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('date');

        //Yasumiライブラリを使用して、祝祭日を取得
        $nationalHolidays = Yasumi::create('Japan', $thisMonth->year, 'ja_JP');

        //企業休暇
        $companyHolidays = Holiday::whereBetween('date',[$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($holiday) {
                return Carbon::parse($holiday->date)->format('m-d');
            });

        //有給休暇
        $paidHolidays = PaidHoliday::where('employee_id', $employee_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($holiday) {
                return Carbon::parse($holiday->date)->format('m-d');
            });

        //Viewに渡すデータを格納するための配列$records[]を定義
        $records = [];
        //変更・追加申請可能な日を取得(今から一週間前)
        $oneWeekAgo = Carbon::now()->subWeek();

        //$startOfMonthから$endOfMonthまで、$date(日付)をループして日別の$recordを作成
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {

            $dateKey = $date->format('Y-m-d');  //$dateをYYYY-MM-DDにフォーマット
            $work = $works->get($dateKey);  //$dateの勤務情報を取得
            $breakings = $work ? $work->breakings : collect();  //休憩情報を取得

            //勤務時間の計算
            $totalWorkTime = 0; //総勤務時間 の定義
            $trueWorkHours = null;  //総勤務時間-総休憩時間 の定義
            $breakingTime = 0;  //各休憩時間の定義
            $totalBreakingTime = 0; //合計休憩時間の定義

            if ($work && $work->work_out) {
                $workIn = Carbon::parse($work->work_in);
                $workOut = Carbon::parse($work->work_out);
                $totalWorkTime += $workIn->diffInMinutes($workOut);

                foreach ($breakings as $breaking) {
                    $breakingIn = Carbon::parse($breaking->breaking_in);
                    $breakingOut = Carbon::parse($breaking->breaking_out);
                    $breakingTime += $breakingIn->diffInMinutes($breakingOut);
                }
                $totalBreakingTime += $breakingTime;

                $trueWorkTime = $totalWorkTime - $totalBreakingTime;
                //時間表示をHH:MMになるよう計算
                $hours = floor($trueWorkTime / 60);
                $minutes = $trueWorkTime % 60;
                $trueWorkHours = sprintf('%d:%02d', $hours, $minutes);

            } else {
                $trueWorkHours = null;  //勤務情報がなければ総勤務時間はnull
            }


            $changeRequest = $changeRequests->get($dateKey); //$dateの日の勤務情報変更・追加申請情報取得
            $submit_status = $changeRequest ? $changeRequest->status : null;  //$dateの日の申請情報があれば申請状況(status)を取得、なければ申請状況はnull

            $withInOneWeek = $date->greaterThanOrEqualTo($oneWeekAgo) && $date->lessThanOrEqualTo(now());   //$dateの日より一週間前以降かつ今日以前かどうか

            $isSunday = $date->isSunday();  //$dateの日が日曜か(日曜ならtrue)

            $isNationalHoliday = $nationalHolidays->isHoliday($date);   //$dateの日が祝祭日に存在するか(すればtrue)

            //企業休暇と有給休暇(m-dにフォーマットされたもの)を検索するため、$dateをm-dにフォーマット
            $holidayKey = $date->format('m-d');

            $companyHoliday = $companyHolidays->get($holidayKey);   //$dateの日の企業休暇情報を取得。企業休暇の日でなければnull
            $isCompanyHoliday = $companyHoliday !== null;   //企業休暇日か。企業休暇日ならtrue
            $holidayType = $isCompanyHoliday ? optional($companyHoliday->first())->type : ($isNationalHoliday ? 'full' : null); //企業休暇日なら休暇のtype(一日休full、半日休half)を取得。祝祭日なら一日休、どちらでもなければnull

            $paidHoliday = $paidHolidays->get($holidayKey); //当該従業員が$dateの日に有給休暇取得をしているか。取得している日ならtrue
            $isPaidHoliday = $paidHoliday !== null;
            $paidHolidayType = $isPaidHoliday ? optional($paidHoliday->first())->type : null;   //$dateが当該従業員が有給取得している日なら休暇のtype(一日休full、午前休morning、午後休afternoon)を取得。有給休暇日でなければnull

            $records[] = [
                'work_id' => $work ? $work->id : null,
                'date' => $date->isoFormat('D(ddd)'),
                'is_sunday' => $isSunday,
                'is_holiday' => $isNationalHoliday || $isCompanyHoliday,
                'holiday_type' => $holidayType,
                'is_paid_holiday' => $isPaidHoliday,
                'paid_holiday_type' => $paidHolidayType,
                'is_today' => $date->isSameDay($today),
                'work_in' => $work ? $work->work_in : null,
                'work_out' => $work ? $work->work_out : null,
                'breakings' => $breakings->map(function ($breaking) {
                    return [
                        'breaking_in' => $breaking->breaking_in,
                        'breaking_out' => $breaking->breaking_out,
                    ];
                })->toArray(),
                'submit_status' => $submit_status,
                'trueWorkTime' => $trueWorkHours,
                'withInOneWeek' => $withInOneWeek,
            ];
        }

        return view('employees/records', compact(
            'employee',
            'thisMonth',
            'preMonth',
            'nextMonth',
            'records',
        ));
    }

    //勤務時間変更申請ページ
    public function changeRequest(Request $request, $employee_id)
    {
        $employee = Employee::find($employee_id);

        $updateWork = '';
        //変更
        if ($request->input('record')) {
            $requestedWork = Work::find($request->input('record'));
            $requestedDate = $requestedWork->date;
            $updateWork = true;
        }

        //追加
        if ($request->input('date')) {
            $requestedWork = null;
            $requestedDate = Carbon::parse(substr($request->input('date'), 0, 10))->format('Y-m-d');
            $updateWork = false;
        }

        return view('employees/request', compact(
            'employee',
            'requestedWork',
            'requestedDate',
            'updateWork',
        ));
    }

    //勤務時間変更申請確認ページ
    public function confirmChangeRequest(ChangeRequestForm $request)
    {
        $employee = Employee::find($request->input('employee_id'));
        $requestData = $request->all();

        $requestData['work_id'] = $requestData['work_id'] ?? null;
        $requestData['work_out'] = $requestData['work_out'] ?? '';
        $requestData['breaking_in'] = $requestData['breaking_in'] ?? [];
        $requestData['breaking_out'] = $requestData['breaking_out'] ?? [];
        $requestData['reason'] = $requestData['reason'] ?? '';

        $updateWork = $request->has('work_id');     //更新

        //勤務情報削除申請の処理
        $deleteWork = $request->input('delete-work', false);
        $deleteBreakings = $request->input('delete-breaking', []);

        if ($deleteWork) {
            $requestDeleteWork = Work::findOrFail($deleteWork);
        }

        //空の休憩データをフィルタリング
        $breakingsForm = collect($requestData['breaking_in'] ?? [])
            ->zip($requestData['breaking_out'] ?? [])
            ->filter(function ($times) {
                return !empty($times[0]) && !empty($times[1]);
            })
            ->map(function ($times) {
                return [
                    'breaking_in' => $times[0],
                    'breaking_out' => $times[1],
                ];
            });

        $reason = $requestData['reason'];
        if ($reason === 'other') {
            if (!empty($requestData['other-reason'])) {
                $reason = $requestData['other-reason'];
            } else {
                $reason = "その他";
            }
        }
        $requestData['reason'] = $reason;

        return view('employees/confirm', compact(
            'employee',
            'requestData',
            'updateWork',
            'deleteWork',
            'deleteBreakings',
            'breakingsForm',
        ));
    }

    //勤務時間変更申請送信
    public function submitChangeRequest(Request $request)
    {
        $requestForChange = $request->all();

        $updateWork = $request->input('work_id');
        $deleteWork = $request->input('delete_work') === 'true';

        $action = 'change';
        if ($deleteWork) {
            $action = 'delete';
        } elseif (!$updateWork) {
            $action = 'add';
        }

        ChangeRequest::create([
            'employee_id' => $requestForChange['employee_id'],
            'date' => $requestForChange['date'],
            'work_id' => $requestForChange['work_id'] ?? null,
            'work_in' => $requestForChange['work_in'] ?? null,
            'work_out' => $requestForChange['work_out'] ?? null,
            'breakings' => json_encode($this->formatBreakings($requestForChange)),
            'reason' => $requestForChange['reason'],
            'action' => $action,
            'status' => 'pending',
        ]);
        return redirect()->route('employee.records', ['employee_id' => $requestForChange['employee_id']]);
    }

    private function formatBreakings($requestForChange)
    {
        $breakings = collect($requestForChange['breaking_in'] ?? [])
            ->zip($requestForChange['breaking_out'] ?? [])
            ->filter(function ($times) {
                return !empty($times[0]) && !empty($times[1]);
            })
            ->map(function ($times) {
                return [
                    'breaking_in' => $times[0],
                    'breaking_out' => $times[1],
                ];
            })
            ->values()
            ->all();

        return empty($breakings) ? null : $breakings;
    }
}
