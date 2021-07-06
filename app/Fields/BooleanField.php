<?php


namespace App\Fields;


class BooleanField extends Field
{

    public bool $isSearchable = true;

    public function getModalInput(): string
    {

        return '
            <div class="input-group my-3">
             <label class="inline-flex items-center">
                    <input type="checkbox" value="1" class="form-checkbox @error(\''. $this->modelField->database_name . '\') is-invalid @enderror"
                                   wire:model="'. $this->modelField->database_name . '" name="' . $this->modelField->label . '">

                      <span class="ml-3 text-sm">' . $this->modelField->label . '</span>
              </label>

             @error(\''. $this->modelField->database_name . '\')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            </div>';
    }


    public function getScripts(): string
    {
        return '';
    }

    public function getTable(): string
    {
        $lowerCaseModelName = $this->getLowerCaseModelNameSingular();
        return "{{ $" . $lowerCaseModelName . "Item->" . $this->modelField->database_name . " ?? '' }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';

        return '$table->tinyInteger(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
