@extends('admin.layouts.app')

@section('title', 'Contact Us')
@section('subTitle', 'Messages')

@section('style')
    <style>
        .contact-avatar {
            display: inline-flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 50%;
            color: #fff; font-weight: 600; font-size: 15px; text-transform: uppercase;
        }
        #contactTable td { vertical-align: middle; }
        .contact-star { font-size: 15px; }
        .contact-summary .card { margin-bottom: 0; }
        .contact-summary .num { font-size: 22px; font-weight: 700; }
        .msg-box { background: #f7f8fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 14px 16px; white-space: pre-line; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            {{-- Summary --}}
            <div class="row contact-summary">
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted">Total Messages</div>
                        <div class="num">{{ $counts['total'] ?? 0 }}</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted">Unread</div>
                        <div class="num text-primary">{{ $counts['unread'] ?? 0 }}</div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted">Resolved (Done)</div>
                        <div class="num text-success">{{ $counts['done'] ?? 0 }}</div>
                    </div></div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Contact Messages</h3>
                    <div class="card-options">
                        <ul class="nav nav-pills contact-filter" data-toggle="tabs">
                            <li class="nav-item"><a class="nav-link active" href="javascript:void(0)" data-status="">All</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-status="unread">Unread</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-status="read">Read</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-status="done">Done</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @include('partials._message')

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="contactTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:30px"></th>
                                    <th style="width:50px"></th>
                                    <th>Sender</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Received</th>
                                    <th style="width:130px">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── View / Reply modal ─────────────────────────────────────── --}}
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="contact-avatar" id="cm_avatar" style="background:#5A67D8"></span>
                        <span class="ml-2" id="cm_name">—</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Email:</strong> <a href="#" id="cm_email"></a></div>
                        <div class="col-sm-6"><strong>Phone:</strong> <span id="cm_phone"></span></div>
                        <div class="col-sm-6 mt-1"><strong>Received:</strong> <span id="cm_received"></span></div>
                        <div class="col-sm-6 mt-1"><strong>Status:</strong> <span id="cm_status"></span></div>
                        <div class="col-12 mt-1" id="cm_subject_wrap"><strong>Subject:</strong> <span id="cm_subject"></span></div>
                    </div>

                    <label class="text-muted small mb-1">MESSAGE</label>
                    <div class="msg-box mb-3" id="cm_message"></div>

                    <div id="cm_prev_reply_wrap" class="mb-3" style="display:none">
                        <label class="text-muted small mb-1">PREVIOUS REPLY <span id="cm_replied_meta" class="text-muted"></span></label>
                        <div class="msg-box" id="cm_prev_reply"></div>
                    </div>

                    <form id="contactReplyForm">
                        <input type="hidden" name="id" id="cm_id">
                        <div class="form-group">
                            <label>Reply <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reply_message" id="reply_message" rows="5"
                                placeholder="Type your reply — it will be emailed to the sender."></textarea>
                            <div id="reply_message_err"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="cm_send">
                            <i class="fa fa-paper-plane"></i> Send Reply
                        </button>
                        <button type="button" class="btn btn-outline-success contact-status" data-status="done">
                            <i class="fa fa-check"></i> Mark Done
                        </button>
                        <button type="button" class="btn btn-outline-secondary contact-status" data-status="unread">
                            <i class="fa fa-undo"></i> Mark Unread
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
@endsection
