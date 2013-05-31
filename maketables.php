<?php
//Casey Balzer
//cs1666
//3-4-13
 error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('max_execution_time', 1200); 
  $hostname = 'localhost';//credentials 
  $username = "root";
  $dbname = "root";
  $password = "root";
    
    $movienames=array();
    $movienumbers=array();        
  $cflag=0;
       $tblstr;
    
  //Connecting to database
  $con = mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
  connect to database! Please try again later.");
  mysql_select_db($dbname);

 function movietitle(){//create movie titles to id db
global $movienumbers, $movienames, $con;

$handle = fopen("u.item", "r");

       if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $temp=explode("|", $buffer);
            array_push($movienumbers, $temp[0]);
            $temp2=htmlspecialchars($temp[1], ENT_QUOTES);
            array_push($movienames, $temp2);
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}//end if

                  $sql = "CREATE TABLE movietitle 
                   (
                  movieID int NOT NULL,
                  PRIMARY KEY(movieID),
                  title varchar(50)
                     )";

                    mysql_query($sql,$con) or die ("Table NOT created!");
                   
                   $result = count($movienumbers);

                         for($i=0;$i<$result;$i++)
                            {
                            $mn=$movienumbers[$i];
                            $mt=$movienames[$i];
                            mysql_query("INSERT INTO movietitle (movieID, title)
                            VALUES ('$mn', '$mt')") or die ("Could not insert into table!");
                             }//end for

                              $msg =  "\n Table Created!";
return $msg;


}//end movietitle


function postable(){//creates the positve table for the db
global  $con;
   $posuser=array();
   $posmid=array();
  ini_set('max_execution_time', 0);
  ini_set('memory_limit', '256M');
$handle = fopen("u.data", "r");

if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        //$temp=explode(" ", $buffer);
           $temp=preg_split('/\s+/', $buffer); 
            $rate=$temp[2];
        
            if($rate > 3){//push values for pos table
            
            array_push($posuser, $temp[0]);
            array_push($posmid, $temp[1]);
            }//end pos if
        }//end while
    
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}//end if
$sql="DROP TABLE IF EXISTS  postable";
mysql_query($sql,$con) or die (mysql_error()) ;
     $sql = "CREATE TABLE postable
                   (
                  id MEDIUMINT NOT NULL AUTO_INCREMENT,
                  userID int,
                  movieID int,
                  PRIMARY KEY(id)
                     )";
                     
mysql_query($sql,$con) or die (mysql_error()) ;
                   
                   $result = count($posuser);
                       
                         for($i=0;$i<$result;$i++)
                            {
                            $un=$posuser[$i];
                            $pmid=$posmid[$i];
                            mysql_query("INSERT INTO postable (userID, movieID)
                            VALUES ('$un', '$pmid')") or die ("Could not insert into postable!");
                             }//end for

                                $msg = "\n postable Created!";
return $msg;

}//end postable function
function negtable(){//creates the neg table for db

global  $con;
   $neguser=array();
   $negmid=array();   
 $handle = fopen("u.data", "r");

if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        //$temp=explode(" ", $buffer);
           $temp=preg_split('/\s+/', $buffer); 
            $rate=$temp[2];
    
    if($rate < 3)//push values for neg table
            {
            array_push($neguser, $temp[0]);
            array_push($negmid, $temp[1]);
            }
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
    
    
}//end if

 $sql = "CREATE TABLE negtable
                   (
                  id MEDIUMINT NOT NULL AUTO_INCREMENT,
                  userID int,
                  movieID int,
                  PRIMARY KEY(id)
                     )";
                     
                mysql_query($sql,$con) ;

                   $result = count($neguser);

                         for($i=0;$i<$result;$i++)
                            {
                            $un=$neguser[$i];
                            $nmid=$negmid[$i];
                            mysql_query("INSERT INTO negtable (userID, movieID)
                            VALUES ('$un', '$nmid')") or die ("Could not insert into negtable!");
                             }//end for

                                $msg = "\n negtable Created!";
                                return $msg;
}//end negtable

function dropall(){//drops all tables
global $con;
$sql="DROP TABLE IF EXISTS  postable";
mysql_query($sql,$con) or die (mysql_error()) ;

$sql="DROP TABLE IF EXISTS  negtable";
mysql_query($sql,$con) or die (mysql_error()) ;

$sql="DROP TABLE IF EXISTS  movietitle";
mysql_query($sql,$con) or die (mysql_error()) ;

$msg = "\n All Tables Dropped!";
return $msg;
}

function posrules($ms,$mc,$max){//creates positve table rules
global $con;
$ut=943;
$ms=$ms/100;
$setr=array();
$mc=$mc/100;
$movid=array();
$totsup=array();            
$res=mysql_query("select movieID, count(movieID) as tot from postable group by movieID");
 
    while($row = mysql_fetch_assoc($res, MYSQL_NUM)){//weeding out the first list 
           //echo "here!\n";
           $mid=$row[0];
            $tl=$row[1];
              $tempms=$tl/$ut;
              if($tempms>=$ms){ //checking initial min support             
              
               array_push($movid, $mid);//made it push to new list
               array_push($totsup, $tl);
               }//end if        
    }//end while
    $set=powerset($movid,$max);//get all subsets from the existing set
   $a= count($set);
     for($i=0;$i<$a;$i++)
     {
     $b=count($set[$i]);
        $s=array();
        $s1=array();              //querying sets to weed out
       $string = "SELECT count(userID) as tot FROM postable WHERE movieID IN ("; 
        for($j=0;$j<$b;$j++)//build query
           {
           array_push($s, $set[$i][$j]);
           $string=$string.$set[$i][$j];
              if($j+1 < $b)//add comma
               {
               $string=$string.",";
               }
           }//end build query
           $string=$string.")";
           $res=mysql_query($string);
           $row = mysql_fetch_assoc($res, MYSQL_NUM);
           $c=$row[0];
           $c=$c/$ut;       
               if($c>=$ms)
                 {
                     //echo "there! ";
                     for($k=0;$k<$max;$k++)
                      {
                       //echo " mmm ".$s[$k];
                      $string="select title from movietitle where movieID=".$s[$k];
                      $res=mysql_query($string);
                      $row = mysql_fetch_assoc($res, MYSQL_NUM);
                         //echo " mmm ".$row[0];
                         
                         $temp=$row[0];
                         array_push($s1, $temp);
                       
                        
                      
                      }
                      array_push($setr, $s1);
                 }
     }
    
    return $setr;
}

function powerSet($in,$minLength) { 
   $count = count($in); 
   $members = pow(2,$count); 
   $return = array(); 
   for ($i = 0; $i < $members; $i++) { 
      $b = sprintf("%0".$count."b",$i); 
      $out = array(); 
      for ($j = 0; $j < $count; $j++) { 
         if ($b{$j} == '1') $out[] = $in[$j]; 
      } 
      if (count($out) == $minLength) { //this line if modified will change output
         $return[] = $out;              //change == to =< if want all combos under max 
      } 
   } 
   return $return; 
} 

function negrules($ms,$mc,$max){// the negative rules same as positive except from negtable
global $con;
$ut=943;
$ms=$ms/100;
$setr=array();
$mc=$mc/100;
$movid=array();
$totsup=array();
$res=mysql_query("select movieID, count(movieID) as tot from negtable group by movieID");
 
    while($row = mysql_fetch_assoc($res, MYSQL_NUM)){
           //echo "here!\n";
           $mid=$row[0];
            $tl=$row[1];
              $tempms=$tl/$ut;
              if($tempms>=$ms){              
               array_push($movid, $mid);
               array_push($totsup, $tl);
               }//end if        
    }//end while
    $set=powerset($movid,$max);
   $a= count($set);
     for($i=0;$i<$a;$i++)
     {
     $b=count($set[$i]);
        $s=array();
        $s1=array();
       $string = "SELECT count(userID) as tot FROM negtable WHERE movieID IN ("; 
        for($j=0;$j<$b;$j++)//build query
           {
           array_push($s, $set[$i][$j]);
           $string=$string.$set[$i][$j];
              if($j+1 < $b)//add comma
               {
               $string=$string.",";
               }
           }//end build query
           $string=$string.")";
           $res=mysql_query($string);
           $row = mysql_fetch_assoc($res, MYSQL_NUM);
           $c=$row[0];
           $c=$c/$ut;       
               if($c>=$ms)
                 {
                     //echo "there! ";
                     for($k=0;$k<$max;$k++)
                      {
                       //echo " mmm ".$s[$k];
                      $string="select title from movietitle where movieID=".$s[$k];
                      $res=mysql_query($string);
                      $row = mysql_fetch_assoc($res, MYSQL_NUM);
                         //echo " mmm ".$row[0];
                         
                         $temp=$row[0];
                         array_push($s1, $temp);
                       
                        
                      
                      }
                      array_push($setr, $s1);
                 }
     }
    
    return $setr;
}
 function comborules($ms,$mc,$max) // combo rules same as above but need a split by if else for chance no negitives made list
 {//to build proper strings to query .... ie an all positive query is different than combo query
 
 
 //SELECT count(postable.userID) as cnt from postable, negtable where postable.userID=negtable.userID and postable.movieID='1' and negtable.movieID=4 
 global $con,$cflag;
$ut=943;
$ms=$ms/100;
$setr=array();
$mc=$mc/100;
$movidp=array();
$totsupp=array();
$movidn=array();
$totsupn=array();
$newp=array();
$newn=array();
$newsup=array();
$res=mysql_query("select movieID, count(movieID) as tot from postable group by movieID");
 //got to get list of all positive movies and negatives weed out and push together 
    while($row = mysql_fetch_assoc($res, MYSQL_NUM)){
           //echo "here!\n";
           $mid=$row[0];
            $tl=$row[1];
              $tempms=$tl/$ut;
              if($tempms>=$ms){              
            
               array_push($movidp, $mid);
               array_push($totsupp, $tl);
               }//end if        
    }//end while
 $res=mysql_query("select movieID, count(movieID) as tot from negtable group by movieID");
 
    while($row = mysql_fetch_assoc($res, MYSQL_NUM)){
           //echo "here!\n";
           $mid=$row[0];
            $tl=$row[1];
              $tempms=$tl/$ut;
              if($tempms>=$ms){              
                $cflag=1;
               array_push($movidp, -1*$mid);
               array_push($totsupp, $tl);
               }//end if        
    }//end while
  
      $set=powerset($movidp,$max);
      $a= count($set);
     if($cflag==1)//detects if a negative made the list -different query-
     {
     for($i=0;$i<$a;$i++)
     {
     $b=count($set[$i]);
        $s=array();
        $s1=array();
        $sneg=array();
       /*SELECT count(postable.userID) as tot
  FROM postable
  JOIN negtable
    ON postable.userID = negtable.userID
 WHERE postable.movieID IN (1, 3) and negtable.movieID IN (112)*/
       $tem;
       $string= "SELECT count(postable.userID) as tot FROM postable JOIN negtable ON postable.userID = negtable.userID WHERE postable.movieID IN (";
        for($j=0;$j<$b;$j++)//build query
           {
           if($set[$i][$j] < 0)
           {
           $tem=$set[$i][$j]*-1;
           array_push($sneg, $tem);
           }//end if
           else
           {
           $string=$string.$set[$i][$j];
           if($j+1 < $b)//add comma
               {
               $string=$string.",";
               }
           }//end else
           array_push($s, $set[$i][$j]);
           
        
         
           }//end build query
           $string=$string.") and negtable.movieID IN (";
           $countneg=count($sneg);
           echo " count ".$countneg." "; 
                 for($h=0;$h<$countneg;$h++)
                 {
                 $tn=$sneg[$h];
                   $string=$string.$tn;
                   if($h+1 < $countneg)//add comma
                   {
                    $string=$string.",";
                   }//end if
                   
                 } //end neg for
                 $string=$string.")";
           echo " ".$string." ";
           $res=mysql_query($string);
           @$row = mysql_fetch_assoc($res, MYSQL_NUM);
           $c=$row[0];
           $c=$c/$ut;       
               if($c>=$ms)
                 {
                     //echo "there! ";
                     for($k=0;$k<$max;$k++)
                      {
                       //echo " mmm ".$s[$k];
                      $cn=$s[$k];
                      if($cn < 0)
                      {
                      $cn=$cn*-1;
                      $string="select title from movietitle where movieID=".$cn;
                      $res=mysql_query($string);
                      $row = mysql_fetch_assoc($res, MYSQL_NUM);
                         //echo " mmm ".$row[0];
                         
                         $temp=$row[0];
                         $tp="-".$temp;
                         array_push($s1, $tp);
                       }
                       else
                       {
                       
                      $string="select title from movietitle where movieID=".$cn;
                      $res=mysql_query($string);
                      $row = mysql_fetch_assoc($res, MYSQL_NUM);
                         //echo " mmm ".$row[0];
                         
                         $temp=$row[0];
                         array_push($s1, $temp);
                       
                       }
                        
                      
                      }
                      array_push($setr, $s1);
                 }
     }
    }//end if $cflag ----> if any neg movies made it
         else//no negatives made it just do a positive query
         {
         for($i=0;$i<$a;$i++)
     {
     $b=count($set[$i]);
        $s=array();
        $s1=array();
       $string = "SELECT count(userID) as tot FROM postable WHERE movieID IN ("; 
        for($j=0;$j<$b;$j++)//build query
           {
           array_push($s, $set[$i][$j]);
           $string=$string.$set[$i][$j];
              if($j+1 < $b)//add comma
               {
               $string=$string.",";
               }
           }//end build query
           $string=$string.")";
           $res=mysql_query($string);
           $row = mysql_fetch_assoc($res, MYSQL_NUM);
           $c=$row[0];
           $c=$c/$ut;       
               if($c>=$ms)
                 {
                     //echo "there! ";
                     for($k=0;$k<$max;$k++)
                      {
                       //echo " mmm ".$s[$k];
                      $string="select title from movietitle where movieID=".$s[$k];
                      $res=mysql_query($string);
                      $row = mysql_fetch_assoc($res, MYSQL_NUM);
                         //echo " mmm ".$row[0];
                         
                         $temp=$row[0];
                         array_push($s1, $temp);
                       
                        
                      
                      }
                      array_push($setr, $s1);
                 }
     }
         }
         $cflag=0;
    return $setr;
 }      
?>

