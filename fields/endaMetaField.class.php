<?php
/**
 * Generates the HTML output for a meta field
 * @author Eric Daams
 */
abstract class endaMetaField {          
  
  /**
   * Field ID
   * @var string
   */
  protected $id;
  
  /**
   * HTML ID
   * @var string
   */
  protected $htmlid;  

  /**
   * Field name
   * @var string
   */
  protected $name;  
    
  /**
   * Field label
   * @var string
   */
  protected $label;    
  
  /**
   * Field value
   * @var mixed
   */
  protected $value;
  
  /**
   * Field attributes
   * @var array
   */
  protected $attributes;
  
  /**
   * Field assist
   * @var string
   */
  
  /**
   * Instantiate field object
   * @return void
   */
  public function __construct($id, $label = '', $single = true, $attributes = array(), $assist = '') {    
    $this->setId($id);
    $this->setName($id);    
    $this->setHtmlId($id);
    $this->setValue($id, $single);
    $this->setLabel($label);    
    $this->setAttributes($attributes);    
    $this->setAssist($assist);
  }
  
  /**
   * Set field name
   * @param mixed $id 
   */
  public function setName($id) {        
    $this->name = $this->id;
    if (is_array($id)) {      
      for ($i=1; $i < count($id); $i++) {
        $this->name .= '['.$id[$i].']';
      }      
    }    
  }
  
  /**
   * Set field ID
   * @param mixed $id
   */
  public function setId($id) {    
    $this->id = is_array($id) ? $id[0] : $id;    
  }
  
  /**
   * Set HTML ID
   * @param mixed $id 
   */
  public function setHtmlId($id) {
   $this->htmlid = $this->id;
    if (is_array($id)) {      
      for ($i=1; $i < count($id); $i++) {
        $this->htmlid .= '_'.$id[$i];
      }      
    }
  }
  
  /**
   * Set field value
   * @param mixed $id 
   * @param bool $single
   */
  public function setValue($id, $single) {
    global $post;
    $value = get_post_meta($post->ID, $this->id, $single);
    
    // Allow for multi-dimensional arrays
    for ($i=1; $i<count($id); $i++) {
      $value = $value[$id[$i]];
    }
    
    $this->value = $value;      
  }
  
  /**
   * Set assist
   * @param string $assist
   * @return void
   */
  public function setAssist($assist) {
    $this->assist = $assist;
  }
  
  /**
   * Set field label
   * @param string $label 
   */
  public function setLabel($label) {
    $this->label = $label;
  }
  
  /**
   * Set field attributes
   * @param array $attributes 
   */
  public function setAttributes(array $attributes) {
    $this->attributes = $attributes;
  }    

  /**
   * Return field ID
   * @return string
   */
  public function getId() {
    return $this->id;
  }
  
  /**
   * Return field label
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }
  
  /**
   * Appends attributes to a form field tag
   * @param string $output
   * @return string
   */
  protected function appendAttributes() {
    $output = '';
    foreach ($this->attributes as $key => $value) {
      if (!in_array($key, array('name', 'id', 'type', 'value'))) {        
        $output .= " $key=\"$value\"";
      }
    }
    return $output;
  }
  
  /**
   * Save field value
   * @param int $post_id
   * @param mixed $value
   * @param array $submitted    POST array
   * @return void
   */
  public function save($post_id, $value, array $submitted) {
    update_post_meta($post_id, $this->getId(), $value);
  }
  
  /**
   * __toString method
   * @abstract
   */
  abstract public function __toString();
  
}