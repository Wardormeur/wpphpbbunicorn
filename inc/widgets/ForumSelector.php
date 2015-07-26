<?php
namespace wpphpbbu\widgets;

class ForumSelector{

    function print_forum($forums = null, $selected)
    {
      // var_dump($selected);
      // Use get_post_meta to retrieve an existing value from the database.
      $checked = false;
      foreach ($forums as $name => $id) {
        if( $selected ){ // we check there is some predefined forums, in case of new article for exemple
          $checked = in_array($id, $selected);
        }
      ?>
      <div id="forum_<?php echo $id ?>"  >
          <label for="forum_id[]"><?php echo $name ?></label>
          <input type="checkbox" name="forum_id[]" value="<?php echo $id ?>"
            <?php echo $checked? "checked" : "";?> />
      </div>
      <?php
      }
  }


}
 ?>
