<?php

class DBI_GlossGA extends DBI {

	public $filecard_path = 'http://digilib.bbaw.de/digitallibrary/servlet/Scaler?fn=/silo10/glossga/';
	//public $filecard_path = 'http://digilib.bbaw.de/digitallibrary/servlet/Scaler?fn=/silo10/Galex/';
	
	// SIDEBAR ELEMENTS ----------------------------------------------------------
	
	public function getReferences_HTML () {
		$html = '';
		$html .= '<h3>'.GGA_REFERENCES.'</h3>';
		$html .= '<ul class="icons no-indent">';
		$html .= '<li class="abbreviations"><a href="abbreviations.php">'.GGA_ABBREVIATIONS.'</a></li>';
		$html .= '<li class="links"><a href="links.php">'.GGA_LINKS.'</a></li>';
		$html .= '<li class="print"><a href="?view=print&'.$this->getUserVar('querystring').'" target="_blank">'.DBI_PRINTABLE_PAGE.'</a></li>';
		$html .= '<li class="contact"><a href="contact.php">'.DBI_CONTACT.'</a></li>';
		$html .= '</ul>';
		return $html;
	}
	
	public function getRubrics_HTML () {
		$html = '';
		$html .= '<h3>'.GGA_BROWSE.'</h3>';
		$html .= '<ul class="icons no-indent">';
		$html .= '<li class="sources"><a href="sources.php">'.GGA_SOURCES.'</a></li>';
		$html .= '<li class="filecards"><a href="filecards.php">'.GGA_FILECARDS.'</a></li>';
		$html .= '<li class="words"><a href="glossary.php">'.GGA_WORDS.'</a></li>';
		$html .= '</ul>';
		return $html;
	}

	public function getRandomFilecard_HTML () {
		$filecard = mysql_get_random_row('filecards','filecard_id');
		$image = $this->filecard_path.$filecard->folder.'/'.$filecard->name.'.'.$filecard->extension.'&dw=200';
		$html = '';
		$html .= '<h3>'.GGA_RANDOM_FILECARD.'</h3>';
		$html .= '<p><a href="filecards.php?id='.$filecard->filecard_id.'">';
		$html .= '<img src="'.$image.'" class="filecard" />';
		$html .= '</a></p>';
		return $html;
	}

	// LIST VIEWS ----------------------------------------------------------------
	
	// this function is probably obsolete
	public function getTransliterationList_HTML ($query) {
		$html = '';
		if (mysql_num_rows($query)>0) {
			$html .= '<table>';
			$html .= '<tr><th>mono</th><th>double</th></tr>'.PHP_EOL;
			while ($transliteration = mysql_fetch_object($query)) {
				$html .= '<tr><td>'.$transliteration->latin_mono.'</td>';
				$html .= '<td>'.$transliteration->latin_double.'</td></tr>';
			}
			$html .= '</table>'.PHP_EOL;
		}
		else {
			$html .= DBI_NO_RESULTS;
		}
		return $html;
	}
	
	// FUNCTIONS USED BY LIST VIEWS ----------------------------------------------
	
	public function getWordLink_HTML ($word) {
		$html = '';
		$html .= '<a href="glossary.php?id='.$word->word_id.'">';
		$html .= $word->gr_lexeme;
		if ($word->gr_lexeme == '' && $word->ar_lexeme == '')
			$html .= GGA_NOT_SPECIFIED;
		if ($word->gr_lexeme && $word->ar_lexeme)
			$html .= DBI_SEPARATOR_SYMBOL;
		$html .= $word->ar_lexeme;
		$html .= '</a>';
		return $html;
	}

	public function getWord_HTML ($word) {
		$html = '';
		$html .= $word->gr_lexeme;
		if ($word->gr_lexeme == '' && $word->ar_lexeme == '')
			$html .= GGA_NOT_SPECIFIED;
		if ($word->gr_lexeme && $word->ar_lexeme)
			$html .= DBI_SEPARATOR_SYMBOL;
		$html .= $word->ar_lexeme;
		return $html;
	}

	public function getCurrentWord_HTML () {
		$html = '';
		$html .= $this->getUserVar('gr_lexeme');
		if ($this->getUserVar('gr_lexeme') == '' && $this->getUserVar('ar_lexeme') == '')
			$html .= GGA_NOT_SPECIFIED;
		if ($this->getUserVar('gr_lexeme') && $this->getUserVar('ar_lexeme'))
			$html .= DBI_SEPARATOR_SYMBOL;
		$html .= $this->getUserVar('ar_lexeme');
		return $html;
	}

	// FUNCTIONS USED BY RECORD VIEWS --------------------------------------------

	public function getFilecardViewer_HTML ($filecard) {
		$html = '';
		$large_image = $this->filecard_path.$filecard->folder.'/'.$filecard->name.'.'.$filecard->extension.'&dw=1500';
		$small_image = $this->filecard_path.$filecard->folder.'/'.$filecard->name.'.'.$filecard->extension.'&dw=560';
		$html .= '<script type="text/javascript" src="javascript/jquery/jquery-1.4.4.min.js"></script>';
		$html .= '<script type="text/javascript" src="javascript/jqzoom/jqzoom.pack.1.0.1.js"></script>';
		$html .= '
			<p><a href="'.$large_image.'" class="filecard" title="'.GGA_MAGNIFIER.'">
			<img src="'.$small_image.'" class="filecard" />
			</a></p>
		';
		$html .= '
			<script type="text/javascript">
			$(document).ready(function(){
				var options = {
					xOffset: 40,
					yOffset: 0,
					zoomWidth: 220,
					zoomHeight: 220,
					showEffect: "fadein",
					hideEffect: "fadeout",
					fadeinSpeed: "fast",
					fadeoutSpeed: "fast",
					title: true
				};
				$(".filecard").jqzoom(options);
			});
			</script>
		';
		return $html;
	} 

}

?>
