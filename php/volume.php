<!--
Name: stlvolume.php

Date: July 17, 20

URL: http://kindle-new.blogspot.it/2012/10/estimate-volume-of-stl-file-in-php.html

reference: http://schmidtcds.com/2011/07/php-stl-files/

Note: for binary stl
-->
<title>PHP - Binary STL volume</title>
<?php



function stlhandling($volume=0,$toobig=0,$filepath){
$x_max = 0;
$y_max = 0;
$z_max = 0;
$x_min = 0;
$y_min = 0;
$z_min = 0;
//$filepath = "4.stl";
//$filepath = "k.stl";
$fp = fopen($filepath, "rb");

$section = file_get_contents($filepath, NULL, NULL, 0, 79);

$sizeoff=filesize( $filepath );

fseek($fp, 80);  
$data = fread($fp, 4);

$numOfFacets = unpack("I", $data);
//echo $numOfFacets[1];
$pointer=12.5*$i+4;
$binary=1;
if(($numOfFacets[1]*12.5)>$sizeoff)$binary=0;

if($binary==0){fseek($fp, 0);
fgets($fp,100);
fgets($fp,100);
fgets($fp,100);
echo "ascii";
}

echo "$numOfFacets[1] ".$numOfFacets[1]."\n<br>";
for ($i = 0; $i < $numOfFacets[1]; $i++){
    //Start Normal Vector
  if($binary==1){
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $normalVectorsX[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $normalVectorsY[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $normalVectorsZ[$i] = $hold[1];
    //End Normal Vector
    //Start Vertex1
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex1X[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex1Y[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex1Z[$i] = $hold[1];
    //End Vertex1
    //Start Vertex2
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex2X[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex2Y[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex2Z[$i] = $hold[1];
    //End Vertex2
    //Start Vertex3
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex3X[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex3Y[$i] = $hold[1];
    $data = fread($fp, 4);
    $hold = unpack("f", $data);
    $vertex3Z[$i] = $hold[1];
    //End Vertex3
    //Attribute Byte Count
    $data = fread($fp, 2);
    $hold = unpack("S", $data);
    $abc[$i] = $hold[1];
   }
   if($binary==0){

    //echo substr(fgets($fp,100),13);
    $vertexarray1=explode(" ",substr(fgets($fp,150),13));
    $vertexarray2=explode(" ",substr(fgets($fp,150),13));
    $vertexarray3=explode(" ",substr(fgets($fp,150),13));
    //echo "\n<br>k".floatval($vertexarray1[0]);
    //echo "\n".fgets($fp,100);
    $vertex1X[$i]=$vertexarray1[0];
    $vertex1Y[$i]=$vertexarray1[1];
    $vertex1Z[$i]=$vertexarray1[2];
    $vertex2X[$i]=$vertexarray2[0];
    $vertex2Y[$i]=$vertexarray2[1];
    $vertex2Z[$i]=$vertexarray2[2];
    $vertex3X[$i]=$vertexarray3[0];
    $vertex3Y[$i]=$vertexarray3[1];
    $vertex3Z[$i]=$vertexarray3[2];
    fgets($fp,80);
    fgets($fp,80);
    $ko=fgets($fp,80);
    //echo substr($ko,0,8)."\n<br>";
    if(substr($ko,0,8)=="endsolid")$numOfFacets[1]=$i-1;//found the end of the file
    fgets($fp,190);

  }
    
    $x_vals = array($vertex1X[$i], $vertex2X[$i], $vertex3X[$i]);
    $y_vals = array($vertex1Y[$i], $vertex2Y[$i], $vertex3Y[$i]);
    $z_vals = array($vertex1Z[$i], $vertex2Z[$i], $vertex3Z[$i]);
    if(max($x_vals) > $x_max) {
        $x_max = max($x_vals);
    }
    if(max($y_vals) > $y_max) {
        $y_max = max($y_vals);
    }    
    if(max($z_vals) > $z_max) {
        $z_max = max($z_vals);
    }    
    if(min($x_vals) < $x_min) {
        $x_min = min($x_vals);
    }
    if(min($y_vals) < $y_min) {
        $y_min = min($y_vals);
    }    
    if(min($z_vals) < $z_min) {
        $z_min = min($z_vals);
    }    
    
}
$x_dim = $x_max - $x_min;
$y_dim = $y_max - $y_min;
$z_dim = $z_max - $z_min;

$volume = $x_dim*$y_dim*$z_dim;

$raw_cost = 15;
$tray_cost = $raw_cost;
$material_cost = $raw_cost*$volume*1.02;
$support_cost = $raw_cost*2;
$total = $tray_cost + $material_cost + $support_cost;
echo "total $ ".number_format($total, 2, '.', ',')."\n<br>";
$fits=0;
if($x_dim<140 && $x_dim<140 && $z_dim<90)
    $fits=1;
if($x_dim<140 && $x_dim<90 && $z_dim<140)
    $fits=1;
if($x_dim<90 && $x_dim<140 && $z_dim<140)
    $fits=1;
$volumeovercharge=$volume/30000;


$volumeovercharge=round($volumeovercharge,0);


//is volume < 0.5
if($volumeovercharge==0 && $volume>0)$volumeovercharge=1;
if($fits==0)$volumeovercharge=-1;
echo "\n<br>x_dim ".$x_dim;echo "\n<br>";
echo "volume $ ".number_format($volume, 2, '.', ',');
echo "\n<br>y_dim ".$y_dim;
echo "\n<br>z_dim ".$z_dim;
echo "\n<br>fits ".$fits;
echo "\n<br>volumeovercharge k ".$volumeovercharge;

return $volumeovercharge;
}
stlhandling(0,0,"ascii_1_hole.stl");
?>