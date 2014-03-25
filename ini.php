<?php
function readINIfile ($filename, $commentchar) {
  $array1 = file($filename);
  $section = '';
  foreach ($array1 as $filedata) {
    $dataline = trim($filedata);
    $firstchar = substr($dataline, 0, 1);
    if ($firstchar!=$commentchar && $dataline!='')
    {
      //It's an entry (not a comment and not a blank line)
      if ($firstchar == '[' && substr($dataline, -1, 1) == ']')
	{
        //It's a section
        $section = strtolower(substr($dataline, 1, -1));
      }
	else
	{
        //It's a key...
        $delimiter = strpos($dataline, '=');
        if ($delimiter > 0)
	  {
          //...with a value
          $key = strtolower(trim(substr($dataline, 0, $delimiter)));
          $value = trim(substr($dataline, $delimiter + 1));
          if (substr($value, 1, 1) == '"' && substr($value, -1, 1) == '"') { $value = substr($value, 1, -1); }
          $array2[$section][$key] = stripcslashes($value);
        }
	  else
	  {
          //...without a value
          $array2[$section][strtolower(trim($dataline))]='';
        }
      }
    }
    else
    {
      //It's a comment or blank line.  Ignore.
    }
  }
  return $array2;
}

function writeINIfile ($filename, $array1, $commentchar, $commenttext)
{
  $handle = fopen($filename, 'wb');
  if ($commenttext!='')
  {
    $comtext = $commentchar.
      str_replace($commentchar, "\r\n".$commentchar,
        str_replace ("\r", $commentchar,
          str_replace("\n", $commentchar,
            str_replace("\n\r", $commentchar,
              str_replace("\r\n", $commentchar, $commenttext)
            )
          )
        )
      )
    ;
    if (substr($comtext, -1, 1)==$commentchar && substr($comtext, -1, 1)!=$commentchar)
    {
      $comtext = substr($comtext, 0, -1);
    }
    fwrite ($handle, $comtext."\r\n");
  }
  foreach ($array1 as $sections => $items)
  {
    //Write the section
    if (isset($section)) { fwrite ($handle, "\r\n"); }
    //$section = ucfirst(preg_replace('/[\0-\37]|[\177-\377]/', "-", $sections));
    $section = preg_replace('/[\0-\37]|\177/', "-", $sections);
    fwrite ($handle, "[".$section."]\r\n");
    foreach ($items as $keys => $values)
    {
      //Write the key/value pairs
      //$key = ucfirst(preg_replace('/[\0-\37]|=|[\177-\377]/', "-", $keys));
      $key = preg_replace('/[\0-\37]|=|\177/', "-", $keys);
      if (substr($key, 0, 1)==$commentchar) { $key = '-'.substr($key, 1); }
      $value = addcslashes($values,'');
      fwrite ($handle, '    '.$key.' = '.$value."\r\n");
    }
  }
  fclose($handle);
}
?>
