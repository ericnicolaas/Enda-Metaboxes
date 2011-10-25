<?php
/**
 * A series of helper functions for generating Ajax 
 * 
 * @author Eric Daams <eric@ericnicolaas.com>
 */

class endaMetaboxesAjaxHelpers {

  /**
   * Saves a custom meta field via Ajax
   * @return void
   */
  public function saveField() {
    $i = $_POST['index'];
    $post_id = $_POST['post_id'];
    $field_id = $_POST['field_id'];
    $key = $_POST['key'];
    $value = $_POST['value'];
    $field_key = $field_id.'_' . str_replace(' ', '', ucwords($key));
    
    $exists = false;
    $current = get_post_meta($post_id, $field_id, true);
    if ($current && is_array($current)) {
      foreach ($current as $k) {
        if ($k['key'] == $key) {
          $exists = true;         
          continue;
        }
      }
    }               
    
    // If field was not alread listed, add it to the array
    if (!$exists) {      
      $current[] = array(
        'key' => $key, 
        'field' => $field_key
      );
      update_post_meta($post_id, $field_id, $current);
    }      
            
    // Also store the field value
    update_post_meta($post_id, $field_key, $value);    
    
    echo $field_key;
    die();
  }
  
  /**
   * Deletes a custom meta field
   * @return void
   */
  public function deleteField() {
    $i = $_POST['index'];
    $post_id = $_POST['post_id'];
    $field_id = $_POST['field_id'];
    $key = $_POST['key'];
    $field = $_POST['field'];    
    
    // Deletes the value field
    delete_post_meta($post_id, $field);
    
    // Updates the key array field
    $current = get_post_meta($post_id, $field_id, true);
    if ($current && is_array($current)) {
      foreach ($current as $i => $k) {
        if ($k['key'] == $key) {
          // remove $i element from array and resave array
          unset($current[$i]);
          update_post_meta($post_id, $field_id, array_values($current));
          echo json_encode(array(get_post_meta($post_id, $field_id, true)));
          die();
        }
      }
    }               
  }
  
  /**
   * Load fields via Ajax
   * @param int $post_id
   * @param string $field_id
   * @return string 
   */
  protected static function loadFields($post_id, $field_id) {
    return json_encode(get_post_meta($post_id, $field_id, true));
  }  
  
  /**
   * Convert string to field key
   * @param string $id
   * @param string $label
   * @return string
   */
  protected function convertToKey($id, $label) {
    return $id.'_' . str_replace(' ', '', ucwords($label));
  }  
}
