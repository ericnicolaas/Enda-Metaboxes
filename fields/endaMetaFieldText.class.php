<?php
/**
 * Generates a text field
 * @author Eric Daams 
 */

class endaMetaFieldText extends endaMetaFieldInput {
  
  public function setType() {
    $this->type = 'text';
  }      
}