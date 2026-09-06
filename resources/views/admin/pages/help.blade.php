@extends('admin.layouts.app')

@section('title', 'Need Help?')
@section('subTitle', 'Help & Guide')

@section('style')
    <style>
        .help-hero { background: linear-gradient(135deg, #4e73df, #224abe); color: #fff; border-radius: 8px; }
        .help-hero .card-body { padding: 32px; }
        .help-icon { width: 44px; height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; background: #eef2ff; color: #4e73df; }
        .help-list li { margin-bottom: 8px; }
        .help-kbd { background: #f4f6fa; border: 1px solid #e3e6ef; border-bottom-width: 2px; border-radius: 4px; padding: 1px 6px; font-size: 12px; }
    </style>
@endsection

@section('content')

    <div class="section-body mt-3">
        <div class="container-fluid">

            {{-- Hero --}}
            <div class="card help-hero">
                <div class="card-body">
                    <h2 class="mb-1">Need Help?</h2>
                    <p class="mb-0" style="opacity:.9">
                        A quick guide to managing your SelfBuy store from this admin panel.
                        Can't find an answer here? Reach the support team using the details below.
                    </p>
                </div>
            </div>

            {{-- Quick topics --}}
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="help-icon mb-2"><i class="fa fa-cubes"></i></span>
                            <h5>Products &amp; Catalog</h5>
                            <ul class="help-list pl-3 mb-0 text-muted">
                                <li>Create categories &amp; sub&#8209;categories first, then add products under them.</li>
                                <li>Use the <strong>Status</strong> switch to show / hide an item on the storefront.</li>
                                <li>Brands, colors, sizes and taxes are managed from their own menu items.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="help-icon mb-2"><i class="fa fa-file-text-o"></i></span>
                            <h5>Pages</h5>
                            <ul class="help-list pl-3 mb-0 text-muted">
                                <li>Edit the content of About Us, FAQ, Contact, Policy pages, etc.</li>
                                <li>Each page has a <strong>slug</strong> the storefront uses to load it.</li>
                                <li>Optional file upload for pages that need a downloadable PDF.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="help-icon mb-2"><i class="fa fa-cog"></i></span>
                            <h5>System Settings</h5>
                            <ul class="help-list pl-3 mb-0 text-muted">
                                <li>Logo, favicon, contact details, currency, language and footer text.</li>
                                <li>Changes apply site&#8209;wide as soon as you hit <strong>Save Settings</strong>.</li>
                                <li>Upload images stay until you replace or remove them.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Us messages --}}
            <div class="card mt-1">
                <div class="card-header"><h3 class="card-title">Managing Contact Us Messages</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="help-list pl-3 text-muted mb-0">
                                <li><strong>Unread / Read / Done</strong> &mdash; a message is marked <em>Read</em> automatically the first time you open it.</li>
                                <li>Click <span class="help-kbd">View / Reply</span> to read the full message and send an email reply. Sending a reply marks the thread <strong>Done</strong>.</li>
                                <li>Use the <span class="help-kbd"><i class="fa fa-check"></i></span> button to mark a message done without replying, or <span class="help-kbd"><i class="fa fa-undo"></i></span> to reopen it.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="help-list pl-3 text-muted mb-0">
                                <li>The <i class="fa fa-star text-warning"></i> star flags important messages for follow&#8209;up.</li>
                                <li>The filter pills (All / Unread / Read / Done) narrow the list.</li>
                                <li><span class="help-kbd"><i class="fa fa-trash"></i></span> is a soft delete &mdash; the message is hidden, not erased.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">Frequently Asked Questions</h3></div>
                <div class="card-body">
                    <div id="helpFaq" class="accordion">
                        <div class="card">
                            <div class="card-header" id="hf1">
                                <a role="button" data-toggle="collapse" href="#hfc1" aria-expanded="true">
                                    A product I added isn't showing on the website
                                </a>
                            </div>
                            <div id="hfc1" class="collapse show" data-parent="#helpFaq">
                                <div class="card-body text-muted">
                                    Check that the product's <strong>Status</strong> is Active, that it is assigned to a
                                    category which is itself Active, and that it has stock and at least one image.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="hf2">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#hfc2">
                                    How do I change the store logo or contact email?
                                </a>
                            </div>
                            <div id="hfc2" class="collapse" data-parent="#helpFaq">
                                <div class="card-body text-muted">
                                    Go to <strong>System Setting</strong>. The logo/favicon live under the
                                    <em>General</em> tab and the email/phone/address under <em>Contact</em>.
                                    Save and the storefront updates immediately.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="hf3">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#hfc3">
                                    A customer replied but didn't get my email
                                </a>
                            </div>
                            <div id="hfc3" class="collapse" data-parent="#helpFaq">
                                <div class="card-body text-muted">
                                    Replies are sent over the store's configured mail (SMTP) account. If sending fails
                                    you'll see an error instead of a success message &mdash; verify the mail credentials
                                    with your developer, then try again.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="hf4">
                                <a class="collapsed" role="button" data-toggle="collapse" href="#hfc4">
                                    I deleted something by mistake
                                </a>
                            </div>
                            <div id="hfc4" class="collapse" data-parent="#helpFaq">
                                <div class="card-body text-muted">
                                    Most deletes in the panel are <strong>soft deletes</strong> &mdash; the record is
                                    hidden but still in the database and can be restored by your developer.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Support --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">Still Stuck?</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <span class="help-icon mb-2"><i class="fa fa-envelope"></i></span>
                            <div class="text-muted">Email support</div>
                            <a href="mailto:{{ setting('support_email', config('mail.from.address')) }}">
                                {{ setting('support_email', config('mail.from.address')) }}
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <span class="help-icon mb-2"><i class="fa fa-phone"></i></span>
                            <div class="text-muted">Phone</div>
                            <span>{{ setting('contact_phone', 'Not set') }}</span>
                        </div>
                        <div class="col-md-4 mb-2">
                            <span class="help-icon mb-2"><i class="fa fa-clock-o"></i></span>
                            <div class="text-muted">Support hours</div>
                            <span>{{ setting('support_hours', 'Mon - Fri, 9:00 AM - 6:00 PM') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
@endsection
