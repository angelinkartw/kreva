@php
    $role = auth()->user()?->role;
    $label = match ($role) {
        'employee' => 'Karyawan',
        'admin' => 'Admin',
        default => 'Pemilik UMKM',
    };
@endphp
<p>{{ $label }}</p>
