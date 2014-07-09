<html>
<head>
	<title>Top 4 Color Picker</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body style="background-color:#e4e3e2">  
<?php
    
    $img = $_POST['upload'];
    $imgHand = imagecreatefrompng("$img");
    $imgSize = GetImageSize($img);
    $imgWidth = $imgSize[0];
    $imgHeight = $imgSize[1];
    echo '<div class="custom">';
    echo "<img src='$img' style='border:1px solid black;float:left;height:320px;'/>";
        // Define a new array to store the info
    echo "<label class='proc_label'>Matched Color</label>
    <label class='proc_label'>Top 4 Color</label>
    ";
    echo '<div class="innerdiv">';

    $pxlCorArr= array();
    for ($l = 0; $l < $imgHeight; $l++) {
        // Start a new "row" in the array for each row of the image.
        $pxlCorArr[$l] = array();
        for ($c = 0; $c < $imgWidth; $c++) {
            $pxlCor = ImageColorAt($imgHand,$c,$l);
            $r = ($pxlCor >> 16) & 0xFF;
            $g = ($pxlCor >> 8) & 0xFF;
            $b = $pxlCor & 0xFF;
            // Put each pixel's info in the array
            $pxlCorArr[$l][$c] = $r*1000000+$g*1000+$b;
        }
    }
    $output_array=array();
   //convert 2d to 1d array
    for ($i = 0; $i < $imgHeight; $i++) {
      for ($j = 0; $j < $imgWidth; $j++) {
        $output_array[] = $pxlCorArr[$i][$j];
      }
    }
   
   
    $reduce=array_count_values($output_array);

     arsort($reduce);
     $newarr=array_slice($reduce,0,4);
    
     $find1=$newarr[0];
     $find2=$newarr[1];
     $find3=$newarr[2];
     $find4=$newarr[3];
     $find1= array_search($find1, $reduce);
     $find2= array_search($find2, $reduce);
     $find3= array_search($find3, $reduce);
     $find4= array_search($find4, $reduce);

     function euler_distance($h1,$s1,$l1,$h2,$s2,$l2){
        $temp=($h2-$h1)*($h2-$h1)+($s2-$s1)*($s2-$s1)+($l2-$l1)*($l2-$l1);
        return sqrt($temp);
     }
     //this is sample image library obviously we can use db to store hsl of color present in out library.
     function matching($Hue,$Saturation,$Lightness){
        $hslcolorarray = array(
            array(0, 0.0, 0.0),
            array(0, 100.0, 50.0),
            array(120, 100.0, 50.0),
            array(240, 100.0, 50.0),
            array(60, 100.0, 50.0), 
            array(0, 0.0, 100.0),
            array(208, 100, 97), 
            array(34, 78, 91), 
            array(180, 100, 50), 
            array(271, 76, 53), 
            array(0, 59, 41), 
            array(34, 57, 70), 
            array(16, 100, 66), 
            array(48, 100, 93), 
            array(180, 100, 27),
            array(0, 0, 66), 
            array(56, 38, 58), 
            array(33, 100, 50), 
            array(328, 100, 54), 
            array(195, 100, 50), 
            array(180, 100, 27), 
            array(300, 100, 50), 
            array(84, 100, 59), 
           
        );
       // echo "running matching function";
        $mindistance=99999;
        $loc=999;
        for($i=0;$i<23;$i++){
            
            $temp=euler_distance($Hue,$Saturation,$Lightness,$hslcolorarray[$i][0],$hslcolorarray[$i][1],$hslcolorarray[$i][2]);
           // echo "<br>eulerian distance is ".$temp;
            if($temp<$mindistance){
               $mindistance=$temp;
               $loc=$i;
            }        
        }
       // echo "closest color is ".$loc;
        $matchedhue= $hslcolorarray[$loc][0];
        $matchedsaturation=$hslcolorarray[$loc][1];
        $matchedLightness=$hslcolorarray[$loc][2];
        echo "<div class='chips' style='background-color:hsl($matchedhue,$matchedsaturation%,$matchedLightness%);float:right;border:1px black solid; '></div><br><br>";
      //  echo "<br><br><br>";
     }

     function toHSL($red,$green,$blue){
        $rprime=$red/255;
        $gprime=$green/255;
        $bprime=$blue/255;
        $H=0;
        $S=0;
        $L=0;
        $CMax=max($rprime,$gprime,$bprime);
        $CMin=min($rprime,$gprime,$bprime);
        $delta=$CMax-$CMin;
        $L=($CMax+$CMin)/2;
        /*
        if($CMax==$rprime&&$delta!=0){
            
            echo $rprime."   ".$gprime."    ".$bprime;
            echo "delta is ".$delta;
            echo "<br>gprime -bprime is ".(($gprime-$bprime)/$delta)/6;
            

            $H=60*((($gprime-$bprime)/$delta)/6);

        }
        if($CMax==$gprime&&$delta!=0){
            $H=60*((($bprime-$rprime)/$delta)+2);
        }
        if($CMax==$bprime&&$delta!=0){
            $H=60*((($rprime-$gprime)/$delta)+4);
        }
       */
        //new algo to find hue is  http://en.wikipedia.org/wiki/Hue#Computing_hue_from_RGB

        if($red>=$green&&$green>=$blue){
            if(($red-$blue)==0){
                $H=0;
            }else{
                $H=60*($green-$blue)/($red-$blue);
            }
        }else
        if($red>=$blue&&$green>=$red){
            if(($green-$blue)==0){
                $H=0;
            }
            else{
            $H = 60*(2 - ($red-$blue)/($green-$blue));
            }
        }else
        if($blue>$red&&$green>=$blue){ 
            if(($green-$red)==0){
                $H=0;
            }
            else{
            $H = 60*(2 + ($blue-$red)/($green-$red)); 
            }
        }else
        if($blue>$green&&$green>$red){
            if(($blue-$red)==0){
                $H=0;
            } 
            else{
            $H = 60*(4 - ($green-$red)/($blue-$red));
            }
        }else
        if($blue>$red&&$red>=$green){ 
            if(($blue-$green)==0){
                $H=0;
            } 
            else{
            $H = 60*(4 + ($red-$green)/($blue-$green));
            }
         }else
         if($blue>$green&&$red>=$blue){
            if(($red-$green)==0){
                $H=0;
            } 
            else{
              $H = 60*(6- ($blue-$green)/($red-$green)); 
            }  
         }
        if($delta==0){
            $S=0;
        }
        if($delta!=0){
            $S=$delta/(1-abs(2*$L-1));
        }
        $S*=100;
        $L*=100;

       // echo "matched color from our library is <br>";
        matching($H,$S,$L);
     }
    

     function ColorDecoder($code){
        $red=($code-($code%1000000))/1000000;
        $green=($code%1000000-$code%1000)/1000;
        $blue=$code%1000;
        /*
        echo "Red: $red <br> ";
        echo "Green: $green<br>";
        echo "Blue: $blue";
        */
        echo "<div class='chips' style='background:rgb($red,$green,$blue);float:left;height:50px;width:100px;border:1px black solid;' <br><br></div>";
       // echo "converting RGB to HSL";
        toHSL($red,$green,$blue);    
     }
    // echo "1st color <br>";
     ColorDecoder($find1);
    // echo "<hr>";
    // echo "2nd color <br>";
     ColorDecoder($find2);
   //  echo "<hr>";
    // echo "3rd color <br>";
     ColorDecoder($find3);
    // echo "<hr>";
     //echo "4th color <br>";
     ColorDecoder($find4);
    // echo "<hr>";

     echo "</div>";
     echo "</div>";

?>
</body>
</html>