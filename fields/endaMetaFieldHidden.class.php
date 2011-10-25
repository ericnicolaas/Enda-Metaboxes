<?php
/**
 * Generates a hidden field
 * @author Eric Daams 
 */

class endaMetaFieldHidden extends endaMetaFieldInput {
  
  public function setType() {
    $this->type = 'hidden';
  }
}