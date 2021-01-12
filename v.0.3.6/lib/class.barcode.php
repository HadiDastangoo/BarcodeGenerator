<?php
class barcode
{
	public function __construct($barcode_string, $barcode_prefix = '', $barcode_label_top = '', $barcode_label_bottom = '', $barcode_label_bottom_textcolor = '', $barcode_label_bottom_bgcolor = '', $width_scale = 1, $code_type = 'code128')
	{
		// ********** <CONFIGURATIONS> **********

		
		putenv('GDFONTPATH=' . realpath('.'));

		// ***** Static Parameters *****
		$this->barcode_wrap_padding = 10;
		$this->barcode_label_top_height = 20;
		$this->barcode_label_padding = 20;
		$this->barcode_height = 50;
		$this->barcode_label_bottom_height = 20;
		$this->code_length = 20;
		$this->fonts_path = __DIR__ . '/fonts/';
		$this->text1_font = "JetBrains.ttf";
		$this->text2_font = "JetBrains.ttf";
		$this->text3_font = "DroidSansMono.ttf";
		$this->text1_size = 10;
		$this->text2_size = 8;
		$this->text3_size = 9;
		$this->text_padding = 7;
		$this->code_string = "";
		$this->max_string_length = 25;
		$this->minimum_width = 255;

		// ***** Dynamics *****
		$this->barcode_label_top = $this->optimizeLength($barcode_label_top);
		$this->barcode_prefix = $this->optimizeLength($barcode_prefix);
		$this->barcode_string = $this->optimizeLength($barcode_string);
		$this->barcode_label_bottom = $this->optimizeLength($barcode_label_bottom);
		$this->barcode_label_bottom_textcolor = ctype_xdigit($barcode_label_bottom_textcolor) ? list($this->r1, $this->g1, $this->b1) = sscanf($barcode_label_bottom_textcolor, "%02x%02x%02x") : list($this->r1, $this->g1, $this->b1) = array(0, 0, 0);
		$this->barcode_label_bottom_bgcolor = ctype_xdigit($barcode_label_bottom_bgcolor) ? list($this->r2, $this->g2, $this->b2) = sscanf($barcode_label_bottom_bgcolor, "%02x%02x%02x") : list($this->r2, $this->g2, $this->b2) = array(255, 255, 255);
		$this->width_scale = $width_scale;
		$this->code_type = $code_type;

		// ********** </CONFIGURATIONS> **********
	}

	protected function optimizeLength($string) 
	{
		return $this->max_string_length < (strlen(trim($string)) - 3) ? mb_substr(trim($string), 0, $this->max_string_length) . '...' : trim($string);
	}


	private function createCodeString()
	{

		// Translate the $barcode_string into barcode the correct $code_type
		switch( strtolower($this->code_type) )
		{
			case "code39":
				$code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");

				// Convert to uppercase
				$upper_text = strtoupper($this->barcode_string);

				for($X = 1; $X<=strlen($upper_text); $X++)
				{
					$this->code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
				}

				$this->code_string = "1211212111" . $this->code_string . "121121211";
				break;


			case "code25":
				$code_array1 = array("1","2","3","4","5","6","7","8","9","0");
				$code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

				for ($X = 1; $X <= strlen($this->barcode_string); $X++)
				{
					for ($Y = 0; $Y < count($code_array1); $Y++)
					{
						if (substr($this->barcode_string, ($X-1), 1) == $code_array1[$Y])
						{
							$temp[$X] = $code_array2[$Y];
						}
					}
				}

				for ($X=1; $X<=strlen($this->barcode_string); $X+=2)
				{
					$temp1 = explode( "-", $temp[$X] );
					$temp2 = explode( "-", $temp[($X + 1)] );
					for ($Y = 0; $Y < count($temp1); $Y++)
					{
						$this->code_string .= $temp1[$Y] . $temp2[$Y];
					}
				}

				$this->code_string = "1111" . $this->code_string . "311";
				break;


			case "codabar":
				$code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
				$code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

				// Convert to uppercase
				$upper_text = strtoupper($this->barcode_string);

				for ($X = 1; $X<=strlen($upper_text); $X++)
				{
					for ($Y = 0; $Y<count($code_array1); $Y++)
					{
						if (substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
						{
							$this->code_string .= $code_array2[$Y] . "1";
						}
					}
				}
				$this->code_string = "11221211" . $this->code_string . "1122121";
				break;


			case "code128":
			default:
				$chksum = 104;

				// Must not change order of array elements as the checksum depends on the array's key to validate final code
				$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
				$code_keys = array_keys($code_array);
				$code_values = array_flip($code_keys);

				for ($X = 1; $X <= strlen($this->barcode_string); $X++)
				{
					$activeKey = substr( $this->barcode_string, ($X-1), 1);
					$this->code_string .= $code_array[$activeKey];
					$chksum=($chksum + ($code_values[$activeKey] * $X));
				}

				$this->code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
				$this->code_string = "211214" . $this->code_string . "2331112";
				break;
		}

		return;

	}

	
	public function createBarcode()
	{

		$this->createCodeString($this->code_type);

		for ($i=1; $i <= strlen($this->code_string); $i++)
		{
			$this->code_length = $this->code_length + (integer)(substr($this->code_string, $i-1, 1));
		}

		$img_width = $this->barcode_wrap_padding + $this->code_length * $this->width_scale + $this->barcode_wrap_padding;
		$img_height = $this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_height + $this->barcode_label_padding * 3 + $this->barcode_label_bottom_height + $this->barcode_wrap_padding;

		// set minimum width for $img_width
		$img_width = ($img_width < $this->minimum_width) ? $this->minimum_width : $img_width;

		$image = imagecreate($img_width, $img_height);

		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$custom_color1 = imagecolorallocate($image, $this->r1, $this->g1, $this->b1); // 180, 170, 100
		$custom_color2 = imagecolorallocate($image, $this->r2, $this->g2, $this->b2); // 230, 230, 175
		$text1_path = $this->fonts_path . $this->text1_font;
		$text2_path = $this->fonts_path . $this->text2_font;
		$text3_path = $this->fonts_path . $this->text3_font;

		imagefill( $image, 0, 0, $white );		

		$current_location_x = $this->barcode_wrap_padding;
		for ($position = 1 ; $position <= strlen($this->code_string); $position++)
		{
			$cur_size = $current_location_x + ( substr($this->code_string, ($position-1), 1) );
			imagefilledrectangle(
				$image,
				$this->barcode_wrap_padding + $current_location_x * $this->width_scale,
				$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_label_padding,
				$this->barcode_wrap_padding + $cur_size * $this->width_scale,
				$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_label_padding + $this->barcode_height,
				($position % 2 == 0 ? $white : $black)
			);
			$current_location_x = $cur_size;
		}

		// add labels
		imagettftext(
			$image, 
			$this->text1_size, 
			0, 
			$this->barcode_wrap_padding + $this->barcode_wrap_padding * $this->width_scale, 
			$this->barcode_wrap_padding + $this->barcode_label_top_height,
			$black, 
			$text1_path, 
			$this->barcode_label_top
		);
		imagettftext(
			$image, 
			$this->text2_size, 
			0, 
			$this->barcode_wrap_padding + $this->barcode_wrap_padding * $this->width_scale, 
			$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_wrap_padding + $this->barcode_height + $this->barcode_label_padding + 5, 
			$black, 
			$text2_path, 
			($this->barcode_prefix != '' ? $this->barcode_prefix . ' ' : '') . $this->barcode_string
		);
		imagefilledrectangle(
			$image,  
			$this->barcode_wrap_padding + $this->barcode_wrap_padding * $this->width_scale, 
			$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_label_padding + $this->barcode_height + $this->barcode_label_padding + $this->barcode_label_bottom_height - 15, 
			$this->barcode_wrap_padding + $this->barcode_wrap_padding * $this->width_scale + strlen($this->barcode_label_bottom) * 8,
			$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_label_padding + $this->barcode_height + $this->barcode_label_padding + $this->barcode_label_bottom_height + 5,
			$custom_color2
		);
		imagettftext(
			$image, 
			$this->text3_size, 
			0, 
			$this->barcode_wrap_padding + $this->barcode_wrap_padding * $this->width_scale + $this->text_padding, 
			$this->barcode_wrap_padding + $this->barcode_label_top_height + $this->barcode_label_padding + $this->barcode_height + $this->barcode_label_padding + $this->barcode_label_bottom_height, 
			$custom_color1, 
			$text3_path, 
			$this->barcode_label_bottom
		);

		$this->image = $image;
		return;
	}

	public function saveBarcode($savePath = './', $barcodeNamePrefix = 'barcode_')
	{
		$this->fileName = $barcodeNamePrefix . trim($this->barcode_string) . '.png';
		$this->filePath = $savePath . $this->fileName;
		imagepng($this->image, $this->filePath);
		return;
	}

	public function showBarcode()
	{
		header ('Content-type: image/png');
		imagepng($this->image);
		return;
	}

	public function __destruct()
	{
		imagedestroy($this->image);
		return;
	}

}
