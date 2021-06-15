<?php


namespace App\Fields;


use App\Models\Log;
use App\Models\ModelField;
use App\Models\ProjectModel;
use Illuminate\Support\Str;

class BelongsToField extends Field
{

    public bool $isSearchable = false;

    public function getModalInput(): string
    {
        $valuesField = ProjectModel::find($this->getValidation('crud'));
        $field = ModelField::find($this->getValidation('field'));




        return '
                  <div class="form-group" wire:ignore>
                            <label for="' . $this->modelField->database_name . '">Example select</label>
                            <select
                                class=""
                                id="' . $this->modelField->database_name . '"
                            >
                            <option selected>Selecione uma opção</option>
                            @foreach(App\Models\\' . $valuesField->name . '::all() as $child)
                                <option value="{{ $child->id }}">{{ $child->' . $field->database_name .' }}</option>
                            @endforeach

                            </select>
                </div>';

    }


    public function getScripts(): string
    {

        return '
         new TomSelect(\'#' . $this->modelField->database_name . '\', {
             plugins: [\'change_listener\'],
            onChange: (value) => {
                @this.set(\'' . $this->modelField->database_name . '\', value);
            }
        });';    }

    public function getTable(): string
    {
        $lowerCaseModelName = $this->getLowerCaseModelNameSingular();
        $relation = ProjectModel::find($this->getValidation('crud'));

        $field = ModelField::find($this->getValidation('field'));

        return "{{ $" . $lowerCaseModelName . "Item->" . Str::camel($relation->label) .  "->" . $field->database_name . " ?? '' }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';

        $relation = ProjectModel::find($this->getValidation('crud'));


        if($relation == null) {
            return '';
        }

        return '$table->foreignId(\'' . $this->modelField->database_name . '\')' . $isRequired . '->constrained(\'' . $relation->database_name . '\')->onDelete(\'cascade\');';
    }
}
