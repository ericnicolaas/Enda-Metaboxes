<?php
/**
 * Add a gallery as a meta field
 *
 * @author Eric Daams <eric@ericnicolaas.com>
 */
class endaMetaFieldGallery extends endaMetaField {
  
  /**
   * Attachments
   * @var object
   */  
  protected $attachments = null;
    
  /**
   * Instantiate gallery field object 
   */
  public function __construct($id) {
    global $post;
    parent::__construct($id, '', true);
    $this->attachments = get_posts(array(
      'post_type' => 'attachment',
      'posts_per_page' => -1,
      'post_status' => null,
      'post_parent' => $post->ID,
      'post_mime_type' => 'image/jpeg'
    ));
  }
 
  /**
   * Render object as string
   * @return string
   */
  public function __toString() {
    $output = '<div class="enda-gallery"';
    $output .= '<table>';
    $output .= '<thead>';
    $output .= '<tr><th></th><th>Mark as Primary Photo</th></tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    foreach ($this->attachments as $image) {
      $output .= '<tr>';
      $output .= '<td><img src="'.$image->guid.'" alt="'.$image->post_title.'" title="'.$image->post_title.'" /></td>';
      $output .= '<td class="enda-featured-photo-select">';
      $output .= '<input type="radio" name="'.$this->id.'" value="'.$image->ID.'" '.$value==$image->ID ? 'checked' : ''.'/>';
      $output .= '</td>';
    }
    $output .= '</tbody>';
    $output .= '<a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?post_id='.$_GET['post'].'&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=445">Upload photo</a>';
    return $output;
  } 
}                                                          