<?php
/**
 * Lê um arquivo .csv que contém o sistema decimal de deweye armazena o resultado
 * em um array
 * @param String $fileName
 * @param string $sep
 * @return array
 */
function parse_csv_cdd($fileName, $sep = ',') {
    $arrParsed = [];
    $top = 0;

    $handle = fopen ($fileName,"r");
    while ( ($data = fgetcsv($handle, 0, $sep) ) !== FALSE) {
        $data[0] = (int) $data[0] + 000;
        if( $data[0] % 100 == 0 ) {
            $top = $data[0];
            $arrParsed[$top] = ['nome' => $data[1], 'sub' => [] ];
        } else {
            $arrParsed[$top]['sub'][$data[0]] = $data[1];
        }
    }

    fclose ($handle);
    return $arrParsed;
}

function slugify($text)
{ 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}