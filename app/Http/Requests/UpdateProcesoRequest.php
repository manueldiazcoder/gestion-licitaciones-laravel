<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProcesoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'objeto'       => ['sometimes', 'required', 'string', 'max:500'],
            'actividad'    => ['nullable', 'string', 'max:255'],
            'descripcion'  => ['sometimes', 'required', 'string', 'max:2000'],
            'moneda'       => ['sometimes', 'required', 'string', 'in:COP,USD,EUR'],
            'presupuesto'  => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999999'],
            'fecha_inicio' => ['sometimes', 'required', 'date', 'before_or_equal:fecha_cierre'],
            'hora_inicio'  => ['sometimes', 'required', 'date_format:H:i'],
            'fecha_cierre'   => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'hora_cierre'    => ['sometimes', 'required', 'date_format:H:i'],
            'estado'         => ['nullable', 'string', 'in:borrador,activo,evaluacion,adjudicado,cancelado'],
            'responsable_id' => ['nullable', 'integer', 'exists:responsables,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'objeto.required'              => 'El objeto es obligatorio.',
            'descripcion.required'         => 'La descripción es obligatoria.',
            'moneda.in'                    => 'Moneda inválida.',
            'presupuesto.numeric'          => 'El presupuesto debe ser un número.',
            'presupuesto.min'              => 'El presupuesto no puede ser negativo.',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de cierre.',
            'fecha_cierre.after_or_equal'  => 'La fecha de cierre debe ser posterior o igual a la fecha de inicio.',
        ];
    }
}
