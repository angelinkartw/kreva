<?php

return [

    'required' => ':attribute wajib diisi.',

    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],

    'min' => [
        'string' => ':attribute minimal harus :min karakter.',
    ],

    'unique' => ':attribute sudah terdaftar.',

    'confirmed' => 'Konfirmasi :attribute tidak cocok.',

    'attributes' => [
        'nama_user' => 'Username',
        'password' => 'Kata sandi',
        'password_confirmation' => 'Konfirmasi kata sandi',
    ],

];
