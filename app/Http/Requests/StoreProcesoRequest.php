<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProcesoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'objeto'       => ['required', 'string', 'max:500'],
            'actividad'    => ['nullable', 'string', 'max:255'],
            'descripcion'  => ['required', 'string', 'max:2000'],
            'moneda'       => ['required', 'string', 'in:COP,USD,EUR'],
            'presupuesto'  => ['required', 'numeric', 'min:0', 'max:999999999'],
            'fecha_inicio' => ['required', 'date', 'before_or_equal:fecha_cierre'],
            'hora_inicio'  => ['required', 'date_format:H:i'],
            'fecha_cierre'   => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'hora_cierre'    => ['required', 'date_format:H:i'],
            'estado'         => ['nullable', 'string', 'in:Borrador,Publicado,En evaluación,Adjudicado,Cancelado'],
            'responsable_id' => ['nullable', 'integer', 'exists:responsables,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'objeto.required'               => 'El objeto es obligatorio.',
            'descripcion.required'          => 'La descripción es obligatoria.',
            'moneda.required'               => 'Seleccioná una moneda.',
            'moneda.in'                     => 'Moneda inválida. Las opciones son COP, USD o EUR.',
            'presupuesto.required'          => 'El presupuesto es obligatorio.',
            'presupuesto.numeric'           => 'El presupuesto debe ser un número.',
            'presupuesto.min'               => 'El presupuesto no puede ser negativo.',
            'fecha_inicio.required'         => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.before_or_equal'  => 'La fecha de inicio debe ser anterior o igual a la fecha de cierre.',
            'fecha_cierre.required'         => 'La fecha de cierre es obligatoria.',
            'fecha_cierre.after_or_equal'   => 'La fecha de cierre debe ser posterior o igual a la fecha de inicio.',
            'hora_inicio.required'          => 'La hora de inicio es obligatoria.',
            'hora_cierre.required'          => 'La hora de cierre es obligatoria.',
        ];
    }
}
