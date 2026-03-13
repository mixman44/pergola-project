<?php

namespace App\Http\Requests\Pergola;

use Illuminate\Foundation\Http\FormRequest;

class DescribePergolaRequest extends FormRequest
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
        return [
            'mode'         => 'required|in:custom',
            'second_image' => 'required|image',
        ];
    }

    /**
     * Messages d'erreur
     */
    public function messages(): array
    {
        return [
            'mode.required'        => 'Le mode est obligatoire.',
            'mode.in'              => 'Seul le mode custom est accepté ici.',
            'second_image.required'=> 'Une image de pergola est obligatoire.',
            'second_image.image'   => 'Le fichier doit être une image valide.',
        ];
    }
}
