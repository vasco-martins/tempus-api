<?php


namespace App\Fields;


class StringField extends Field
{

    public function getModalInput(): string
    {

        return '
            <label for="basiurl">' . $this->modelField->label . '</label>
            <div class="input-group mb-3">
                    <input type="text" class="form-control @error(\''. $this->modelField->database_name . '\') is-invalid @enderror"
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
        $lowerCaseModelName = $this->getLowerCaseModelName();
        return "{{ $$lowerCaseModelName->" . $this->modelField->database_name . " ?? '' }}";
    }
}
