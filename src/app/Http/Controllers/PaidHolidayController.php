<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PaidHoliday;
use Illuminate\Http\Request;

class PaidHolidayController extends Controller
{
    public function applyPaidHoliday($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        return view('employees/apply-paid-holiday', compact(
            'employee',
        ));
    }

    public function submitPaidHoliday(Request $request)
    {
        $count = 0.5;
        if ($request->input('type') == 'full') {
            $count = 1;
        }

        PaidHoliday::create([
            'employee_id' => $request->input('employee_id'),
            'date' => $request->input('date'),
            'type' =>$request->input('type'),
            'reason' =>$request->input('reason'),
            'status' => 'pending',
            'count' => $count,
        ]);

        return redirect()->back();
    }
}
