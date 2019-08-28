<?php

function dodb ($query,$type)
{

$result="OK";

$db_host = '';
$db_user = '';
$db_pass = '';
$db_name = '';

$link = @mysqli_connect($db_host,$db_user,$db_pass,$db_name);

if (mysqli_connect_errno()) $result=false;
        else {
                $result = mysqli_query($link,$query);
                if ($type=="multi") {
                        $count=0;
                        while ($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                $q_list_array[$count]=$row;
                                $count++;
                        }
                        @mysqli_free_result($result);
                        @$result=$q_list_array;
                }
        }

@mysqli_close ($link);

return $result;
}

?>
