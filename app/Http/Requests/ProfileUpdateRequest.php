<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MongoDB\BSON\ObjectId;

class ProfileUpdateRequest extends BaseRequest
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
        $id = auth()->guard('api')->id();
        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id,_id|domain:ericsson.com",
            'new_password' => 'nullable|sometimes|confirmed|min:5',
            'old_password' => 'nullable|sometimes|required_with:new_password',
            'username' => "required|alpha|unique:users,username,$id,_id"
        ];
    }
}
