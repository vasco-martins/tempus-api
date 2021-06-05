<?php


namespace App\Fields;


use App\Helpers\FieldType;
use App\Models\ModelField;

class FieldsController
{

    private ModelField $modelField;

    private array $fields = [
        FieldType::STRING => StringField::class,
        FieldType::TEXT => StringField::class,
        FieldType::TEXTAREA => TextareaField::class,
        FieldType::NUMBER => NumberField::class,
        FieldType::PASSWORD => PasswordField::class,
        FieldType::EMAIL => EmailField::class,
        FieldType::SELECT => SelectField::class,
        FieldType::BELONGS_TO => BelongsToField::class,
    ];

    public array $searchable = [
        FieldType::STRING,
        FieldType::TEXT,
        FieldType::TEXTAREA,
        FieldType::NUMBER,
        FieldType::EMAIL,
        FieldType::SELECT,
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
