<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StaticPagesController extends Controller
{
    public function contact()
    {
        return view('static_pages.contact');
    }

    public function about()
    {
        return view('static_pages.about');
    }

    public function terms()
    {
        return view('static_pages.terms');
    }

    public function privacy()
    {
        return view('static_pages.privacy');
    }

    public function faq()
    {
        return view('static_pages.faq');
    }

    public function services()
    {
        return view('static_pages.services');
    }

    /**
     * Handle the contact form submission.
     */
    public function store(ContactFormRequest $request)
    {
        try {
            $contactData = $request->validated();

            // Add timestamp and IP for tracking
            $contactData['submitted_at'] = now();
            $contactData['ip_address'] = $request->ip();

            // Send email to admin
            Mail::to(config('mail.admin_email', 'support@massar.biz'))
                ->send(new ContactFormMail($contactData));

            // Optional: Send confirmation email to user
            if (config('mail.send_contact_confirmation', true)) {
                Mail::to($contactData['email'])
                    ->send(new ContactConfirmationMail($contactData));
            }

            // Log the contact submission
            Log::info('Contact form submitted', [
                'email' => $contactData['email'],
                'subject' => $contactData['subject'],
                'ip' => $contactData['ip_address']
            ]);

            return back()->with('success', __('static_pages.Thank you for your message. We will get back to you soon!'));

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown'
            ]);

            return back()
                ->withInput()
                ->with('error', __('static_pages.Sorry, there was an error sending your message. Please try again.'));
        }
    }

    /**
     * Get subject options for the contact form.
     */
    public function getSubjectOptions()
    {
        return [
            'general' => __('static_pages.General Inquiry'),
            'technical' => __('static_pages.Technical Support'),
            'billing' => __('static_pages.Billing and Payment'),
            'partnership' => __('static_pages.Partnerships'),
            'other' => __('static_pages.Other'),
        ];
    }

    public function sitemap()
    {
        $path = public_path('sitemap.xml');

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
