<?php
/**
 * demo_view.php along with demo_list.php provides a sample web application
 * 
 * @package nmListView
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.10 2012/02/28
 * @link http://www.newmanix.com/
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see demo_list.php
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
 
# check variable of item passed in - if invalid data, forcibly redirect back to demo_list.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
	 $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
	myRedirect(VIRTUAL_PATH . "surveys/index.php");
    //header("Location:" . VIRTUAL_PATH . "demo/demo_list.php");
}

//---end config area --------------------------------------------------

$mySurvey = new Survey($myID);
dumpDie($mySurvey);

if($mySurvey->isValid)
{#only load data if record found
	$config->titleTag = $mySurvey->Title . " surveys made with PHP & love!"; #overwrite PageTitle with Muffin info!
}

# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center"><?=$mySurvey->Title?></h3>

<?php
if($mySurvey->isValid)
{#records exist - show muffin!
//create survey object?
 
    echo '<p>Title: ' . $mySurvey->Title . '</p>';
    echo '<p>Description: ' . $mySurvey->Description . '</p>';
    
}else{//no such survey!
    echo '<div align="center">What! No such survey? There must be a mistake!!</div>';
    echo '<div align="center"><a href="' . VIRTUAL_PATH . 'surveys/index.php">Another Survey?</a></div>';
}

get_footer(); #defaults to theme footer or footer_inc.php

class Survey
{
    public $SurveyID = 0;
    public $Title = '';
    public $Description = '';
    public $isValid = false;
    public $Questions = array();
    
    public function __construct($id)
    {
        $this->SurveyID = (int)$id; 
        
        $sql = "select * from wn17_surveys where SurveyID=" . (int)$id;
        
        # connection comes first in mysqli (improved) function
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        if(mysqli_num_rows($result) > 0)
        {#records exist - process
               $this->isValid = true;	
               while ($row = mysqli_fetch_assoc($result))
               {

                   $this->Title = dbOut($row['Title']);
                   $this->Description = dbOut($row['Description']);

               }
        }

        @mysqli_free_result($result); # We're done with the data!
        
        
      //question array starts here
        
        
        //Select QuestionID, Question, Description from wn17_questions where SurveyID = 1
        $sql = "Select QuestionID, Question, Description from wn17_questions where SurveyID =" . (int)$id;
        
        # connection comes first in mysqli (improved) function
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        if(mysqli_num_rows($result) > 0)
        {#records exist - process
               //$this->isValid = true;	
               while ($row = mysqli_fetch_assoc($result))
               {

                   //$this->Title = dbOut($row['Title']);
                   //$this->Description = dbOut($row['Description']);
                   $this->Questions[] = new Question($row['QuestionID'],dbOut($row['Question']),dbOut($row['Description']));

               }
        }

        @mysqli_free_result($result); # We're done with the data! 
        
        
        
        
      //end of question array  
        
    }#end Survey Constructor
    
    
}#end Survey Class
    
class Question
{
    public $QuestionID = 0;
    public $Text = '';
    public $Description = '';    
    
    public function __construct($QuestionID,$Text,$Description)
    {
        $this->QuestionID = (int)$QuestionID;
        $this->Text = $Text;
        $this->Description = $Description;
        
    }#end Question constructor
    
    
    
}#end Question class 