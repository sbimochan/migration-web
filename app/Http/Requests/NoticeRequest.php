<?php

namespace App\Http\Requests;


class NoticeRequest extends Request
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
            'metadata.title' => 'required',
            'screen.name'    => 'required',
        ];
    }
}
