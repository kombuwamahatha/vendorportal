<x-mail::message>
# Welcome to Earthy Ceylon!

Dear {{ $vendor->contact_person }},

We are delighted to inform you that your vendor application for **{{ $vendor->brand_name }}** has been approved. You are now part of the Earthy Ceylon family!

You can log in to the Vendor Portal using the credentials below:

**Login URL:** {{ config('app.url') }}/login
**Email:** {{ $vendor->email }}
**Temporary Password:** {{ $plainPassword }}

<x-mail::button :url="config('app.url') . '/login'" color="success">
Login to Vendor Portal
</x-mail::button>

**Important:** Please change your password immediately after your first login.

Once logged in you can start submitting your products for review. Our team will curate and publish approved products to the Earthy Ceylon store.

If you have any questions, please don't hesitate to contact us.

Warm regards,
The Earthy Ceylon Team

{{ config('app.name') }}
</x-mail::message>