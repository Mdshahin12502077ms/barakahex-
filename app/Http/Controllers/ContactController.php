<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Reminder;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Contact;
use App\Traits\SendMailTrait;

class ContactController extends Controller
{
    use SendMailTrait;
    public function contact(Request $request)
    {

        try {
            $contact = new Contact;
            $contact = [
                'subject' => __('contact_mail'),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
            ];

            $this->sendMail(setting('email_address'), 'website.contact.contact-email', $contact, $contact['email']);
            return response()->json(['message' => 'Form submitted successfully'], 200);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

}
