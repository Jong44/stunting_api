<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrowthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'child_id' => 'required|uuid|exists:children,id',
                'weight' => 'required|numeric|min:0',
                'height' => 'required|numeric|min:0',
                'head_circumference' => 'nullable|numeric|min:0',
                'date' => 'required|date',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'child_id' => 'sometimes|required|uuid|exists:children,id',
                'weight' => 'sometimes|required|numeric|min:0',
                'height' => 'sometimes|required|numeric|min:0',
                'head_circumference' => 'sometimes|nullable|numeric|min:0',
                'date' => 'sometimes|required|date',
            ];
        }
        return [];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'ID anak harus diisi.',
            'child_id.uuid' => 'ID anak harus berupa UUID.',
            'child_id.exists' => 'ID anak tidak ditemukan.',
            'weight.required' => 'Berat badan harus diisi.',
            'weight.numeric' => 'Berat badan harus berupa angka.',
            'weight.min' => 'Berat badan tidak boleh kurang dari 0.',
            'height.required' => 'Tinggi badan harus diisi.',
            'height.numeric' => 'Tinggi badan harus berupa angka.',
            'height.min' => 'Tinggi badan tidak boleh kurang dari 0.',
            'head_circumference.numeric' => 'Lingkar kepala harus berupa angka.',
            'head_circumference.min' => 'Lingkar kepala tidak boleh kurang dari 0.',
            'date.required' => 'Tanggal pengukuran harus diisi.',
            'date.date' => 'Format tanggal tidak valid.',
        ];
    }


}
