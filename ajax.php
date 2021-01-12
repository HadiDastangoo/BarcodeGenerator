<?php
// requirements
require('config.php');

// Manage AJAX Requests
if ( is_ajax() )
{
	$data = prepareBarcodeParameters($_POST);

	if ( !isset($data['error']) )
	{
		$response['error'] = false;
		$response['result'] = generateBarcodeURI($data);
	}
	else
	{
		$response['error'] = true;
		$response['result'] = isset($data['errorMessage']) ? $data['errorMessage'] : 'Unknown error occurd.';
	}
}
else
{
	$response['error'] = true;
	$response['result'] = 'Invalid request.';
}

// print out message
echo json_encode($response);
