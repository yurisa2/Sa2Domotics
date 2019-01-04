<?php

class FileToDb {

  public function readFiles($directory) {
    $this->directory = $directory;
    
    $this->file_list = scandir($directory,1);
    return $this->file_list;
  }

  public function readContents($prefix) {
    $table = array();
    foreach ($this->file_list as $key => $value) {
      if ($prefix == substr($value,0,strlen($prefix))) {
        $file_content = file_get_contents($this->directory."/".$value);
        $file_content = json_decode($file_content);
        $file_content->filename = (string)$value;
        $table[] = $file_content;
      }
    }
    return $table;
  }

}



?>
