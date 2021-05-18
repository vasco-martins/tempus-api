<?php


namespace App\Fields;


use App\Models\ModelField;

class FieldsController
{

    private ModelField $modelField;

    private array $fields = [
        'string' => StringField::class,
        'text' => StringField::class,
        'textarea' => TextareaField::class,
        'number' => NumberField::class,
    ];

    /**
     * FieldsController constructor.
     * @param ModelField $modelField
     */
    public function __construct(ModelField $modelField)
    {
        $this->modelField = $modelField;
    }

    public function getField(): Field {
        return new $this->fields[$this->modelField->type]($this->modelField);
    }

}
