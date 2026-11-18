<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Services\AuditLogger;
use App\Support\Html;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->load('store:id,user_id,name,slug,verification_status');
    }

    public function update(Request $request)
    {
        $request->merge(['phone' => Html::phone($request->input('phone'))]);
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'regex:/^628[0-9]{8,12}$/', 'unique:users,phone,'.$user->id],
        ]);

        // Ganti nomor telepon -> wajib verifikasi ulang
        if (isset($data['phone']) && $data['phone'] !== $user->phone) {
            $user->phone_verified_at = null;
            AuditLogger::log('user.phone_changed', $user);
        }
        $user->fill($data)->save();

        return $user;
    }

    /** Hak penghapusan data (UU PDP): anonimkan & nonaktifkan akun. */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $request->validate(['password' => ['required', 'string']]);
        if (! \Illuminate\Support\Facades\Hash::check($request->input('password'), (string) $user->password)) {
            abort(422, 'Kata sandi salah.');
        }

        $user->tokens()->delete();
        $user->forceFill([
            'name' => 'Pengguna Terhapus', 'email' => 'deleted+'.$user->id.'@kreva.invalid', 'phone' => null,
            'password' => null, 'status' => 'suspended',
        ])->save();
        $user->addresses()->delete();
        $user->delete();
        AuditLogger::log('user.deleted', null, ['user_id' => $user->id], $user->id);

        return response()->noContent();
    }

    public function notifications(Request $request)
    {
        return UserNotification::where('user_id', $request->user()->id)->latest('id')->cursorPaginate(30);
    }

    public function readNotification(Request $request, int $id)
    {
        UserNotification::where('user_id', $request->user()->id)->whereKey($id)->update(['read_at' => now()]);

        return response()->noContent();
    }
}
