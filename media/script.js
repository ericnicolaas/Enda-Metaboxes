$ = jQuery.noConflict();

$(document).ready(function(){
  
  /**
   * Return HTML-safe string
   */
  var escapeHtml = function(unsafe) {
    return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  };
  
  /**
   * Update custom field via Ajax
   */
  var updateField = function(element, e) {
    e.preventDefault();
    
    // Set up data object 
    var row = $(element).parents('tr'), 
    data = $.parseJSON($(element).attr('rel'));        
    data.action = 'save_field';    
    data.key = row.find('.key input').val();        
    data.value = row.find('.value input').val();        
    
    $.ajax({
      url: ajaxurl, 
      type: 'POST', 
      data: data, 
      success: function(results) {        
        row.find('.value input').attr('name', results);
        row.find('input')
          .css({ 'background-color' : '#effeb9' }) 
          .delay(3000)
          .animate({ 'background-color' : '#ffffff' }, 300);        
      }
    });
  }
  
  /**
   * Delete custom field via Ajax
   */
  var deleteField = function(element, e) {
    e.preventDefault();    
    
    // Set up data object
    var row = $(element).parents('tr'), 
    data = $.parseJSON($(element).attr('rel'));
    data.action = 'delete_field';    
    data.key = row.find('.key input').val();
    data.field = row.find('.value input').attr('name');

    $.ajax({
      url: ajaxurl, 
      type: 'POST', 
      data: data, 
      success: function(results){
        console.log(results);
      }
    }); 
  }
  
  // Add fields
  $('#addFields').click(function(e){
   e.preventDefault();
   
   var rel = $.parseJSON($(this).attr('rel')),
       table = $(this).parents('table'), 
       lastRow = table.find('tbody tr:last'), 
       jsonObj = $.parseJSON(lastRow.find('button[name="updateField"]').attr('rel')),
       cnt = $('#cntFields').val();
       newRows = '';
   
   for (i=1; i<=cnt; i=i+1) {
     var index = jsonObj.index + i, 
         keyName = rel.field_id+'['+index+'][key]', 
         keyId = rel.field_id+'_'+index+'_key', 
         valueName = rel.field_id+'['+index+'][value]', 
         valueId = rel.field_id+'_'+index+'_value';
          
     // Creates associated json object for the update and delete buttons for this field
     jsonObj.index = index;
     var jsonString = escapeHtml(JSON.stringify(jsonObj));
           
     newRows += '<tr>'
             + '<td class="key"><div class="enda-meta-row enda-text-field"><input name="'+keyName+'" id="'+keyId+'" type="text" value="" /></div></td>'
             + '<td class="value"><div class="enda-meta-row enda-text-field"><input name="'+valueName+'" id="'+valueId+'" type="text" value="" /></div></td>'
             + '<td><button class="button" name="updateField" rel="'+jsonString+'">Update</button><button class="button" name="deleteField" rel="'+jsonString+'">Delete</button></td>'
             + '</tr>';
   }     
			
   $(newRows).insertAfter(lastRow);
   
   $('button[name="updateField"]').unbind('click').bind('click', function(e) { updateField(this, e); });
   $('button[name="deleteField"]').unbind('click').bind('click', function(e) { deleteField(this, e); });
   
  });
  
  // Update field
  $('button[name="updateField"]').bind('click', function(e) { updateField(this, e); });
    
  // Delete field
  $('button[name="deleteField"]').bind('click', function(e) { deleteField(this, e); });
});