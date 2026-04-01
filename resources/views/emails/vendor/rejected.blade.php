<x-mail::message>
# Vendor Application Update

Dear {{ $vendor->contact_person }},

Thank you for your interest in becoming a vendor on Earthy Ceylon.

After reviewing your application for **{{ $vendor->brand_name }}**, we are unable to approve it at this time for the following reason:

> {{ $rejectionReason }}

If you believe this was made in error or would like to reapply after addressing the above, please contact us and we will be happy to assist.

Warm regards,
The Earthy Ceylon Team

{{ config('app.name') }}
</x-mail::message>