<?php
/**
 * A class designed to be extended and used by any plugin that needs to generate custom metaboxes
 * @package Enda Metaboxes
 */

/*
Plugin Name: Enda Metaboxes 
Plugin URI: http://ericnicolaas.com
Description: A set of classes designed to be extended and used by any plugin that needs to generate custom metaboxes.
Version: 1.0
Author: Eric Daams
Author URI: http://ericnicolaas.com
*/

/**
 * Require all files
 */
add_action('plugins_loaded', 'enda_metaboxes_init');

function enda_metaboxes_init() {  
  require_once('fields/endaMetaField.class.php');
  require_once('fields/endaMetaFieldInput.class.php');
  require_once('fields/endaMetaFieldSelect.class.php');
  require_once('fields/endaMetaFieldText.class.php');
  require_once('fields/endaMetaFieldCheckbox.class.php');
  require_once('fields/endaMetaFieldHidden.class.php');
  require_once('fields/endaMetaFieldTextarea.class.php');
  require_once('fields/endaMetaFieldGallery.class.php');
  require_once('fields/endaMetaFieldKeyValue.class.php');
  require_once('helpers/endaMetaboxesAjaxHelpers.class.php');
}

/**
 * Register admin stylesheet
 */
add_action('admin_print_styles', 'enda_metaboxes_admin_styles');

function enda_metaboxes_admin_styles() {
  wp_register_style('enda-metaboxes-css', '/wp-content/plugins/enda-metaboxes/media/style.css');
  wp_enqueue_style('enda-metaboxes-css');    
}

/**
 * Register admin javascript
 */
add_action('admin_enqueue_scripts', 'enda_metaboxes_admin_scripts');

function enda_metaboxes_admin_scripts() {
  wp_register_script('enda-metaboxes-js', '/wp-content/plugins/enda-metaboxes/media/script.js');
  wp_enqueue_script('enda-metaboxes-js');
}

/**
 * Register Ajax hooks
 */
add_action('wp_ajax_save_field', array('endaMetaboxesAjaxHelpers', 'saveField'));
add_action('wp_ajax_delete_field', array('endaMetaboxesAjaxHelpers', 'deleteField'));

/**
 * Generate metaboxes with built-in save function
 * @author Eric Daams
 */
abstract class endaMetaBoxes {
  
  /**
   * Stores the post type's metaboxes
   * @var array 
   */
  protected $metaboxes = array();
  
  /**
   * Stores the field IDs 
   * @var array 
   */
  protected $metafields = array();   
    
  /**
   * Instantiate object and register action hooks
   * @param bool $save
   * @param integer $post_id
   * @param object $post
   * @return void
   */
  public function __construct($save = false, $post_id = '', $post = null) {        
    $this->addMetaBoxes();    
    $this->registerMetaBoxes();
    if ($save) {
      $this->save($post_id, $post);
    }   
  }
  
  /**
   * Add metabox
   * @param endaMetaBox
   */
  public function addBox(endaMetaBox $box) {
    $this->metaboxes[$box->getId()] = $box;
    $this->addFields($box->getFields());
  }
  
  /**
   * Add fields
   * @param array $fields
   */
  public function addFields($fields) {
    foreach ($fields as $field_id => $field) {
      $this->addField($field_id, $field);
    }
  }
  
  /**
   * Adds a meta field to the array of fields to save
   * @param int $field_id
   * @param array $field
   */
  public function addField($field_id, array $field) {
    $this->metafields[$field_id] = $field;
  }
  
  /**
   * Register metaboxes. Runs on admin_init hook
   * @return void
   */
  public function registerMetaBoxes() {    
    if (empty($this->metaboxes))
      return;

    foreach($this->metaboxes as $box) {      
      add_meta_box($box->getId(), $box->getTitle(), array(&$this, 'outputMetaBoxes'), $this->post_type, $box->getContext(), $box->getPriority(), $box);
    }    
  }
  
  /*
   * Display meta boxes
   * @return void
   */
  public function outputMetaBoxes($post, $box) {   
    $this->metaboxes[$box['id']]->renderBeforeFields();
    foreach ($this->metaboxes[$box['id']]->getFields() as $field) {
      foreach ($field as $f)
      {
        echo $f;
      }      
    }        
    $this->metaboxes[$box['id']]->renderAfterFields();
    wp_nonce_field('enda_meta_'.$this->post_type, 'enda_nonce_'.$this->post_type, false);
  }

  /**
   * Runs on post save. Save fields to database. 
   * @return void
   */
  public function save($post_id, $post) {
    global $wpdb;    
    
    // Verify if this is an auto save routine. If it is, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return $post_id;

    // Verify this came from our screen
    if (!wp_verify_nonce($_POST['enda_nonce_'.$this->post_type], 'enda_meta_'.$this->post_type)) {
      return $post_id;
    }
    
    // Verify that user has permission to edit the page
    if (current_user_can('edit_page', $post_id)) {  
      // For every registered meta field, check if it's been set. If so, save it.
      foreach ($this->metafields as $field_id => $field) {                
        if (!empty($_POST[$field_id])) {          
          $field[0]->save($post_id, $_POST[$field_id], $_POST);          
        }        
      }
    } 
  }  
    
  /**
   * Central function for adding metaboxes
   * @abstract
   */
  abstract protected function addMetaBoxes();
}

/**
 * Meta box object 
 * @author Eric Daams
 */

class endaMetaBox {  

  /**
   * Meta box ID
   * @var string
   */
  protected $box_id;
  
  /**
   * Meta box title
   * @var string
   */
  protected $box_title;
  
  /**
   * Context
   * @var string
   */
  protected $context;
  
  /**
   * Priority 
   * @var string
   */
  protected $priority;
  
  /**
   * Fields
   * @var array
   */
  protected $fields = array();
    
  /**
   * Arbitrary text/html to insert before fields
   * @var string
   */
  protected $beforefields;
  
  /**
   * Arbitrary text/html to insert after fields
   * @var string
   */  
  protected $afterfields;  
  
  /**
   * Instantiate meta box
   * @param endaMetaBoxes $metaboxes
   * @param string $id
   * @param string $title
   * @param string $context
   * @param string $priority
   */
  public function __construct($id, $title, $context = 'advanced', $priority = 'default') {
    $this->box_id = $id;
    $this->box_title = $title;
    $this->context = $context;
    $this->priority = $priority;
  }
  
  /**
   * Add meta field
   * @param endaMetaField $field
   * @return void
   */
  public function addField(endaMetaField $field) {    
    $this->fields[$field->getId()][] = $field;    
  }
  
  /**
   * Return fields
   * @return array
   */
  public function getFields() {
    return $this->fields;
  }
  
  /**
   * Return box ID
   * @return string
   */
  public function getId() {
    return $this->box_id;
  }
  
  /**
   * Return box title
   * @return string
   */
  public function getTitle() {
    return $this->box_title;
  }
  
  /**
   * Return context
   * @return string
   */
  public function getContext() {
    return $this->context;
  }
  
  /**
   * Return priority
   * @return string
   */
  public function getPriority() {
    return $this->priority;
  }
  
  
  /**
   * Add text to display before fields
   * @param string $string
   * @return void
   */
  public function addStringBefore($string)
  {
    $this->beforefields = $string;
  }
  
  /**
   * Add text to display after fields
   * @param string $string
   * @return void
   */
  public function addStringAfter($string)
  {
    $this->afterfields = $string;
  }  
  
  /**
   * Add paragraph
   * @param string $string
   * @param string $where     Where to add paragraph. Possible values: before, after 
   * @return void 
   */
  protected function addParagraph($string, $where)
  {
    $method = 'addString'.ucfirst($where);
    $this->$method('<p>'.$string.'</p>');
  }
  
  /**
   * Add paragraph before fields
   * @param string $string
   * @return void
   */
  public function addParagraphBefore($string)
  {
    $this->addParagraph($string, 'before');
  }
  
  /**
   * Add text to display after fields
   * @param string $string
   * @return void
   */  
  public function addParagraphAfter($string)
  {
    $this->addParagraph($string, 'after');
  }  
  
  /**
   * Render content before fields
   * @return void
   */
  public function renderBeforeFields()
  {
    echo $this->beforefields;
  }
  
  /**
   * Render content after fields
   * @return void
   */
  public function renderAfterFields()
  {
    echo $this->afterfields;
  }
}