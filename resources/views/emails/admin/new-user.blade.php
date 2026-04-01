<x-mail::message>
# Welcome to Earthy Ceylon Vendor Portal

Dear {{ $adminUser->name }},

An admin account has been created for you on the Earthy Ceylon Vendor Portal with the role of **{{ ucfirst($role) }}**.

You can log in using the credentials below:

**Login URL:** {{ config('app.url') }}/admin/login
**Email:** {{ $adminUser->email }}
**Temporary Password:** {{ $plainPassword }}

<x-mail::button :url="config('app.url') . '/admin/login'">
Login to Admin Panel
</x-mail::button>

**Important:** Please change your password immediately after your first login.

Warm regards,
The Earthy Ceylon Team

{{ config('app.name') }}
</x-mail::message>