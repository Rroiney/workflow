<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Leaves\UpdateLeaveStatusRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;

class UpdateLeaveStatusAction
{
    public function execute(UpdateLeaveStatusRequest $request, LeaveRequest $leave, int $approverId): LeaveRequest
    {
        if ($leave->status !== 'pending') {
            return $leave;
        }

        $status = $request->string('status')->toString();

        if ($status === 'approved') {
            $days = now()->parse($leave->from_date)
                ->diffInDays(now()->parse($leave->to_date)) + 1;

            $balance = LeaveBalance::query()
                ->where('user_id', $leave->user_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->firstOrFail();

            $balance->decrement('balance', $days);
        }

        $leave->update([
            'status' => $status,
            'approved_by' => $approverId,
        ]);

        $leaveType = $leave->leaveType->name ?? 'leave';
        $from = now()->parse($leave->from_date)->format('d M Y');
        $to = now()->parse($leave->to_date)->format('d M Y');

        if ($status === 'approved') {
            activity_log(
                'leave_approved',
                "has approved your {$leaveType} from {$from} to {$to}",
                $leave
            );
        } else {
            activity_log(
                'leave_rejected',
                "has rejected your {$leaveType} from {$from} to {$to}",
                $leave
            );
        }

        return $leave;
    }
}
