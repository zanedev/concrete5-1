<?

defined('C5_EXECUTE') or die(_("Access Denied."));

set_time_limit(0);
$jobObj = Loader::model("job");
$outputDisabled=0;

if (!Job::authenticateRequest($_REQUEST['auth'])) {
	exit;
} 

//JSON vars
$jsonErrorCode=0;   // 0: Successful, 1: job not found
$jsonMessage='';
$jsonJHandle='';
$jsonJID=0;
$jsonJDateLastRun='';

//Uncomment to turn debugging on
//$_REQUEST['debug']=1;

//all print/echo statements are suppressed, unless the debug variable is set
if(!$_REQUEST['debug']) ob_start();


//run by job handle (aka file name) or run by job id
if( strlen($_REQUEST['jHandle'])>0 || intval($_REQUEST['jID'])>0 ){

	if($_REQUEST['jHandle']) 
		$jobObj=Job::getByHandle($_REQUEST['jHandle']);
	else 
		$jobObj=Job::getByID( intval($_REQUEST['jID']) );
		
	if(!$jobObj){ 
		$jsonErrorCode=1;
		$jsonMessage='Error: Job not found';
	}else{ 
	
		//Change Job Status
		if( $_REQUEST['jStatus'] ){
			$jobObj->setJobStatus( $_REQUEST['jStatus'] );
		//Run Job
		}else{
			$jsonMessage = $jobObj->executeJob(); 	
		}
		
		//set json vars
		$jsonJHandle = $jobObj->jHandle;	
		$jsonJDateLastRun = $jobObj->jDateLastRun;		
		$jsonJID=$jobObj->jID;			
		if( $jobObj->isError() ) 
			$jsonErrorCode = $jobObj->getError();		
	} 
	$runTime=date('n/j/y \a\t g:i A', strtotime($jsonJDateLastRun));
//run all jobs - default
}else{ 
	Job::runAllJobs();
	if(!$_REQUEST['debug']) 
		$outputDisabled=1;
	$jsonMessage='All Jobs Run Successfully';
	$runTime=date('n/j/y \a\t g:i A');
	$jsonJHandle ='All Jobs';	
}

//all print/echo statements are suppressed, unless the debug variable is set
if(!$_REQUEST['debug']){ 
	ob_end_clean();
}

//$runTime=strtotime($jsonJDateLastRun);
if(!$outputDisabled)
	echo '{error: '.intval($jsonErrorCode).',message:"'.addslashes($jsonMessage).'",jHandle:"'.$jsonJHandle.'",jID:'.$jsonJID.',jDateLastRun:"'.$runTime.'"}';
?>