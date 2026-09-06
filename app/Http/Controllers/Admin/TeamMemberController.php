<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\FileUploadService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TeamMemberController extends Controller
{
    private FileUploadService $files;

    public function __construct()
    {
        $this->files = new FileUploadService();
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.team.team');
        }

        $members = TeamMember::select([
            'id', 'name', 'designation', 'photo', 'sort_order', 'status', 'created_at',
        ]);

        return DataTables::of($members)

            ->addColumn('photo', function ($row) {
                return '<img src="' . e($row->photo_url) . '" alt="" '
                    . 'style="width:42px;height:42px;border-radius:50%;object-fit:cover;">';
            })

            ->editColumn('designation', fn ($row) => e($row->designation ?: '—'))

            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';

                return '
                    <label class="custom-switch">
                        <input type="checkbox" class="custom-switch-input toggle-status"
                            data-id="' . $row->id . '"
                            data-url="/admin/team_member_status"
                            data-table="#teamTable" ' . $checked . '>
                        <span class="custom-switch-indicator"></span>
                    </label>';
            })

            ->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->format('d M Y'))

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.edit_team_member', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger delete-team-member" data-id="' . $row->id . '">
                        <i class="fa fa-trash"></i>
                    </button>';
            })

            ->rawColumns(['photo', 'status', 'action'])
            ->make(true);
    }

    public function create_member()
    {
        return view('admin.team.create_team_member');
    }

    public function process_member(Request $request, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $data = $this->payload($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->files->uploadImage($request->file('photo'), 'team');
        }

        $member = TeamMember::create($data);

        $request->session()->flash('success', 'Team member added successfully.');
        return $rs->setCreatedResponse('Team member added successfully.', $member);
    }

    public function edit_member($id)
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return redirect()->route('admin.team_members')->with('error', 'Team member not found.');
        }

        return view('admin.team.edit_team_member', compact('member'));
    }

    public function update_member(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $member = TeamMember::find($id);

        if (!$member) {
            return $rs->setNotFoundResponse('Team member not found.');
        }

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $data = $this->payload($request);

        if ($request->hasFile('photo')) {
            $this->files->deleteImage($member->photo, 'team');
            $data['photo'] = $this->files->uploadImage($request->file('photo'), 'team');
        }

        $member->update($data);

        $request->session()->flash('success', 'Team member updated successfully.');
        return $rs->setSuccessResponse('Team member updated successfully.', $member);
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $member = TeamMember::find($id);

        if (!$member) {
            return $rs->setNotFoundResponse('Team member not found.');
        }

        $member->update(['status' => (int) $request->status]);

        return $rs->setSuccessResponse('Status updated.', ['status' => $member->status]);
    }

    public function delete_member(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $member = TeamMember::find($id);

        if (!$member) {
            return $rs->setNotFoundResponse('Team member not found.');
        }

        $member->delete(); // soft delete - the photo file is kept

        return $rs->setSuccessResponse('Team member deleted.', ['id' => $id]);
    }

    private function rules(): array
    {
        return [
            'name'          => 'required|string|max:150',
            'designation'   => 'nullable|string|max:150',
            'bio'           => 'nullable|string|max:2000',
            'email'         => 'nullable|email|max:190',
            'phone'         => 'nullable|string|max:30',
            'facebook_url'  => 'nullable|url|max:255',
            'twitter_url'   => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url'  => 'nullable|url|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'status'        => 'required|in:0,1',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
    }

    private function payload(Request $request): array
    {
        return [
            'name'          => $request->name,
            'designation'   => $request->designation,
            'bio'           => $request->bio,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'facebook_url'  => $request->facebook_url,
            'twitter_url'   => $request->twitter_url,
            'instagram_url' => $request->instagram_url,
            'linkedin_url'  => $request->linkedin_url,
            'sort_order'    => (int) ($request->sort_order ?? 0),
            'status'        => (int) $request->status,
        ];
    }
}
