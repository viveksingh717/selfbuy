<?php

namespace App\Http\Controllers;

use App\Mail\ContactAcknowledgementMail;
use App\Mail\ContactAdminNotificationMail;
use App\Models\AdminNotification;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ContactController extends Controller
{
    /**
     * Store a storefront "Contact Us" submission and fire the notification
     * emails (admin gets the message, the visitor gets an acknowledgement).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:190'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Honeypot — real users never fill this hidden field.
            'website' => ['nullable', 'size:0'],
        ], [
            'website.size' => 'Spam detected.',
        ]);

        $contact = ContactUs::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'subject'    => $validated['subject'] ?? null,
            'message'    => $validated['message'],
            'status'     => 'unread',
            'ip_address' => $request->ip(),
        ]);

        AdminNotification::record([
            'type'  => 'contact',
            'title' => 'New contact message from ' . $contact->name,
            'body'  => Str::limit(strip_tags($contact->message), 90),
            'url'   => route('admin.contact_us'),
            'icon'  => 'fa-envelope',
            'data'  => ['contact_id' => $contact->id],
        ]);

        $this->sendNotifications($contact);

        return back()
            ->with('contact_success', "Thanks {$contact->name}! Your message has been sent — we'll reply within 24 hours.")
            ->withFragment('contact-form');
    }

    /** Best-effort mail — a mail failure must not break the thank-you page. */
    private function sendNotifications(ContactUs $contact): void
    {
        $adminEmail = setting('contact_email') ?: config('mail.from.address');

        try {
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ContactAdminNotificationMail($contact));
            }

            Mail::to($contact->email)->send(new ContactAcknowledgementMail($contact));
        } catch (Throwable $e) {
            Log::error('Contact notification mail failed: ' . $e->getMessage(), [
                'contact_id' => $contact->id,
            ]);
        }
    }
}
