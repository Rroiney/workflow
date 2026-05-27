<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Leaves\StoreLeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\TenantUser;
use Illuminate\Validation\ValidationException;

class CreateLeaveRequestAction
{
    public function execute(StoreLeaveRequest $request, TenantUser $user): LeaveRequest
    {
        $days = now()->parse($request->input('from_date'))
            ->diffInDays(now()->parse($request->input('to_date'))) + 1;

        $balance = LeaveBalance::query()
            ->where('user_id', $user->id)
            ->where('leave_type_id', $request->integer('leave_type_id'))
            ->firstOrFail();

        if ($balance->balance < $days) {
            throw ValidationException::withMessages([
                'leave_type_id' => 'Insufficient leave balance',
            ]);
        }

        $leave = LeaveRequest::query()->create([
            'user_id' => $user->id,
            'leave_type_id' => $request->integer('leave_type_id'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'reason' => $request->input('reason'),
        ]);

        $leaveType = $leave->leaveType->name ?? 'leave';
        $from = now()->parse($leave->from_date)->format('d M Y');
        $to = now()->parse($leave->to_date)->format('d M Y');

        activity_log(
            'leave_applied',
            "has applied for {$leaveType} from {$from} to {$to}",
            $leave
        );

        return $leave;
    }
}
