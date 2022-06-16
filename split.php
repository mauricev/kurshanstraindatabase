<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
      echo "we are here<br>";
      $string = 'twa653';
      $array = preg_split('/([a-z]+)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
      var_dump($array);
     ?>
  </body>
</html>
