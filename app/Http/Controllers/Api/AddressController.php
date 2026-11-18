<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Html;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private function rules(bool $partial = false): array
    {
        $s = $partial ? 'sometimes' : 'required';

        return [
            'label' => ['nullable', 'string', 'max:50'],
            'recipient' => [$s, 'string', 'max:100'],
            'phone' => [$s, 'regex:/^628[0-9]{8,12}$/'],
            'province' => [$s, 'string', 'max:80'], 'city' => [$s, 'string', 'max:80'], 'district' => [$s, 'string', 'max:80'],
            'postal_code' => [$s, 'digits:5'],
            'full_address' => [$s, 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function index(Request $request)
    {
        return $request->user()->addresses()->orderByDesc('is_default')->latest('id')->get();
    }

    public function store(Request $request)
    {
        $request->merge(['phone' => Html::phone($request->input('phone'))]);
        $data = $request->validate($this->rules());
        $user = $request->user();

        if (! $user->addresses()->exists()) {
            $data['is_default'] = true;
        }
        if (! empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        return response()->json($user->addresses()->create($data), 201);
    }

    public function update(Request $request, int $id)
    {
        $request->merge(['phone' => Html::phone($request->input('phone'))]);
        $address = $request->user()->addresses()->findOrFail($id); // cek kepemilikan (anti-IDOR)
        $data = $request->validate($this->rules(true));

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }
        $address->update($data);

        return $address;
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->addresses()->findOrFail($id)->delete();

        return response()->noContent();
    }
}
