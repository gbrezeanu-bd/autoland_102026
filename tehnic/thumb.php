<?php
   //
   // proportional on-the-fly thumb generator from JPG images
   //
   // usage example: 
   // <img data-no-retina src= "thumb.php?src=pic.jpg&wmax=150&hmax=100&quality=90&bgcol=FF0000"> </img>
   //
   // parameters:  src = source image
   //              wmax = max width
   //              hmax = max height
   //              quality = JPG quality of generated thumb - optional. 
   //                        if not specified, quality=90
   //              bgcol = if specified, allways generates exact wmax x hmax sized thumb, 
   //                      with bacground color bgcol and centered source image 
   //
   // note: if source image is smaller than desired thumbnail, it will not be resized!

   header("Content-type: image/jpeg");
   $source = @imagecreatefromjpeg('../'.$_GET['src']);
//   $source = imagecreatefromjpeg($src);
   $orig_w=imagesx($source);
   $orig_h=imagesy($source);
   $wmax=$_GET['wmax'];
   $hmax=$_GET['hmax'];
   $quality=$_GET['quality'];
   $bgcol=$_GET['bgcol'];
   if ($orig_w>$wmax || $orig_h>$hmax)
   { 
       $thumb_w=$wmax;
       $thumb_h=$hmax;
       if ($thumb_w/$orig_w*$orig_h>$thumb_h) 
           $thumb_w=round($thumb_h*$orig_w/$orig_h); 
       else 
           $thumb_h=round($thumb_w*$orig_h/$orig_w);
   } else
   {
       $thumb_w=$orig_w;
       $thumb_h=$orig_h;
   }
   if (!@$bgcol)
   {
       $thumb=imagecreatetruecolor($thumb_w,$thumb_h);
       imagecopyresampled($thumb,$source,
                           0,0,0,0,$thumb_w,$thumb_h,$orig_w,$orig_h);
   }
   else
   {
       $thumb=imagecreatetruecolor($wmax,$hmax);
       imagefilledrectangle($thumb,0,0,$wmax-1,$hmax-1,intval($bgcol,16));
       imagecopyresampled($thumb,$source,
                           round(($wmax-$thumb_w)/2),round(($hmax-$thumb_h)/2),
                           0,0,$thumb_w,$thumb_h,$orig_w,$orig_h);
   }
   if (!@$quality) $quality=90;
   imagejpeg($thumb,"",$quality);
   //imagedestroy($thumb);
?>