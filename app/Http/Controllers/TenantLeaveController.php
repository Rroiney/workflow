<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantLeaveController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tenant')->user();

        $balances = null;
        $myLeaves = null;
        $pendingLeaves = null;

        // Employee + Manager: balances + own leaves
        if (in_array($user->role, ['employee', 'manager'])) {
            $balances = LeaveBalance::with('leaveType')
                ->where('user_id', $user->id)
                ->get();

            $myLeaves = LeaveRequest::with('leaveType')
                ->where('user_id', $user->id)
                ->get();
        }

        // Manager + Admin: approve others
        if (in_array($user->role, ['manager', 'admin'])) {
            $pendingLeaves = LeaveRequest::with(['user', 'leaveType'])
                ->where('user_id', '!=', $user->id)
                ->where('status', 'pending')
                ->get();
        }

        return view('tenant.leaves.index', compact(
            'balances',
            'myLeaves',
            'pendingLeaves'
        ));
    }

    public function create()
    {
        $leaveTypes = LeaveType::all();
        return view('tenant.leaves.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        // Validate first
        $request->validate([
            'leave_type_id' => 'required|exists:tenant.leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // Calculate days
        $days = now()->parse($request->from_date)
            ->diffInDays(now()->parse($request->to_date)) + 1;

        // Fetch user-specific balance
        $balance = LeaveBalance::where('user_id', Auth::guard('tenant')->id())
            ->where('leave_type_id', $request->leave_type_id)
            ->firstOrFail();

        // Check balance
        if ($balance->balance < $days) {
            return back()->withErrors(['Insufficient leave balance']);
        }

        // Create leave request
        $leave = LeaveRequest::create([
            'user_id' => Auth::guard('tenant')->id(),
            'leave_type_id' => $request->leave_type_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
        ]);

        // Activity log (context-rich)
        $leaveType = $leave->leaveType->name ?? 'leave';
        $from = now()->parse($leave->from_date)->format('d M Y');
        $to = now()->parse($leave->to_date)->format('d M Y');

        activity_log(
            'leave_applied',
            "has applied for {$leaveType} from {$from} to {$to}",
            $leave
        );

        return redirect()
            ->route('leaves.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Leave applied successfully.');
    }


    public function updateStatus($tenant, $leaveId, Request $request)
    {
        $leave = LeaveRequest::findOrFail($leaveId);

        // Prevent double approval/rejection
        if ($leave->status !== 'pending') {
            return back();
        }

        if ($request->status === 'approved') {
            $days = now()->parse($leave->from_date)
                ->diffInDays(now()->parse($leave->to_date)) + 1;

            $balance = LeaveBalance::where('user_id', $leave->user_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->firstOrFail();

            $balance->decrement('balance', $days);
        }

        $leave->update([
            'status' => $request->status,
            'approved_by' => Auth::guard('tenant')->id(),
        ]);

        $leaveType = $leave->leaveType->name ?? 'leave';
        $from = now()->parse($leave->from_date)->format('d M Y');
        $to = now()->parse($leave->to_date)->format('d M Y');

        if ($request->status === 'approved') {
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


        return back()->with(
            'success',
            $request->status === 'approved'
                ? 'Leave request approved.'
                : 'Leave request rejected.'
        );
    }
}
