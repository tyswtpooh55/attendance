<?php

namespace App\Http\Controllers;

use App\Models\Breaking;
use App\Models\Employee;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $employees = Employee::all();

        return view('index', compact(
            'greeting',
            'employees'
        ));
    }

    //打刻ページ
    public function attendance(Request $request)
    {
        $employee_id = $request->employee_id;
        $employee = Employee::where('id', $employee_id)->first();

        //打刻ボタンの無効化
        $today = Carbon::today()->format('Y-m-d');

        $thisDayWork = Work::where('employee_id', $employee_id)
            ->where('date', $today)
            ->latest()
            ->first();  //当日の勤務記録

        $thisDayWorkBreaking =  //当日の勤務記録の休憩のうち最新のもの
            $thisDayWork
                ? Breaking::where('work_id', $thisDayWork->id)
                    ->latest('created_at')
                    ->first()
                : null;

            //勤務開始ボタン無効化
        $workInBtnDisable =
            $thisDayWork !== null && empty($thisDayWork->work_out);  //当日の勤務記録が未退勤

            //勤務終了ボタン無効化
        $workOutBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            ($thisDayWorkBreaking && empty($thisDayWorkBreaking->breaking_out));    //当日の休憩記録が休憩中

            //休憩開始ボタン無効化
        $breakingInBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            ($thisDayWorkBreaking && empty($thisDayWorkBreaking->breaking_out));    //当日の休憩記録が休憩中

            //休憩終了ボタンの無効化
        $breakingOutBtnDisable =
            !$thisDayWork ||    //当日の勤務記録がない
            !empty($thisDayWork->work_out) ||     //当日の勤務記録が退勤済み
            !$thisDayWorkBreaking ||    //当日の休憩記録がない
            ($thisDayWorkBreaking && !empty($thisDayWorkBreaking->breaking_out));   //休憩中でない


        // お誕生日おめでとう
        $isBirthday = false;
        if ($employee && $employee->birthday) {
            $today = now()->format('m-d');
            $birthday = Carbon::parse($employee->birthday)->format('m-d');
            $isBirthday = ($today === $birthday);
        }

        return view('employees/stamping', compact(
            'employee',
            'workInBtnDisable',
            'workOutBtnDisable',
            'breakingInBtnDisable',
            'breakingOutBtnDisable',
            'isBirthday',
        ));
    }

    //打刻機能
    public function clickedBtn(Request $request, $employee_id)
    {
        $employee = Employee::find($employee_id);

        $action = $request->input('action');
        $stampedMsg = '';
        $timestamp = '';

        switch ($action) {
            case 'work_in':

                //勤務開始
                $nowWork = Work::create([
                    'employee_id' => $employee_id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'work_in' => Carbon::now()->format('H:i:s'),
                ]);
                $stampedMsg = '今日もよろしくにゃ。';
                $timestampMsg = '勤務開始: ' . $nowWork->work_in;

                break;

            case 'work_out':

                //勤務終了
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest('created_at')
                    ->first();
                $nowWork->update([
                    'work_out' => Carbon::now()->format('H:i:s'),
                ]);
                $workedTime = Work::find($nowWork->id);
                $stampedMsg = '今日もお疲れさまでした。';
                $timestampMsg = '勤務開始: ' . $workedTime->work_in . '<br>' . '勤務終了: ' . $workedTime->work_out;

                break;

            case 'breaking_in':

                //休憩開始
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest()
                    ->first();
                $nowBreaking = Breaking::create([
                    'work_id' => $nowWork->id,
                    'breaking_in' => Carbon::now()->format('H:i:s'),
                ]);
                $stampedMsg = '休憩開始だ！';
                $timestampMsg = '休憩開始: ' . $nowBreaking->breaking_in;

                break;
            case 'breaking_out':

                //休憩終了
                $nowWork = Work::where('employee_id', $employee_id)
                    ->latest()
                    ->first();
                $nowBreaking = Breaking::where('work_id', $nowWork->id)
                    ->latest()
                    ->first();
                $nowBreaking->update([
                    'breaking_out' => Carbon::now()->format('H:i:s'),
                ]);
                $brokenTime = Breaking::find($nowBreaking->id);
                $stampedMsg = 'こっからもよろしくにゃん。';
                $timestampMsg = '休憩開始: ' . $brokenTime->breaking_in . '<br>' . '休憩終了: ' . $nowBreaking->breaking_out;

                break;
            default:
                //不明なアクション
                break;
        }

        return view('employees/stamped', compact(
            'employee',
            'stampedMsg',
            'timestampMsg',
        ));
    }

    //月別勤務記録ページ
    public function records(Request $request, $employee_id)
    {
        $employee = Employee::find($employee_id);

        $thisMonth = $request->query('month') ? new Carbon($request->query('month')) : Carbon::parse(Carbon::now()->format('Y-m'));
        $preMonth = $thisMonth->copy()->subMonth();
        $nextMonth = $thisMonth->copy()->addMonth();

        $startOfMonth = $thisMonth->copy()->startOfMonth();
        $endOfMonth = $thisMonth->copy()->endOfMonth();

        $works = Work::with('breakings')
            ->where('employee_id', $employee_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $records = [];

        for ($date=$startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $work = $works->firstWhere('date', $date->format('Y-m-d'));
            $breakings = $work ? $work->breakings : collect();

            $totalWorkTime = 0;
            $trueWorkHours = null;
            $breakingTime = 0;
            $totalBreakingTime = 0;

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
                $hours = floor($trueWorkTime / 60);
                $minutes = $trueWorkTime % 60;
                $trueWorkHours = sprintf('%d:%02d', $hours, $minutes);

            } else {
                $trueWorkHours = null;
            }


            $records[] = [
                'work_id' => $work? $work->work_id : null,
                'date' => $date->format('d'),
                'work_in' => $work ? $work->work_in : null,
                'work_out' => $work ? $work->work_out : null,
                'breakings' => $breakings->map(function ($breaking) {
                    return [
                        'breaking_in' => $breaking->breaking_in,
                        'breaking_out' => $breaking->breaking_out,
                    ];
                })->toArray(),
                'trueWorkTime' => $trueWorkHours,
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
        $work_id = $request->input('record');

        return view('employees/request');
    }

    //勤務時間変更申請送信
    public function sendChangeRequest()
    {
        //
    }
}
