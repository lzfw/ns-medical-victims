<?php

function getCitation ( $word, $format ) {
	$txt = '';
	switch ($format) {
		case 'list':
			// content
			$txt .= GGA_SOURCE.': '.$word->source_name.PHP_EOL;
			$txt .= GGA_FILECARD.': '.$word->filecard_name.PHP_EOL;
			// greek
			$txt .= GGA_GREEK_LEXEME.': '.$word->gr_lexeme.PHP_EOL;
			$txt .= GGA_GREEK_EXPRESSION.': '.$word->gr_expression.PHP_EOL;
			$txt .= GGA_GREEK_POS.': '.$word->gr_pos_title.PHP_EOL;
			$txt .= GGA_GREEK_REFERENCE.': '.($word->gr_reference?$word->gr_reference:GGA_NOT_SPECIFIED).PHP_EOL;
			$txt .= GGA_GREEK_QUOTATION.': '.($word->gr_quotation?$word->gr_quotation:GGA_NOT_SPECIFIED).PHP_EOL;
			// arabic
			$txt .= GGA_ARABIC_ROOT.': '.
				$word->ar_root_1.' '.
				$word->ar_root_2.' '.
				$word->ar_root_3.' '.
				$word->ar_root_4.' '.
				$word->ar_root_5.PHP_EOL;
			$txt .= GGA_ARABIC_STEM.': '.roman_numeral($word->ar_stem).PHP_EOL;
			$txt .= GGA_ARABIC_LEXEME.': '.$word->ar_lexeme.'&#x200f;'.PHP_EOL;
			$txt .= GGA_ARABIC_EXPRESSION.': '.$word->ar_expression.PHP_EOL;
			$txt .= GGA_ARABIC_POS.': '.$word->ar_pos_title.PHP_EOL;
			$txt .= GGA_ARABIC_REFERENCE.': '.($word->ar_reference?$word->ar_reference:GGA_NOT_SPECIFIED).PHP_EOL;
			$txt .= GGA_ARABIC_QUOTATION.': '.($word->ar_quotation?$word->ar_quotation:GGA_NOT_SPECIFIED).PHP_EOL;
			// critical apparatus
			$txt .= GGA_ANNOTATION.': '.($word->annotation?$word->annotation:GGA_NOT_SPECIFIED).PHP_EOL;
			// link
			$txt .= GGA_LINK.': http://telotadev.bbaw.de/glossga/glossary.php?id='.($word->word_id).PHP_EOL;
			break;
		case 'gr-ar':
			$txt .=
				($word->gr_lexeme ? $word->gr_lexeme : 'n/a').
				($word->gr_pos ? ' '.$word->gr_pos_title : '').
				($word->gr_reference ? ', '.$word->gr_lexeme : '').
				' = '.
				($word->ar_root_1.$word->ar_root_2.$word->ar_root_3.$word->ar_root_4.$word->ar_root_5).
				($word->ar_stem ? ' '.roman_numeral($word->ar_stem) : '').
				'. '.
				($word->ar_expression ? $word->ar_expression.' ' : '').
				($word->ar_annotation or $word->ar_quotation or $word->ar_reference? ']' : '').
				($word->ar_annotation ? ' '.$word->ar_annotation : '').
				($word->ar_quotation ? ' '.$word->ar_quotation : '').
				($word->ar_reference ? ' '.$word->ar_reference : '').
				'.'.PHP_EOL;
			break;
		case 'ar-gr':
			$txt .=
				($word->ar_root_1.$word->ar_root_2.$word->ar_root_3.$word->ar_root_4.$word->ar_root_5).'. '.
				($word->ar_stem ? roman_numeral($word->ar_stem).'. ' : '').
				($word->ar_lexeme ? $word->ar_lexeme.'&#x200f;'.' ' : '').
				($word->ar_pos ? $word->ar_pos_title : '').
				(($word->ar_pos or $word->ar_lexeme) ? '. ' : '').
				'- '.
				($word->gr_lexeme ? $word->gr_lexeme : 'n/a ').
				($word->gr_annotation ? ', '.$word->gr_annotation : '').': '.
				($word->gr_expression ? $word->gr_expression.' ' : '').
				($word->gr_quotation or $word->gr_reference ? ']' : '').'] '.
				($word->gr_quotation ? $word->gr_quotation.' ' : '' ).
				($word->gr_reference ? $word->gr_reference.' ' : '' ).
				'= '.
				($word->ar_expression ? $word->ar_expression.' ' : '').
				($word->ar_annotation ? $word->annotation.' ' : '').
				($word->ar_quotation or $word->ar_reference ? ']' : '').
				($word->ar_quotation ? ' '.$word->ar_quotation : '').
				($word->ar_reference ? ' '.$word->ar_reference : '').
				'.'.PHP_EOL;
			break;
	}
	return $txt;
}

?>
