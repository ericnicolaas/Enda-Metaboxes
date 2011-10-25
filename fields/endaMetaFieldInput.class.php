<?php

/**
 * Description of endaMetaFieldInput
 *
 * @abstract
 * @author Eric Daams <eric@ericnicolaas.com>
 */
abstract class endaMetaFieldInput extends endaMetaField {
  
  /**
   * Input type
   * @var string 
   */
  protected $type;
  
  /**
   * Instantiate input field
   * @return void
   */  
  public function __construct($id, $label = '', $single = true, $attributes = array(), $assist = '') {
    parent::__construct($id, $label, $single, $attributes, $assist); 
    $this->setType();
  }
  
  /**
   * Return object as string
   * @return string 
   */  
  public function __toString() {
    $output = '<div class="enda-meta-row enda-'.$this->type.'-field">';
    $output .= $this->label != '' ? '<label for="' . $this->htmlid . '">' . $this->label . ':</label>' : '';
    $output .= '<input name="' .$this->name . '" id="' . $this->htmlid . '" type="'. $this->type .'" value="' . $this->value . '"'; // Tag remains open
    if (count($this->attributes)) {
      $output .= $this->appendAttributes();    
    }
    $output .= '/>'; // Close input tag
    if (strlen($this->assist)) {
      $output .= '<span class="assist">'.$this->assist.'</span>';
    }
    $output .= '</div>';
    return $output;
  }
  
  abstract protected function setType();
}