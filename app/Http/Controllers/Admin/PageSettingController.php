<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use App\Services\PageSettingService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PageSettingController extends Controller
{
    public function __construct()
    {
        $this->pageService = new PageSettingService();
    }

    /** One list for every page. Table shell on a normal request, JSON on ajax. */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.pages.page');
        }

        $pages = PageSetting::select(['id', 'name', 'slug', 'status', 'updated_at']);

        return DataTables::of($pages)

            ->editColumn('name', function ($row) {
                return e($row->name ?: $row->slug);
            })

            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';

                return '
                    <label class="custom-switch">
                        <input
                            type="checkbox"
                            class="custom-switch-input toggle-status"
                            data-id="' . $row->id . '"
                            data-url="/admin/page_status"
                            data-table="#pageTable"
                            ' . $checked . '
                        >
                        <span class="custom-switch-indicator"></span>
                    </label>
                ';
            })

            ->editColumn('updated_at', function ($row) {
                return Carbon::parse($row->updated_at)->format('d M Y, h:i A');
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.edit_page', $row->id) . '"
                        class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-danger delete-page"
                        data-id="' . $row->id . '">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            })

            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /** The common edit form - same blade for every page. */
    public function edit_page($id)
    {
        $page = $this->pageService->get_page_by_id($id);

        if (!$page) {
            return redirect()->route('admin.pages')->with('error', 'Page not found.');
        }

        return view('admin.pages.page_edit', compact('page'));
    }

    public function update_page(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            $request->session()->flash('error', 'Please login to continue.');
            return redirect()->route('admin.login');
        }

        // Everything is optional except the slug (it's the front-end lookup key).
        $validator = Validator::make($request->all(), [
            'slug'             => 'required|string|max:150|unique:page_settings,slug,' . $id,
            'name'             => 'nullable|string|max:150',
            'label'            => 'nullable|string|max:150',
            'title'            => 'nullable|string|max:200',
            'short_description' => 'nullable|string',
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'status'           => 'required|in:0,1',
            'file'             => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp',
            'remove_file'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $payload = $request->only([
            'slug', 'name', 'label', 'title', 'short_description', 'description',
            'content', 'meta_title', 'meta_description', 'meta_keywords', 'status', 'remove_file',
        ]);

        if ($request->hasFile('file')) {
            $payload['file'] = $request->file('file');
        }

        $result = $this->pageService->update_page($id, $payload);

        if ($result['status']) {
            $request->session()->flash('success', $result['message']);
            return $rs->setSuccessResponse($result['message'], $result['data']);
        }

        $request->session()->flash('error', $result['message']);
        return $rs->setErrorResponse($result['message']);
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $result = $this->pageService->toggle_status($request->status, $id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['status' => $request->status]);
        }

        return $rs->setErrorResponse($result['message']);
    }

    public function delete_page(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $result = $this->pageService->delete_page($id);

        if ($result['status']) {
            $request->session()->flash('success', $result['message']);
            return $rs->setSuccessResponse($result['message'], ['id' => $id]);
        }

        return $rs->setErrorResponse($result['message']);
    }
}
