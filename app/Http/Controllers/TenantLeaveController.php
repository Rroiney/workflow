<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\CreateLeaveRequestAction;
use App\Actions\Tenant\UpdateLeaveStatusAction;
use App\Http\Requests\Tenant\Leaves\StoreLeaveRequest;
use App\Http\Requests\Tenant\Leaves\UpdateLeaveStatusRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Repositories\Tenant\LeaveRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantLeaveController extends Controller
{
    public function index(LeaveRepository $leaveRepository): View
    {
        $user = Auth::guard('tenant')->user();
        $this->authorize('viewAny', LeaveRequest::class);

        return view('tenant.leaves.index', $leaveRepository->getIndexData($user));
    }

    public function create(): View
    {
        $this->authorize('create', LeaveRequest::class);

        $leaveTypes = LeaveType::all();

        return view('tenant.leaves.create', compact('leaveTypes'));
    }

    public function store(
        StoreLeaveRequest $request,
        CreateLeaveRequestAction $createLeaveRequestAction
    )
    {
        $this->authorize('create', LeaveRequest::class);
        $createLeaveRequestAction->execute($request, Auth::guard('tenant')->user());

        return redirect()
            ->route('leaves.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Leave applied successfully.');
    }


    public function updateStatus(
        string $tenant,
        LeaveRequest $leave,
        UpdateLeaveStatusRequest $request,
        UpdateLeaveStatusAction $updateLeaveStatusAction
    )
    {
        $this->authorize('updateStatus', $leave);
        $updateLeaveStatusAction->execute($request, $leave, Auth::guard('tenant')->id());

        return back()->with(
            'success',
            $request->status === 'approved'
                ? 'Leave request approved.'
                : 'Leave request rejected.'
        );
    }
}
