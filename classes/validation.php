<?php

class Validation {
  public $errors = array();
  public function validate($data, $rules) {
    $valid = TRUE;
    foreach($rules as $fieldname => $rule) {
      $callbacks = explode('|', $rule);
      foreach($callbacks as $callback) {
        $value = isset($data[$fieldname]) ? $data[$fieldname] : NULL;
        if ($this->$callback($value, $fieldname) == FALSE) {
          $valid = FALSE;
        }
      }
    }
    return $valid;
  }

  public function email($value, $fieldname) {
    if (!empty($value)) {
      $valid = filter_var($value, FILTER_VALIDATE_EMAIL);
      if ($valid == FALSE) {
        $this->errors[] = "$fieldname nem helyes e-mail cím.";
      }
    }
    else {
      return TRUE;
    }
    return $valid;
  }

  public function text($value, $fieldname) {
    $whitelist = '/^[a-zA-Z0-9 ,\.\+:]+$/';
    if (!empty($value)) {
      $valid = preg_match($whitelist, $value);
      if ($valid == FALSE) {
        $this->errors[] = "$fieldname nem megengedett karaktereket tartalmaz.";
      }
    }
    else {
      return TRUE;
    }
    return $valid;
  }

  public function password($value, $fieldname) {
    $whitelist = "/^[a-zA-Z0-9 ,\.\+;:!_\-@]+$/";
    if (!empty($value)) {
      $valid = preg_match($whitelist, $value);
      if ($valid == FALSE) {
        $this->errors[] = "$fieldname nem megengedett karaktereket tartalmaz.";
      }
    }
    else {
      return TRUE;
    }
    return $valid;
  }

  public function required($value, $fieldname) {
    $valid = !empty($value);
    if ($valid == FALSE) {
      $this->errors[] = "A(z) $fieldname mező megadása kötelező.";
    }
    return $valid;
  }
}