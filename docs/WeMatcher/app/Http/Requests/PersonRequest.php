<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
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
        // $date = strtotime(date("Y-m-d").' -18 year');
        // $datestr = date('m/d/Y', $date);

        return [
            'avatar' => 'required',
            'name' => 'required|max:255',
            // 'birthday' => 'required|date|date_format:m/d/Y|before:'.$datestr,
            'age' => 'required',
            'country' => 'required',
            'gender' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'birthday.before'  => 'You must be over 18+ years.',
        ];
    }
}
