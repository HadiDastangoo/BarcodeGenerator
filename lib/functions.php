<?php

//	Check if request is Ajax
function is_ajax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'));
}

// generate barcode url
function prepareBarcodeParameters($request)
{
	// set default
	$error = false;

	// request inputs
	$requestInputs = array_keys($request);

	// allowed inpuat fields
	$inputs = array(
		'bc_caption',
		'bc_serial',
		'bc_bulk_data',
		'bc_copyright',
		'bc_copyright_color',
		'bc_copyright_textcolor',
		'bc_copyright_bgcolor',
		'bc_width_scale',
		'bc_generate'
	);

	// echo "<pre>";
	// print_r($inputs);
	// echo "</pre><hr />";

	// echo "<pre>";
	// print_r($request);
	// echo "</pre><hr />";

	// get data
	foreach( $inputs as $input )
	{
		if ( in_array($input, $requestInputs) && !$error )
		{
			if ($input == 'bc_copyright_textcolor' || $input == 'bc_copyright_bgcolor')
			{
				if ($request['bc_copyright_color'] == 0)
				{
					$data[$input] = ($input == 'bc_copyright_textcolor') ? '000000' : 'ffffff';
				}
				else
				{
					$data[$input] = ltrim(trim($request[$input]), '#');
				}
			}
			elseif ( $input == 'bc_serial' )
			{
				if ( substr($request[$input], 0, 1) == '{' && substr($request[$input], -1) == '}' )
				{
					$n = (int) substr($request[$input], 1, strlen($request[$input]) - 1);

					if (1 <= $n && $n <= 20) 
					{
						$data['bc_serial'] = rand(1,9);
						for ($i = 1; $i < $n; $i++)
						{
							$data['bc_serial'] .= rand(0,9);
						}
					}
					else
					{
						$error = true;
						$errorMessage = 'Serial is invalid ({n} must be between 1 to 20).';
					}
				}
				else
				{
					$data[$input] = $request[$input];
				}
			}
			else {
				$data[$input] = trim($request[$input]);
			}
		}
		else
		{
			$error = true;
			$errorMessage = isset($errorMessage) ? $errorMessage : 'Invalid input field/data.';
		}
	}

	return $error ? array('error' => true, 'errorMessage' => $errorMessage) : $data;

}

// ++++++++++++++++++++++++++++++++++++++
//
//	Generate Barcode URI Address
//
// ++++++++++++++++++++++++++++++++++++++
function generateBarcodeURI($data, $barcodePrefix = 'barcode_')
{
	if ( !isset($data['error']) )
	{
		// create image url
		$imageURI = "{$barcodePrefix}{$data['bc_serial']}.png?" . uniqid();
		$imageURI .= "&text={$data['bc_serial']}&caption={$data['bc_caption']}&footer={$data['bc_copyright']}&prefix=&f_textcolor={$data['bc_copyright_textcolor']}&f_bgcolor={$data['bc_copyright_bgcolor']}&wscale={$data['bc_width_scale']}";
	}

	return isset($imageURI) ? $imageURI : false;
}


function zipFolder( $path )
{
	// Initialize archive object
	$zip = new ZipArchive();

	$zipFile = './cache/archive/barcodes_' . date('Y-m-d_his_') . time() . rand(100, 999) . '.zip';
	$zip->open( $zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE );

	// Create recursive directory iterator
	$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::LEAVES_ONLY );

	foreach ($files as $name => $file)
	{
	    // Skip directories (they would be added automatically)
	    if (!$file->isDir())
	    {
	        // Get real and relative path for current file
	        $filePath = $file->getRealPath();
	        $relativePath = substr($filePath, strlen($path) + 1);

	        // Add current file to archive
	        $zip->addFile($filePath, $relativePath);
	    }
	}

	// Zip archive will be created only after closing object
	$zip->close();

	return $zipFile;
}
