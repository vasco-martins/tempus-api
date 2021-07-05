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
                            <label for="' . $name . '">' . $this->modelField->label . '</label>
                            <select
                                class=""
                                id="' . $name . '"
                                name="' . $name . '[]"
                                multiple
                            >
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
        //$this->getValidation('required') == null|false ?
        $isRequired = '->nullable()';

        $relation = ProjectModel::find($this->getValidation('crud'));

        $name = $this->modelField->database_name;
        $ownName = $relation->database_name;

        if($relation == null) {
            return '';
        }

        // Relation field
        $str = '$table->foreignId(\'' . Str::singular($this->modelField->projectModel->database_name) . '_id\')' . $isRequired . '->constrained(\'' . $this->modelField->projectModel->database_name . '\')->onDelete(\'cascade\');';
        // Current field
        $str .= "\n\t\t\t" . '$table->foreignId(\'' . $ownName   . '_id\')' . $isRequired . '->constrained(\'' . $ownName . '\')->onDelete(\'cascade\');';

        return $str;
    }
}
