<?php


namespace App\Fields;


class EmailField extends Field
{

    public bool $isSearchable = true;

    public function getModalInput(): string
    {

        return '
            <label for="basiurl">' . $this->modelField->label . '</label>
            <div class="input-group mb-3">
                    <input type="email" class="form-control @error(\''. $this->modelField->database_name . '\') is-invalid @enderror"
                                   wire:model="'. $this->modelField->database_name . '" name="' . $this->modelField->label . '">
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
        return '$table->string(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
