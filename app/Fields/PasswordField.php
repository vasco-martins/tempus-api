<?php


namespace App\Fields;


class PasswordField extends Field
{

    public bool $isSearchable = false;

    public function getModalInput(): string
    {

        return '
            <label for="basiurl">' . $this->modelField->label . '</label>
            <div class="input-group mb-3">
                    <input type="password" class="form-control @error(\''. $this->modelField->database_name . '\') is-invalid @enderror"
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
        return "Escondido";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';

        return '$table->string(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
