<?php
//	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//
//		"BARCODE GENERATOR"
//		A new Barcode Generator, single or in a bulky way!
//		Author: Hadi Dastangoo <h.dastangoo@gmail.com>
//
//	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// requirements
require('config.php');

// Manage POST Request
if ( isset($_POST['bc_generate']) )
{
	include('lib/formProcessor.php');
}
?>
<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Barcode Generator</title>
		<link rel="apple-touch-icon" sizes="180x180" href="./images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="./images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="./images/favicon-16x16.png">
		<link rel="manifest" href="./images/site.webmanifest">
		<link rel="stylesheet" href="./css/bootstrap-4.5.0.min.css">
		<link rel="stylesheet" href="./css/bootstrap-colorpicker.min.css">
		<link rel="stylesheet" href="./css/ispinner.prefixed.css">
		<link href="https://fonts.googleapis.com/css2?family=Harmattan&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="./css/common.css">
	</head>
	<body>

		<nav class="navbar navbar-dark bg-dark">

			<header class="container">
				<a class="navbar-brand" href="./">
					<img src="./images/favicon-32x32.png" width="30" height="30" class="d-inline-block" alt="">&nbsp;
					<?php echo APP_NAME; ?> <small class="text-muted">v. <?php echo APP_VERSION; ?></small>
				</a>
			</header>

		</nav>

		<div class="container mt-4">

<?php
if ( isset($_SESSION['error']) || isset($_SESSION['success']) )
{
?>
			<div class="row mt-3">
				<div class="col-md-12 alert alert-<?php echo isset($_SESSION['success']) ? 'success' : 'danger'; ?>">
					<?php echo isset($_SESSION['success']) ? $_SESSION['success'] : $_SESSION['error']; ?>
					<?php
					if (isset($_SESSION['zip_file_url']))
					{
					?>
						<br /><a href="<?php echo $_SESSION['zip_file_url']; ?>" class="btn btn-primary btn-sm mt-3">Download .zip File</a>
					<?php
					}
					?>
				</div>
			</div>
<?php
	if(isset($_SESSION['error'])) unset($_SESSION['error']);
	if(isset($_SESSION['success'])) unset($_SESSION['success']);
	unset($_SESSION['zip_file_url']);
}
?>

			<div class="row mt-3">

				<!-- Top|Left Panel -->
				<div class="col-md-6">

					<!-- Form -->
					<form name="barcode_generator_form" id="bc_generator_form" method="post" enctype="application/x-www-form-urlencoded" action="./">


						<h3 class="text-uppercase mb-3 font-weight-bold">Single or Bulk Generate</h3>
 						<nav class="mb-3">
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<a class="nav-item nav-link<?php echo !isset($_SESSION['bulk_tab']) ? ' active' : ''; ?>" id="nav-single-tab" data-toggle="tab" href="#nav-single" role="tab" aria-controls="nav-single" aria-selected="<?php echo !isset($_SESSION['bulk_tab']) ? 'true' : 'false'; ?>">Single</a>
								<a class="nav-item nav-link<?php echo isset($_SESSION['bulk_tab']) ? ' active' : ''; ?>" id="nav-bulk-tab" data-toggle="tab" href="#nav-bulk" role="tab" aria-controls="nav-bulk" aria-selected="<?php echo isset($_SESSION['bulk_tab']) ? 'true' : 'false'; ?>">Bulk</a>
							</div>
						</nav>

						<div class="tab-content" id="nav-tabContent">
							<!-- SINGLE -->
							<div class="tab-pane fade<?php echo !isset($_SESSION['bulk_tab']) ? ' show active' : ''; ?>" id="nav-single" role="tabpanel" aria-labelledby="nav-single-tab">
								<div class="form-group">
									<label for="bc_caption">Caption:</label>
									<input name="bc_caption" type="text" class="form-control" id="bc_product" placeholder="e.g. Apple iPhone 11 Pro Max" value="<?php echo isset($_POST['bc_caption']) ? $_POST['bc_caption'] : ''; ?>">
									<small class="form-text text-muted font-italic">Leave blank for remove caption.</small>
								</div>
								<div class="form-group">
									<label for="bc_serial">Serial:</label>
									<input name="bc_serial" type="text" class="form-control" id="bc_serial" placeholder="e.g. 1234567890 or {9}" value="<?php echo isset($_POST['bc_serial']) ? $_POST['bc_serial'] : ''; ?>">
									<small class="form-text text-muted font-italic">Sp! Magic!... Use "{n}" format for n digits random number! (n: between 1 to 20)</small>
								</div>
							</div>
							<!-- /SINGLE -->

							<!-- BULK -->
							<div class="tab-pane fade<?php echo isset($_SESSION['bulk_tab']) ? ' show active' : ''; ?>" id="nav-bulk" role="tabpanel" aria-labelledby="nav-bulk-tab">
								<div class="form-group">
									<label for="bc_bulk_data">Bulk Data Rows:</label>
									<textarea name="bc_bulk_data" id="bc_bulk_data" class="form-control" rows="5" placeholder="e.g. My Caption,1234567890"><?php echo isset($_POST['bc_bulk_data']) ? $_POST['bc_bulk_data'] : ''; ?></textarea>
									<small class="form-text text-muted font-italic">
										Use caption & serial combination with "<strong>,</strong>" (Comma) as separator, one per line.<br/>
										Example:<br />
										First Caption,123456789<br />
										Another Caption,987654321
									</small>
								</div>								
							</div>
							<!-- /BULK -->
						</div>

						<?php unset($_SESSION['bulk_tab']); ?>
 
						<h3 class="text-uppercase mt-4 font-weight-bold">Other Settings</h3>
 						<div class="form-group">
							<label for="bc_copyright">Copyright:</label>
							<input name="bc_copyright" type="text" class="form-control" id="bc_copyright" placeholder="e.g. NaghshinehPars.com" value="<?php echo isset($_POST['bc_copyright']) ? $_POST['bc_copyright'] : ''; ?>">
							<small class="form-text text-muted font-italic">Leave blank for remove copyright text.</small>
						</div>

						<div class="form-group">
							<div class="custom-control custom-switch mt-2">
								<input name="bc_copyright_color" type="hidden" id="bc_copyright_color" value="<?php echo isset($_POST['bc_copyright_color']) ? $_POST['bc_copyright_color'] : 0; ?>">
								<input id="bc_copyright_color_switch" type="checkbox" class="custom-control-input"<?php echo isset($_POST['bc_copyright_color']) && $_POST['bc_copyright_color'] == 0 || !isset($_POST['bc_copyright_color']) ? ' checked' : ''; ?>>
								<label class="custom-control-label" for="bc_copyright_color_switch">Simple Copyright Text (No Color)</label>
							</div>
						</div>

						<div id="bc_colorpicker_box" class="form-row"<?php echo isset($_POST['bc_copyright_color']) && $_POST['bc_copyright_color'] == 0 || !isset($_POST['bc_copyright_color']) ? ' style="display: none;"' : ''; ?>>
							<div class="form-group col-md-6">
								<label for="bc_copyright_textcolor">Copyright Text Color:</label>
								<div id="cp_text" class="input-group" title="Using input value">
									<input name="bc_copyright_textcolor" id="bc_copyright_textcolor" type="text" class="form-control" value="<?php echo isset($_POST['bc_copyright_textcolor']) ? $_POST['bc_copyright_textcolor'] : '#ffffff'; ?>"/><!-- #867606 -->
									<span class="input-group-append">
										<span class="input-group-text colorpicker-input-addon"><i></i></span>
									</span>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="bc_copyright_bgcolor">Copyright Background Color:</label>
								<div id="cp_bg" class="input-group" title="Using input value">
									<input name="bc_copyright_bgcolor" id="bc_copyright_bgcolor" type="text" class="form-control input-lg" value="<?php echo isset($_POST['bc_copyright_bgcolor']) ? $_POST['bc_copyright_bgcolor'] : '#000000'; ?>"/><!-- #E6E6AF -->
									<span class="input-group-append">
										<span class="input-group-text colorpicker-input-addon"><i></i></span>
									</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="bc_width_scale">Width Scale:</label>
							<select class="custom-select" id="bc_width_scale" name="bc_width_scale">
<?php
$scaleOptions = array(0.5, 1.0, 1.5, 2.0);
$selected = isset($_POST['bc_width_scale']) ? $_POST['bc_width_scale'] : 1.0;
foreach ($scaleOptions as $option)
{
	echo "\t\t\t\t\t\t\t\t<option" . ($selected == $option ? ' selected>' : '>') . "$option</option>\n";
}
?>
							</select>
						</div>

						<div class="form-group mt-4">
							<button name="bc_generate" type="submit" id="bc_generate" class="btn btn-primary btn-block btn-success btn-lg">
								<span id="btn_loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="margin-top:3px;vertical-align:unset;display:none;"></span>
								<span id="btn_status">Generate</span>
							</button>
						</div>

					</form>
					<!-- /Form -->

				</div>
				<!-- /Top|Left Panel -->

				<!-- Bottom|Right Panel -->
				<div class="col-md-6 justify-content-center bg-light p-5">

					<div class="align-self-center text-center">
						<div class="row">
							<h2 class="lead font-weight-bold">Preview:</h2>
						</div>
						<div class="row">
							<img id="bc_image_result" class="img-fluid" src="<?php echo isset($_POST['bc_generate']) && isset($_SESSION['sample_image_preview']) ? $_SESSION['sample_image_preview'] : './images/sample.png'; ?>" />
						</div>
					</div>

				</div>
				<!-- /Bottom|Right Panel -->

			</div>

		</div>
		
		<footer class="container">
			<div class="row">
				<div class="col-md-12 text-center p-5 text-muted font-italic">
					<small>made with <i class="my-icon icon-heart" style="color:#ff0000;">&#xe802;</i> @<a href="https://naghshinehpars.com/">NaghshinehPars</a> <i class="my-icon icon-emo-wink2">&#xe813;</i> <br />All rights reserved. &copy 2020</small>
				</div>
			</div>
		</footer>

		<script src="./js/jquery-3.5.1.min.js"></script>
		<script src="./js/popper-1.16.0.min.js"></script>
		<script src="./js/bootstrap-4.5.0.min.js"></script>
		<script src="./js/iconic.min.js"></script>
		<script src="./js/bootstrap-colorpicker.min.js"></script>
		<script src="./js/common.js"></script>
	</body>
</html>