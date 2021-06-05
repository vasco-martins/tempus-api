<?php


namespace App\Fields;


use Illuminate\Support\Str;

class SelectField extends Field
{

    public bool $isSearchable = true;

    public function getModalInput(): string
    {
        $valuesField = $this->modelField->validations()->where('name', 'values')->first();
        $valuesField = (array) json_decode($valuesField->value);

        $options = '';

        foreach($valuesField as $valueField) {
            $options .= ' <option value="' . $valueField->name . '">{{ App\Models\\' . $this->modelField->model->name . '::' . Str::upper($this->modelField->database_name) . '_SELECT[\'' . $valueField->name . '\'] ?? \'\' }}</option>' . "\n\t\t\t\t\t\t\t";
        }

        return '
                  <div class="form-group" wire:ignore>
                            <label for="' . $this->modelField->database_name . '">Example select</label>
                            <select
                                class=""
                                id="' . $this->modelField->database_name . '"
                            >
                            <option selected>Selecione uma opção</option>
                            ' . $options .'
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
        });';
    }

    public function getTable(): string
    {
        $lowerCaseModelName = $this->getLowerCaseModelNameSingular();
        return "{{ \App\Models\\" . $this->modelField->model->name . "::" . Str::upper($this->modelField->database_name) . '_SELECT[$' . $lowerCaseModelName . 'Item->' . $this->modelField->database_name . "] ?? '' }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';
        return '$table->string(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
