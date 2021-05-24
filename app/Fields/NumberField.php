<?php


namespace App\Fields;


class NumberField extends Field
{

    public bool $isSearchable = true;

    public function getModalInput(): string
    {
        $min = $this->getValidation('min') != null ? 'min="' . $this->getValidation('min') . '"' : '';
        $max = $this->getValidation('max') != null ? 'min="' . $this->getValidation('max') . '"' : '';

        return '
            <label for="basiurl">' . $this->modelField->label . '</label>
            <div class="input-group mb-3">
                    <input type="number" ' . $min . ' ' . $max . ' class="form-control @error(\''. $this->modelField->database_name . '\') is-invalid @enderror"
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
        return "{{ $$lowerCaseModelName->" . $this->modelField->database_name . " ?? 'Sem dados' }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';
        return '$table->integer(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
