<?php
namespace App\MyClasses;

class RequestValidator {
    protected $fields = ['phone', 'card', 'email'];

    public function getFields() {
        return $this->fields;
    }

    public function validate($type, $id) {
        return (in_array($type, $this->fields) && !empty($id));
    }
}