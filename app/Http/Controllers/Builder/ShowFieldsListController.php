<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowFieldsListController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {

        return response()->json([

            [
                "type" => "string",
                "label" => "Texto curto",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "quill",
                        "label" => "Rich Text",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "min",
                        "label" => "Mínimo de carateres",
                        "type" => "number",
                        "value" => "0",
                        "required" => false
                    ],
                    [
                        "name" => "max",
                        "label" => "Máximo de carateres",
                        "type" => "number",
                        "value" => "0",
                        "max" => 255,
                        "required" => false
                    ],
                ],
            ],
            [
                "type" => "email",
                "label" => "Email",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "unique",
                        "label" => "Único na Tabela:",
                        "type" => "text",
                        "required" => false
                    ],
                ]
            ],
            [
                "type" => "text",
                "label" => "Texto",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false,
                    ],
                    [
                        "name" => "unique",
                        "label" => "Único",
                        "type" => "checkbox",
                        "required" => false,
                    ],
                    [
                        "name" => "min",
                        "label" => "Mínimo de carateres",
                        "type" => "number",
                        "value" => "0",
                        "required" => false
                    ],
                    [
                        "name" => "max",
                        "label" => "Máxima de carateres",
                        "type" => "number",
                        "value" => "0",
                        "required" => false
                    ],
                ],
            ],
            [
                "type" => "textarea",
                "label" => "Textarea",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "quill",
                        "label" => "Rich Text",
                        "type" => "checkbox",
                        "required" => false
                    ]
                ]
            ],
            [
                "type" => "password",
                "label" => "Password",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                ]
            ],
            [
                "type" => "number",
                "label" => "Número",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "unique",
                        "label" => "Único",
                        "type" => "checkbox",
                        "required" => false,
                    ],
                    [
                        "name" => "min",
                        "label" => "Mínimo de dígitos",
                        "type" => "number",
                        "value" => "0",
                        "required" => false
                    ],
                    [
                        "name" => "max",
                        "label" => "Máximo de dígitos",
                        "type" => "number",
                        "value" => "0",
                        "required" => false
                    ],
                ]
            ],
            [
                "type" => "select",
                "label" => "Select",
                "validations" => [
                    [
                        "name" => "required",
                        "label" => "Campo obrigatório",
                        "type" => "checkbox",
                        "required" => false
                    ],
                    [
                        "name" => "values",
                        "label" => "Valores",
                        "type" => "select",
                        "required" => false,
                    ],
                ]
            ],
        ]);
    }
}
