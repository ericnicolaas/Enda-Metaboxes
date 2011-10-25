<?php

/**
 * Custom meta field where fields are added for key/value pairs
 *
 * @author Eric Daams <eric@ericnicolaas.com>
 */
class endaMetaFieldKeyValue extends endaMetaField {
  
  /**
   * Fields to display
   * @var array
   */
  protected $fields = array();
  
  /**
   * Key field
   * @var endaMetaField
   */
  protected $keyfield;
  
  /**
   * Value field
   * @var endaMetaField
   */
  protected $valuefield;
  
  /**
   * Button text
   * @var string
   */
  protected $buttontext;
  
  /**
   * Button ID
   * @var string
   */
  protected $buttonid;  
  
  /**
   * Registered keys
   * @var array
   */
  protected $keys;
  
  /**
   * Instantiate field
   * @return void
   */  
  public function __construct($id, $labels = array(), $single = true, $attributes = array()) {    
    global $post;
    $this->setId($id);
    $this->setName($id);    
    $keys = get_post_meta($post->ID, $this->name, $single);
    $this->keys = is_array($keys) ? $keys : array();
    
    for ($i=0; $i<=count($this->keys); $i++) {           
      $this->fields[$i]['key'] = new endaMetaFieldText(array($id, $i, 'key'), '', $single, $attributes); // This is tied to the parent key

      // The value fields are generated on the fly and link to the value field
      $key = isset($this->keys[$i]['key']) ? $this->convertToKey($this->keys[$i]['key']) : array($id, $i, 'value');
      $this->fields[$i]['value'] = new endaMetaFieldText($key, '', $single, $attributes); // This is a unique key                  
    } 
  }
  
  /**
   * Return object as string
   * @return string 
   */  
  public function __toString() {    
    $output = '<table class="enda-key-value-fields">';
    $output .= '<thead><tr>';
    $output .= '<td>Name</td>';
    $output .= '<td>Value</td>';
    $output .= '<td>Actions</td>';
    $output .= '</tr></thead>';    
    $output .= '<tfoot>';    
    $output .= '<tr>';
    $output .= '<td colspan=3>';
    $output .= '<h4>Add blank fields:</h4>';
    $output .= '<label for"cntFields">Number of fields to add:</label>';
    $output .= '<select id="cntFields" name="cntFields">';
    for ($i=1; $i<31; $i++) {
      $output .= '<option value="'.$i.'">'.$i.'</option>';
    }      
    $output .= '</select>';
    $output .= '<button id="addFields" name="addFields" class="button" rel="'.$this->getFieldJson().'">Add Fields</button></td>';    
    $output .= '</tr>';
    $output .= '</tfoot>';
    $output .= '<tbody>';
    foreach ($this->fields as $i => $f) {            
      $output .= '<tr>';    
      $output .= '<td class="key">'.$f['key'].'</td>';
      $output .= '<td class="value">'.$f['value'].'</td>';
      $output .= '<td>';
      $output .= '<button class="button" name="updateField" rel="'.$this->getUpdateJson($i).'" id="add_'.$this->id.'">Update</button>';
      $output .= '<button class="button" name="deleteField" rel="'.$this->getDeleteJson($i).'" id="delete_'.$this->id.'">Delete</button>';
      $output .= '</td>';    
      $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    return $output;    
  }  
  
  /**
   * Return json string for field update
   * @param int $i
   * @return string
   */
  public function getUpdateJson($i) {
    return htmlspecialchars(json_encode(array(
        'field_id' => $this->id, 
        'post_id' => $_GET['post'], 
        'index' => $i
    )));
  }
  
  /**
   * Return json string for field deletion
   * @param int $i
   * @return string
   */
  public function getDeleteJson($i) {
    return htmlspecialchars(json_encode(array(
        'field_id' => $this->id, 
        'post_id' => $_GET['post'], 
        'index' => $i
    )));
  }
  
  /**
   * Return json string for adding new fields
   * @return string
   */
  public function getFieldJson() {
    return htmlspecialchars(json_encode(array(
        'field_id' => $this->id
    )));
  }
  
  /**
   * Save field values
   * @param int $post_id
   * @param array $value
   * @param array $submitted    $_POST array
   * @return void
   */
  public function save($post_id, $value, $submitted) {
    $keys = array();
    foreach ($value as $f) {
      if (!empty($f['key'])) {
        $field = $this->convertToKey($f['key']);
                
        $keys[] = array(
          'key' => $f['key'], 
          'field' => $field
        );
        
        // Save value for each key
        $value = isset($f['value']) ? $f['value'] : $submitted[$field];
        update_post_meta($post_id, $field, $value);
      }        
    }        
    
    // Save field keys
    update_post_meta($post_id, $this->id, $keys);    
  }  
  
  /**
   * Convert string to field key
   * @param string $label
   * @return string
   */
  protected function convertToKey($label) {
    return $this->id.'_' . str_replace(' ', '', ucwords($label));
  }
}