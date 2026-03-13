<?php

namespace App\Http\Requests\Pergola;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePergolaRequest extends FormRequest
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
            'model'        => 'required|string',
            'mode'         => 'required|in:default,custom,2image',
            'image'        => 'required|image',
            'second_image' => 'required_if:mode,2image,custom|image',
        ];
    }

    /**
     * Messages d'erreur
     */
    public function messages(): array
    {
        return [
            'model.required'        => 'Le modèle IA est obligatoire.',
            'mode.required'         => 'Le mode de génération est obligatoire.',
            'mode.in'               => 'Le mode doit être default, custom ou 2image.',
            'image.required'        => 'Une image de fond est obligatoire.',
            'image.image'           => 'Le fichier doit être une image valide.',
            'second_image.required_if' => 'Une image de pergola est requise pour ce mode.',
            'second_image.image'    => 'Le second fichier doit être une image valide.',
        ];
    }
}
