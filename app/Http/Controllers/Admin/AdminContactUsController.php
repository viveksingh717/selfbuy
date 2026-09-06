<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\ContactUs;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AdminContactUsController extends Controller
{
    /** List shell on a normal request, DataTables JSON on ajax. */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $counts = [
                'total'  => ContactUs::count(),
                'unread' => ContactUs::where('status', 'unread')->count(),
                'done'   => ContactUs::where('status', 'done')->count(),
            ];

            return view('admin.pages.contact_us', compact('counts'));
        }

        $messages = ContactUs::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->select(['id', 'name', 'email', 'phone', 'subject', 'message', 'status', 'is_starred', 'replied_at', 'created_at']);

        return DataTables::of($messages)

            ->addColumn('star', function ($row) {
                $on = $row->is_starred ? 'text-warning' : 'text-muted';

                return '<a href="javascript:void(0)" class="contact-star ' . $on . '" data-id="' . $row->id . '">
                            <i class="fa fa-star"></i>
                        </a>';
            })

            ->addColumn('avatar', function ($row) {
                return '<span class="contact-avatar" style="background:' . $row->avatar_color . '">'
                    . e($row->initial) . '</span>';
            })

            ->addColumn('sender', function ($row) {
                $name = e($row->name);

                if ($row->status === 'unread') {
                    $name = '<strong>' . $name . '</strong>';
                }

                $phone = $row->phone
                    ? '<div class="text-muted small"><i class="fa fa-phone"></i> ' . e($row->phone) . '</div>'
                    : '';

                return '<div>' . $name . '</div>' . $phone;
            })

            ->editColumn('email', function ($row) {
                return '<a href="mailto:' . e($row->email) . '">' . e($row->email) . '</a>';
            })

            ->addColumn('preview', function ($row) {
                $subject = $row->subject ? '<div>' . e($row->subject) . '</div>' : '';

                return $subject . '<div class="text-muted small">' . e(Str::limit(strip_tags($row->message), 70)) . '</div>';
            })

            ->editColumn('status', function ($row) {
                $map = [
                    'unread' => '<span class="badge badge-primary">Unread</span>',
                    'read'   => '<span class="badge badge-secondary">Read</span>',
                    'done'   => '<span class="badge badge-success">Done</span>',
                ];

                $replied = $row->replied_at
                    ? ' <span class="badge badge-light" title="Replied ' . $row->replied_at->format('d M Y') . '"><i class="fa fa-reply"></i></span>'
                    : '';

                return ($map[$row->status] ?? $row->status) . $replied;
            })

            ->editColumn('created_at', function ($row) {
                return '<span title="' . $row->created_at->format('d M Y, h:i A') . '">'
                    . $row->created_at->diffForHumans() . '</span>';
            })

            ->addColumn('action', function ($row) {
                $doneBtn = $row->status === 'done'
                    ? '<button class="btn btn-sm btn-outline-secondary contact-status" data-id="' . $row->id . '" data-status="unread" title="Mark as unread"><i class="fa fa-undo"></i></button>'
                    : '<button class="btn btn-sm btn-outline-success contact-status" data-id="' . $row->id . '" data-status="done" title="Mark as done"><i class="fa fa-check"></i></button>';

                return '
                    <button class="btn btn-sm btn-primary contact-view" data-id="' . $row->id . '" title="View / Reply">
                        <i class="fa fa-envelope-open"></i>
                    </button>
                    ' . $doneBtn . '
                    <button class="btn btn-sm btn-danger contact-delete" data-id="' . $row->id . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            })

            ->rawColumns(['star', 'avatar', 'sender', 'email', 'preview', 'status', 'created_at', 'action'])
            ->make(true);
    }

    /** Full message payload for the view/reply modal. Opening marks it read. */
    public function show($id, ResponseService $rs)
    {
        $message = ContactUs::find($id);

        if (!$message) {
            return $rs->setNotFoundResponse('Message not found.');
        }

        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return $rs->setSuccessResponse('OK', [
            'id'          => $message->id,
            'name'        => $message->name,
            'email'       => $message->email,
            'phone'       => $message->phone,
            'subject'     => $message->subject,
            'message'     => $message->message,
            'status'      => $message->status,
            'is_starred'  => $message->is_starred,
            'admin_reply' => $message->admin_reply,
            'replied_at'  => $message->replied_at?->format('d M Y, h:i A'),
            'replied_by'  => $message->replied_by,
            'created_at'  => $message->created_at->format('d M Y, h:i A'),
            'initial'     => $message->initial,
            'avatar_color' => $message->avatar_color,
        ]);
    }

    /** Send an email reply and mark the thread done. */
    public function reply(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $message = ContactUs::find($id);

        if (!$message) {
            return $rs->setNotFoundResponse('Message not found.');
        }

        $validator = Validator::make($request->all(), [
            'reply_message' => 'required|string|min:2|max:5000',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $body = $request->input('reply_message');

        try {
            Mail::to($message->email)->send(new ContactReplyMail($message, $body));
        } catch (\Throwable $e) {
            Log::error('Contact reply mail failed: ' . $e->getMessage());
            return $rs->setErrorResponse('Could not send the email. Check the mail configuration and try again.');
        }

        $message->update([
            'admin_reply' => $body,
            'replied_at'  => now(),
            'replied_by'  => Auth::guard('admin')->user()->email ?? 'admin',
            'status'      => 'done',
        ]);

        return $rs->setSuccessResponse('Reply sent successfully.', ['id' => $message->id]);
    }

    /** unread | read | done */
    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:unread,read,done',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $message = ContactUs::find($id);

        if (!$message) {
            return $rs->setNotFoundResponse('Message not found.');
        }

        $message->update(['status' => $request->status]);

        return $rs->setSuccessResponse('Status updated.', ['status' => $request->status]);
    }

    public function toggle_star($id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $message = ContactUs::find($id);

        if (!$message) {
            return $rs->setNotFoundResponse('Message not found.');
        }

        $message->update(['is_starred' => !$message->is_starred]);

        return $rs->setSuccessResponse('Updated.', ['is_starred' => $message->is_starred]);
    }

    public function destroy($id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $message = ContactUs::find($id);

        if (!$message) {
            return $rs->setNotFoundResponse('Message not found.');
        }

        $message->delete();

        return $rs->setSuccessResponse('Message deleted.', ['id' => $id]);
    }

    /** Static help / usage guide for the admin panel. */
    public function help()
    {
        return view('admin.pages.help');
    }
}
