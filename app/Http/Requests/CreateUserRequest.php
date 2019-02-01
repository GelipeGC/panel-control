<?php

namespace App\Http\Requests;

use App\{Role,User};
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required',
            'bio'   =>'required',
            'twitter' => ['nullable','url'],
            'role' => ['nullable', Rule::in(Role::getList())],
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

    public function messages()
    {
       return [
            'name.required' => 'El campo nombre es obligatorio'
        ];
    }

    public function createUser()
    {
        DB::transaction(function () {

            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => $this->role ?? 'user'
            ]);

            $user->profile()->create([
                'bio'   => $this->bio,
                'twitter' => $this->twitter ?? null,
                'profession_id' => $this->profession_id,
            ]);
            if (! empty ($this->skills)) {            
                $user->skills()->attach($this->skills ?? []);
            }
        });
    }
}
