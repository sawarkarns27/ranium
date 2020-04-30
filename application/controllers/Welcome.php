<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		$this->load->view('welcome_message');    

	}

	function getObjects()
	{
		$start_date 					= 	date('Y-m-d',strtotime($this->input->post('start_date')));
		$end_date 						= 	date('Y-m-d',strtotime($this->input->post('end_date')));
		
		$response_a 					= 	$this->getAsteroidsObjects($start_date,$end_date);
		if(!$response_a['near_earth_objects']){
			$this->session->set_flashdata('msg', 'Invalid response, please select valid dates');
			redirect(site_url());
		}
        $asteroids 						=	$response_a['near_earth_objects'][$end_date];
        $asteroidsId 					=	array_column($asteroids,'id');
        $closestData 					=	array_map(function($x){ return $x[0]; }, array_column($response_a['near_earth_objects'][$end_date],'close_approach_data'));
        $closestRelativeV 				=	array_column(array_column($closestData,'relative_velocity'),'kilometers_per_hour');
        $fastestKm 						=	array_keys($closestRelativeV,max($closestRelativeV));
        $fastestAsteroidId 				=	$asteroidsId[$fastestKm[0]];
        $fastestAsteroidSpeed 			=	round(max($closestRelativeV),2);


        $closestDistance 				=	array_column(array_column($closestData,'miss_distance'),'kilometers');
        $closestKm 						=	array_keys($closestDistance,min($closestDistance));
        $closetAsteroidId 				=	$asteroidsId[$closestKm[0]];
        $closestAsteroidDistance 		=	round(min($closestDistance),2);
        $passArray 						= 	array(
					        						'fastestAsteroidId'		=>	$fastestAsteroidId,
					        						'fastestAsteroidSpeed'	=>	$fastestAsteroidSpeed,
					        						'closetAsteroidId'		=>	$closetAsteroidId,
					        						'closestAsteroidDistance'=>	$closestAsteroidDistance,
					        					);
        redirect(site_url().'?fastestAsteroidId='.$fastestAsteroidId.'&fastestAsteroidSpeed='.$fastestAsteroidSpeed.'&closetAsteroidId='.$closetAsteroidId.'&closestAsteroidDistance='.$closestAsteroidDistance.'&start_date='.$start_date.'&end_date='.$end_date);
	}

	function createDateRange()
	{
		$format 						= 	"d-m-Y";
	    $begin 							= 	new DateTime($this->input->post('start_date'));
	    $end 							= 	new DateTime($this->input->post('end_date'));
	 
	    $interval 						= 	new DateInterval('P1D'); // 1 Day
	    $dateRange 						= 	new DatePeriod($begin, $interval, $end);
	 
	    $range 							= 	[];
	    $element_count 					= 	[];
	    foreach ($dateRange as $date) {
	        $range[] 					= 	$date->format($format);
	        $getAsteroidsObjects 		= 	$this->getAsteroidsObjects($date->format('Y-m-d'));
	        $element_count[] 			= 	$getAsteroidsObjects['element_count'];
	    }
	 	array_push($range,date('d-m-Y',strtotime($this->input->post('end_date'))));
	 	$lastgetAsteroidsObjects 		= 	$this->getAsteroidsObjects($this->input->post('end_date'));
	    array_push($element_count,$lastgetAsteroidsObjects['element_count']);
	    $data['range'] 					= 	$range;
	    $data['element_count'] 			= 	$element_count;
	    echo json_encode($data);
	}

	function getAsteroidsObjects($start_date,$end_date='')
	{
		if($end_date){
			$url = 'https://api.nasa.gov/neo/rest/v1/feed?start_date='.$start_date.'&end_date='.$end_date.'&api_key=cQ5qofepkHyTZbrPfFEwe56TjcbVd6Xf2HxbKeJ1';
		}else{
			$url = 'https://api.nasa.gov/neo/rest/v1/feed?start_date='.$start_date.'&api_key=cQ5qofepkHyTZbrPfFEwe56TjcbVd6Xf2HxbKeJ1';
		}

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        return $response_a;
	}

}









