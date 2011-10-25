<?php
/**
 * Generates a textarea
 * @author Eric Daams 
 */

class endaMetaFieldTextarea extends endaMetaField {
  
  public function __toString() {
    $output = '<div class="enda-meta-row">';
    $output .= isset($this->label) ? '<label for="' . $this->id . '">' . $this->label . ':</label>' : '';
    $output .= '<textarea name="' .$this->name . '" id="' . $this->id . '"';
    if (count($this->attributes)) {
      $output .= $this->appendAttributes();
    }
    $output .= '>'; // Close textarea tag
    $output .= $this->value;
    $output .= '</textarea>';
    if (strlen($this->assist)) {
      $output .= '<span class="assist">'.$this->assist.'</span>';
    }
    $output .= '</div>';
    return $output;
  }
}