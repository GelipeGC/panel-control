<?php

namespace App\Http\Requests;

use App\Role;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => '',
            'bio'   =>'required',
            'twitter' => ['nullable','url'],
            'role' => ['required', Rule::in(Role::getList())],
            'profession_id' => [
                'nullable', 'present',
                Rule::exists('professions', 'id')->whereNull('deleted_at')
            ],
            'skills' => [
                'array',
                Rule::exists('skills', 'id'),
            ]
        ];
    }

    public function updateUser($user)
    {
        $user->fill([
            'name' => $this->name,
            'email' => $this->email
        ]);

        if ($this->password != null) {
            $user->password = bcrypt($this->password);
        }
        $user->role = $this->role;
        $user->save();

        $user->profile->update([
            'bio' => $this->bio,
            'twitter' => $this->twitter,
            'profession_id' => $this->profession_id
        ]);

        $user->skills()->sync($this->skills ?: []);
        
    }
}
