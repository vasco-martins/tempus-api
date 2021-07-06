<?php


namespace App\Fields;


class TextareaField extends Field
{

    public bool $isSearchable = true;

    public function getModalInput(): string
    {

        $validations = $this->modelField->validations;
        $usesQuill = false;

        foreach ($validations as $validation) {
            if($validation->name == "quill" && $validation->value == 1) {
                $usesQuill = true;
                break;
            }
        }

        if($usesQuill) {
            return '
             <label for="basiurl">' . $this->modelField->label . '</label>

                        <div class="mt-2 bg-white" wire:ignore>
                            <div
                                class=""
                                x-data
                                x-ref="quillEditor"
                                x-init="
         quill = new Quill($refs.quillEditor, {theme: \'snow\'});
         quill.on(\'text-change\', function () {
           $dispatch(\'input\', quill.root.innerHTML);
           @this.set(\''. $this->modelField->database_name . '\', quill.root.innerHTML)
         });

         window.livewire.on(\'clear-input\', function() {
            quill.root.innerHTML = @this.get(\''. $this->modelField->database_name . '\');
         });
       "
                                x-on:quill-input.debounce.2000ms="@this.set(\''. $this->modelField->database_name . '\', $event.detail)"
                            >
                                {!! $'. $this->modelField->database_name . ' !!}
                            </div>

                        </div>

                          <input type="hidden" class="is-invalid">
                        @error(\''. $this->modelField->database_name . '\')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
            ';

        }


        return '<label for="basiurl">' . $this->modelField->label . '</label>
                        <div class="input-group mb-3">
                            <textarea name="text" class="form-control @error(\''. $this->modelField->database_name . '\') is-invalid @enderror" wire:model="'. $this->modelField->database_name . '"></textarea>
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
        return "{{ \Illuminate\Support\Str::limit(strip_tags($" . $lowerCaseModelName . "Item->" . $this->modelField->database_name . "), 50,'...')  }}";
    }

    public function getMigration(): string
    {
        $isRequired = $this->getValidation('required') == null|false ? '->nullable()' : '';
        return '$table->text(\'' . $this->modelField->database_name . '\')' . $isRequired . ';';
    }
}
