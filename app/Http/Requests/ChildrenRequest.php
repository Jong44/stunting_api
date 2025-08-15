<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChildrenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|max:255',
                'birth_date' => 'required|date',
                'gender' =>  'required|string|in:laki,perempuan',
                'parent_id' => 'required|uuid|exists:users,id',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => 'sometimes|required|string|max:255',
                'birth_date' => 'sometimes|required|date',
                'gender' => 'sometimes|required|string',
                'parent_id' => 'sometimes|required|uuid|exists:users,id',
            ];
        }

        return [];
    }
    public function messages()
    {
        return [
            'name.required' => 'Nama anak harus diisi.',
            'birth_date.required' => 'Tanggal lahir anak harus diisi.',
            'gender.required' => 'Jenis kelamin anak harus diisi.',
            'gender.in' => 'Jenis kelamin anak harus laki atau perempuan.',
            'parent_id.required' => 'ID orang tua harus diisi.',
            'parent_id.uuid' => 'ID orang tua harus berupa UUID.',
            'parent_id.exists' => 'ID orang tua tidak ditemukan.',
        ];
    }


}
