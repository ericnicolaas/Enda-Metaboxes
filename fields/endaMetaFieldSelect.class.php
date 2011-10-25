<?php
/**
 * Generates a select field
 * @author Eric Daams 
 */

class endaMetaFieldSelect extends endaMetaField {
  
  public function __toString() {
    $output = '<div class="enda-meta-row">';
    $output .= isset($this->label) ? '<label for="' . $this->id . '">' . $this->label . ':</label>' : '';
    $output .= '<select name="' .$this->id . '" id="' . $this->id . '">';
    foreach ($this->options as $option) {
      $output .= '<option value="' . $option->id . '"' . $option->selected ? ' selected="selected"' : '' . '>';
      $output .= '</option>';
    }
    $output .= '</select>';
    $output .= '</div>';
    return $output;
  }
}