<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $managedUser = $this->route('user');
        $managedUserId = $managedUser instanceof User ? $managedUser->id : $managedUser;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($managedUserId)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'string', Rule::in(User::roles())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
