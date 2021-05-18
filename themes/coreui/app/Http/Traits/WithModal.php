<?php

namespace  App\Http\Traits;

trait WithModal {

    public $isOpen = 0;

    public function openModal() {
        $this->isOpen = 1;
    }

    public function closeModal() {
        $this->isOpen = 0;
        $this->resetErrorBag();
        $this->resetValidation();
    }

}
