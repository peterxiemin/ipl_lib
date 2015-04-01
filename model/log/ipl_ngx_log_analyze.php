<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/1
 * Time: 19:21
 */


function getArrFrargs($args)
{
    $arr = explode("&", $args);
    $dict = array();
    foreach ($arr as $item) {
        $tmp = explode("=", $item);
        if (count($tmp) !== 2) return false;
        $dict[$tmp[0]] = trim($tmp[1], "\n");
    }
    return $dict;
}


function getMid($filename)
{
    $handle = fopen($filename, "r");
    if ($handle) {
        $mid_dict = array();
        while (($buffer = fgets($handle, 4096)) !== false) {
            $args = explode("?", $buffer);
            if (count($args) == 3) {
                $tem_dict = getArrFrargs($args[2]);
                if ($tem_dict === false) continue;
                if (array_key_exists('mid', $tem_dict)) {
                    if (!array_key_exists($tem_dict['mid'], $mid_dict)) {
                        $mid_dict[$tem_dict['mid']] = 0;
                    }
                    $mid_dict[$tem_dict['mid']] += 1;
                }
                else {
                    //echo "no mid key in array\n";
                }
            }
            else {
                //echo "count is not 3\n";
            }
        }<?php
         /**
          * Created by PhpStorm.
          * User: xiemin
          * Date: 2015/4/1
          * Time: 19:21
          */


         function getArrFrargs($args)
         {
                 $arr = explode("&", $args);
                 $dict = array();
                 foreach ($arr as $item) {
                         $tmp = explode("=", $item);
                         if (count($tmp) !== 2) return false;
                         $dict[$tmp[0]] = trim($tmp[1], "\n");
                 }
                 return $dict;
         }


         function getMid($filename)
         {
                 $handle = fopen($filename, "r");
                 if ($handle) {
                         $mid_dict = array();
                         while (($buffer = fgets($handle, 4096)) !== false) {
                                 $args = explode("?", $buffer);
                                 if (count($args) == 3) {
                                         $tem_dict = getArrFrargs($args[2]);
                                         if ($tem_dict === false) continue;
                                         if (array_key_exists('mid', $tem_dict)) {
                                                 if (!array_key_exists($tem_dict['mid'], $mid_dict)) {
                                                         $mid_dict[$tem_dict['mid']] = 0;
                                                 }
                                                 $mid_dict[$tem_dict['mid']] += 1;
                                         }
                                         else {
                                                 //echo "no mid key in array\n";
                                         }
                                 }
                                 else {
                                         //echo "count is not 3\n";
                                 }
                         }
                         if (!feof($handle)) {
                                 echo "Error: unexpected fgets() fail\n";
                         }
                         fclose($handle);
                         //var_dump($mid_dict);
                         //$count = count($mid_dict);
                         //echo "uv: $count\n";
                         return $mid_dict;
                 }
         }

         $mid1 = getMid("./log1");
         $mid2 = getMid("./log2");
         $i = 0;
         foreach ($mid1 as $k => $v)
         {
                 if (array_key_exists($k, $mid2)) {
                         $i++;
                 }
         }

         echo "vaild: $i";

         ?>
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
        //var_dump($mid_dict);
        //$count = count($mid_dict);
        //echo "uv: $count\n";
        return $mid_dict;
    }
}

$mid1 = getMid("./log1");
$mid2 = getMid("./log2");
$i = 0;
foreach ($mid1 as $k => $v)
{
    if (array_key_exists($k, $mid2)) {
        $i++;
    }
}

echo "vaild: $i";

?>