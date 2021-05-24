<?php


namespace App\Fields;


use App\Models\ModelField;
use Illuminate\Support\Str;

abstract class Field
{

    protected ModelField $modelField;

    public bool $isSearchable = false;

    /**
     * Field constructor.
     * @param ModelField $modelField
     */
    public function __construct(ModelField $modelField)
    {
        $this->modelField = $modelField;
    }

    protected function getLowerCaseModelName(): string
    {
        return Str::lower(Str::plural($this->modelField->model->name));
    }

    protected function getLowerCaseModelNameSingular(): string
    {
        return Str::lower(Str::singular($this->modelField->model->name));
    }


    protected function getValidation($validationName, $default = null) {
        foreach ($this->modelField->validations as $validation) {
            if($validation->name == $validation) {
                return $validation->value;
            }
        }

        return $default;
    }

    abstract public function getModalInput(): string;
    abstract public function getScripts(): string;
    abstract public function getTable(): string;
    abstract public function getMigration(): string;


}
