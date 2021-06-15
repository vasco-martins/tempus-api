<?php


namespace App\Fields;


use App\Models\Log;
use App\Models\ModelField;
use App\Models\ProjectModel;
use Illuminate\Support\Str;

class BelongsToManyField extends Field
{

    public bool $isSearchable = false;

    public function getModalInput(): string
    {
        $valuesField = ProjectModel::find($this->getValidation('crud'));
        $field = ModelField::find($this->getValidation('field'));

        $name = Str::endsWith($this->modelField->database_name, '_id') ? $this->modelField->database_name : $this->modelField->database_name . '_id';



        return '
                  <div class="form-group" wire:ignore>
                            <label for="' . $name . '">Example select</label>
                            <select
                                class=""
                                id="' . $name . '"
                                name="' . $name . '[]"
                                multiple
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
        $name = Str::endsWith($this->modelField->database_name, '_id') ? $this->modelField->database_name : $this->modelField->database_name . '_id';

        return '
         new TomSelect(\'#' . $name . '\', {
             plugins: [\'change_listener\'],
            onChange: (value) => {
                @this.set(\'' . $name . '\', value);
            }
        });';    }

    public function getTable(): string
    {
        $lowerCaseModelName = $this->getLowerCaseModelNameSingular();
        $relation = ProjectModel::find($this->getValidation('crud'));

        $field = ModelField::find($this->getValidation('field'));

        return "{{ $" . $lowerCaseModelName . "Item->" . Str::camel(Str::plural($relation->label)) .  "->pluck('" . $field->database_name . "')->implode(', ') ?? '' }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';

        $relation = ProjectModel::find($this->getValidation('crud'));

        $name = Str::endsWith($this->modelField->database_name, '_id') ? $this->modelField->database_name : $this->modelField->database_name . '_id';
    //    $ownName = Str::endsWith($this->modelField->database_name, '_id') ? $this->modelField->database_name : $this->modelField->database_name . '_id';

        if($relation == null) {
            return '';
        }

        $str = '$table->foreignId(\'' . $name . '\')' . $isRequired . '->constrained(\'' . $relation->database_name . '\')->onDelete(\'cascade\');';
        $str .= '$table->foreignId(\'' . $name . '\')' . $isRequired . '->constrained(\'' . $relation->database_name . '\')->onDelete(\'cascade\');';

        return $str;
    }
}
