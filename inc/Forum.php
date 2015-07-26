<?php
namespace wpphpbbu;

class Forum{
  private $forum_list = [];

  function __construct($user_id){
    global $table_prefix,$db,$auth;

    $sql = 'SELECT forum_id,forum_name from '.$table_prefix.'forums';
    $forums = $db->sql_query($sql);
    while($forum = $forums->fetch_assoc())
    {
      if ($auth->acl_get('f_', $forum['forum_id'])){
        $this->forum_list[$forum['forum_name']] = $forum['forum_id'];
      }
    }
  }

  function get_forum_list(){
    return $this->forum_list;
  }
}
