<?php
// load main barcode class
require('class.barcode.php');

// check if form submitted
if ( isset($_POST['bc_generate']) )
{

	unset($_SESSION['sample_image_preview']);

	if ( isset($_POST['bc_bulk_data']) && trim($_POST['bc_bulk_data']) !== '')
	{
		// prepare data
		$data = prepareBarcodeParameters($_POST);

		if ( !isset($data['error']) )
		{
			// create directory to save barcodes
			$dir = '/cache/images/' . date("Y-m-d/H-i-s") . '_' . rand(100000, 999999) . '/';
			$realPath = __DIR__ . '/..' . $dir;

			if ( mkdir( $realPath, 0777, true ) )
			{
				// get all data into lines
				$data['bc_bulk_data'] = trim($data['bc_bulk_data']);
				$dataLines = explode("\n", $data['bc_bulk_data']);

				// count lines
				$linesCount = sizeof($dataLines);
				$ignoredLines = array();

				// total
				$i = 0;

				// successful
				$j = 0;

				// repeated
				$r = 0;

				// define serial array
				$serialsArray = array();

				// get data line by line
				foreach($dataLines as $dataLine)
				{					
					$i++;
					$line = explode(',', trim($dataLine, ','));
					$lineArrayCount = sizeof($line);

					if ( ($lineArrayCount == 1 && isset($line[0]) && trim($line[0]) !== '') || $lineArrayCount == 2 )
					{
						// set caption & serial
						list($data['bc_caption'], $data['bc_serial']) = ( $lineArrayCount == 2 ) ? array(trim($line[0]), trim($line[1])) : array('', trim($line[0]));

						// search if serial is repeated
						if ( !in_array($data['bc_serial'], $serialsArray) )
						{
							$j++;							
							$barcode = new barcode($data['bc_serial'], '', $data['bc_caption'], $data['bc_copyright'], $data['bc_copyright_textcolor'], $data['bc_copyright_bgcolor'], $data['bc_width_scale']);
							$barcode->createBarcode();
							$barcode->saveBarcode($realPath);
							$_SESSION['sample_image_preview'] = !isset($_SESSION['sample_image_preview']) ? '.' . $dir . $barcode->fileName : $_SESSION['sample_image_preview'];
							unset($barcode);

							// index saerial to array
							$serialsArray[] = !in_array($data['bc_serial'], $serialsArray) ? $data['bc_serial'] : '';
						}
						else
						{
							// repeated serial
							$r++;
							$ignoredLines[] = $i;
						}
					}
					else
					{
						$ignoredLines[] = $i;
					}
				}
			}
			else
			{
				$_SESSION['error'] = 'Could not make directory. (Err. #' . __LINE__ . ')';
			}

			// create log to dispalay
			$k = sizeof($ignoredLines);
			$log = "<strong>$j barcode" . ($j > 1 ? 's' : '') . " generated " . (($j > 0) ? 'successfully' : '') . "</strong> (from $i non-empty line" . ($i > 1 ? 's' : '') . ").";
			if ( $k !== 0 )
			{
				$log .= "<br /><small>Ignored/Syntax Error Lines: <strong>" . $k . " line" . ($k > 1 ? 's' : '') . "</strong><br />";
				$log .= "<em>Ignored lines: ";
				foreach( $ignoredLines as $ignoredLine )
					$log .= '#' . $ignoredLine . ', ';
				$log = rtrim($log, ', ');
				$log .= (($r > 0) ? " ($r serial" . (($r > 1) ? 's are' : ' is') . " repeated)" : '') . "</em></small>";
			}

			// set session
			$_SESSION['success'] = $log;

			// create zip file
			if ( $j > 0 ) $_SESSION['zip_file_url'] = zipFolder( $realPath );
		}
		else
		{
			$_SESSION['error'] = $data['errorMessage'] . ' (Err. #' . __LINE__ . ')';
		}
	}
	else
	{
		$_SESSION['error'] = 'No bulk data submitted. (Err. #' . __LINE__ . ')';
	}
}
else
{
	$_SESSION['error'] = 'No bulk data submitted. (Err. #' . __LINE__ . ')';
}

$_SESSION['bulk_tab'] = true;
