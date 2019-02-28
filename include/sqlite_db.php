<?php


class sa2_db
{

  function __construct()
  {
    $db_string = "sqlite:db/sa2domotics.db";

    $this->obj_db = new PDO($db_string);
    $this->obj_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  function insert_temp_logger($data_array)    {

    $sql_query =
    "
    insert into temp_logger
    (time,millis,tempc0,tempc1,heat_pump,main_pump)
    values
    ('$data_array[time]',
    '$data_array[millis]',
    '$data_array[tempc0]',
    '$data_array[tempc1]',
    '$data_array[heat_pump]',
    '$data_array[main_pump]'
    )";

    try {
      $this->obj_db->exec($sql_query);

    }
    catch(Exception $e) {
      echo 'Exception -> ';
      echo "$sql_query<br>";
      var_dump($e->getMessage());
      exit;
    }
    //  echo $sql_query; //DEBUG

    return $this->obj_db->lastInsertId();
  }



  function insert_event($data_array)    {

    $sql_query =
    "
    insert into temp_logger
    (time,event,event_text)
    values
    ('$data_array[time]',
    '$data_array[event]',
    '$data_array[event_text]'
    )";

    try {
      $this->obj_db->exec($sql_query);

    }
    catch(Exception $e) {
      echo 'Exception -> ';
      echo "$sql_query<br>";
      var_dump($e->getMessage());
      exit;
    }
    //  echo $sql_query; //DEBUG

    return $this->obj_db->lastInsertId();
  }

  function select_temps($size_secs) {

    if($size_secs == 0 || is_null($size_secs)) $size_secs = 3600;
    // $sql_query = " select * from temp_logger where time > ".time() - $size_secs;
    $sql_query = " select * from temp_logger where temp_logger.time > ".(time() - $size_secs);

echo $sql_query ." <br>";

    try {
     $prepared =  $this->obj_db->prepare($sql_query);
      $prepared->execute();
    }

    catch(Exception $e) {

      echo 'Exception -> ';
      echo "$sql_query<br>";
      var_dump($e->getMessage());

      exit;
    }

//     $retorno = array();
// foreach ($prepared->fetchAll() as $key => $value) {
//   $retorno[$value["time"]] = $value;
// }
    //  echo $sql_query; //DEBUG
    return $prepared->fetchAll();
  }



}







?>
