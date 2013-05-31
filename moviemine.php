<?php
// Casey Balzer
// cs1666
// 3-4-13

// u.data and u.item need to be in local directory to create tables

//negative table works well with support from 9-6% any higher no results
//any lower .... im sure it will work if ya got the time, I gave it 15mins 
//and still did not finish

//the positive table is working well from 25 on up to tested 50 support
//at 20 support it again takes too long, the php prob does not help

//input takes whole numbers to be converted to percentages later ex 40 = 40%

//ran out of time, sorry wish i could have done more with the css and untested 
//for different screen sizes/ hope it looks okay
//inputs must be correct no error checking like if mutiple checkboxes checked
//or bad inputs

//unfortunatly the combo table acts more as another positive table due to the fact you
//need min support of 9 to make the negative table work. The positive table gets too backed up at 25
// so when you get that low it may take all day to get results
//although I am sure that if you really want to wait it out, it will work, how long I dunno
 
 //future ideas ---- well if given more time, I am sure I could speed this up 
 //maybe even use an external script and eliminate the db
 // probs too many queries -->maybe better queries --> a better way to weed out bad combos
 
 
include "maketables.php";
error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('memory_limit', '350M');
ini_set('max_execution_time', 1200); //cant be too much more patient
$msg=null;
$out;
$tf=0;
if(isset($_POST['cmt']))//first couple are table functions
{

$msg=movietitle();
}

if(isset($_POST['pt']))
{
$msg=postable();
}

if(isset($_POST['nt']))
{
$msg=negtable();
}

if(isset($_POST['drop']))
{
$msg=dropall();
}

if(isset($_POST['sub2']))//mining function calls
{
    $tf=1;
    if(isset($_POST['pc']))//pos
    {
    $tf=2;
    
    $out=posrules($_POST['minsup'],$_POST['minconf'],$_POST['max']);
    }
    
    if(isset($_POST['nc']))//neg
    {
    $tf=3;
    
    $out=negrules($_POST['minsup'],$_POST['minconf'],$_POST['max']);
    }
    
    if(isset($_POST['cc']))//combo
    {
    $tf=4;
    
    $out=comborules($_POST['minsup'],$_POST['minconf'],$_POST['max']);
    }
    
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div class="head">
</br><big><big><center><h1>MovieMine</h1></center></big></big>
</div>
<div class="bdy">
<form action="moviemine.php" method="post">

<input type="submit" name="cmt" value="Create Movie Table"></br></br>
<input type="submit" name="pt" value="Create Pos Table"></br></br>
<input type="submit" name="nt" value="Create Neg Table"></br></br>
<input type="submit" name="drop" value="Drop All Tables"></br>
<?php
if(!is_null($msg))
{
echo @$msg;
}
?>

</form>
<div class="sel">
<form action="moviemine.php" method="post">
<center>MinSup<input type="text" name="minsup" >&nbsp;&nbsp;&nbsp;
MinConf<input type="text" name="minconf" ></center></br>
<center>MaxMovies<input type="text" name="max" ></center></br>
<center>POS<input type="checkbox" name="pc" value="1" >&nbsp;&nbsp;&nbsp;
NEG<input type="checkbox" name="nc" >&nbsp;&nbsp;&nbsp;
COMBO<input type="checkbox" name="cc" ></center></br>
<center><input type="submit" name="sub2" value="SUBMIT"></center>
</form>
</div>
<div class="mov">
<?php



$cnt=count(@$out);
if($tf==2)//positive table output flag
{
   for($i=0;$i<$cnt;$i++)
  {
     $cnt2=count($out[$i]);
       echo "<font color=\"green\">People who enjoyed watching</font> ";
       for($j=0;$j<$cnt2;$j++)
        {
         if($j+1==$cnt2)
          {
          echo "<font color=\"yellow\"> also reccommend watching</font><font color=\"red\"> ".$out[$i][$j]."!</font></br></br>";
          }
            else
            {
            echo "<font color=\"blue\">".$out[$i][$j].",</font>";
            } 
         }
  }
}
if($tf==3)//negative table output flag
{
   for($i=0;$i<$cnt;$i++)
  {
     $cnt2=count($out[$i]);
       echo "<font color=\"green\">People who disliked watching</font> ";
       for($j=0;$j<$cnt2;$j++)
        {
         if($j+1==$cnt2)
          {
          echo "<font color=\"yellow\"> also disliked watching</font><font color=\"red\"> ".$out[$i][$j]."!</font></br></br>";
          }
            else
            {
            echo "<font color=\"blue\">".$out[$i][$j].",</font>";
            } 
         }
  }
}
if($tf==4)//combo output
{
for($i=0;$i<$cnt;$i++)
  {
  $cnt2=count($out[$i]);
     for($j=0;$j<$cnt2;$j++)
        {
         $check=$out[$i][$j];
         $check2=explode("-",$check);
          if($check2[0]=="-")
           {
             $check=substr($check, 1);
             if($j+1==$cnt2)
              {
              echo "<font color=\"yellow\"> also disliked watching</font><font color=\"red\"> ".$check."!</font></br></br>";
              }
              else 
              {
              "<font color=\"yellow\">if disliked </font>".$check." ";
              }
           }
           else
           {
             
             if($j+1==$cnt2)
             {
             echo "<font color=\"green\"> also liked watching</font><font color=\"blue\"> ".$check."!</font></br></br>";
             }//end 
               else
               {
               echo "<font color=\"green\">if liked </font>".$check." ";
               }//end positive else
           }//end else
        }//end j for
  }//end i for

}//end 
?>
</div>
</div>

</body>
</html>