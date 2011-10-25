<?php
/**
 * Generates a checkbox field
 * @author Eric Daams 
 */

class endaMetaFieldCheckbox extends endaMetaFieldInput {
  
  public function setType() {
    $this->type = 'checkbox';
  }
}