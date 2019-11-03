<?php

abstract class View_List extends View {
	
	protected $SortOptions = array();
	protected $PageOptions = array();
	//protected $GroupOptions = array();
	
	// SORT OPTIONS --------------------------------------------------------------
	
	public function addSortOption () {
		// addSortOption ( title , field_name , primary_order , secondary_order )
		$args = func_get_args();
		require_once 'sortoptions.php';
		return $this->SortOptions[] = call_user_func_array('SortOption::create',$args);
	}
	
	public function getSortOptions_HTML () {
		$html = '';
		$sort_options = array();
		foreach ($this->SortOptions as $option) {
			$sort_options[] = $option->getHTML($this->Creator);
		}
		$html .= '<p>'.L_RESULTS_ORDER.': '.implode(Z_SEPARATOR_SYMBOL,$sort_options).'</p>'.PHP_EOL;
		return $html;
	}
	
	// GROUP OPTIONS --------------------------------------------------------------
	
	/*
	public function addGroupOption () {
		// addGroupOption ( title , field_name )
		$args = func_get_args();
		require_once 'groupoptions.php';
		return $this->GroupOptions[] = call_user_func_array('GroupOption::create',$args);
	}
	
	public function getGroupOptions_HTML () {
		$html = '';
		$group_options = array();
		foreach ($this->GroupOptions as $option) {
			$group_options[] = $option->getHTML($this->Creator);
		}
		$html .= '<p>'.L_RESULTS_ORDER.': '.implode(Z_SEPARATOR_SYMBOL,$group_options).'</p>'.PHP_EOL;
		return $html;
	}
	*/
	
	// PAGE OPTIONS --------------------------------------------------------------
	
	public function addPageOptions () {
		// addPageOption ( arg1, arg2, arg3, ... )
		$args = func_get_args();
		require_once 'pageoptions.php';
		return $this->PageOptions[] = func_get_args();
	}
	
	public function getPageOptions_HTML () {
		$html = '';
		$page_options = array();
		foreach ($this->PageOptions as $option) {
			$page_options[] = $option->getHTML($this->Creator);
		}
		$html .= '<p>'.L_RESULTS_PAGE.': '.implode(Z_SEPARATOR_SYMBOL,$page_options).'</p>'.PHP_EOL;
		return $html;
	}
	
	// BROWSE OPTIONS ------------------------------------------------------------
	
	public function getBrowseOptions_HTML () {
		$html = NULL;
		if ($this->Creator->getUserVar('total_results') > Z_LIST_ROWS_PAGE) {
			$first = $this->Creator->getUserVar('skip') + 1;
			$last = ($this->Creator->getUserVar('skip') + Z_LIST_ROWS_PAGE < $this->Creator->getUserVar('total_results'))
				? ($this->Creator->getUserVar('skip') + Z_LIST_ROWS_PAGE)
				: ($this->Creator->getUserVar('total_results'));
			$next = ($last < $this->Creator->getUserVar('total_results'))
				? ($first + Z_LIST_ROWS_PAGE)
				: 0;
			$previous = ($first > Z_LIST_ROWS_PAGE)
				? ($first - Z_LIST_ROWS_PAGE)
				: 0;
			if (Z_LIST_ROWS_SKIP) {
				$last_skip = ($this->Creator->getUserVar('skip') + Z_LIST_ROWS_SKIP < $this->Creator->getUserVar('total_results'))
					? ($this->Creator->getUserVar('skip') + Z_LIST_ROWS_SKIP)
					: ($this->Creator->getUserVar('total_results'));
				$next_skip = ($last_skip < $this->Creator->getUserVar('total_results'))
					? ($first + Z_LIST_ROWS_SKIP)
					: 0;
				$previous_skip = ($first > Z_LIST_ROWS_SKIP)
					? ($first - Z_LIST_ROWS_SKIP)
					: 0;
			}
			// list navigation
			$html .= '<p>';
			// total results
			$html .= L_RESULTS.': <b>'.$first.' – '.$last.'</b> '.L_OF_TOTAL_RESULTS.' <b>'.$this->Creator->getUserVar('total_results').'</b>. ';
			// link to first page
			if ($this->Creator->getUserVar('skip') > 0) {
				$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
				if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
				if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
				$html .= '">'.Z_FIRST_SYMBOL.L_RESULTS_FIRST.'</a> ';
			}
			else {
				$html .= '<a class="button inactive">'.Z_FIRST_SYMBOL.L_RESULTS_FIRST.'</a> ';
			}
			// skip back
			if (Z_LIST_ROWS_SKIP) {
				if ($previous_skip) {
					$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
					if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
					if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
					if ($previous_skip > 1) $html .= '&skip='.($previous_skip - 1);
					$html .= '">'.Z_SKIP_BACK_SYMBOL.Z_LIST_ROWS_SKIP.'</a> ';
				}
				else {
					$html .= '<a class="button inactive">'.Z_SKIP_BACK_SYMBOL.Z_LIST_ROWS_SKIP.'</a> ';
				}
			}
			// previous page
			if ($previous) {
				$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
				if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
				if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
				if ($previous > 1) $html .= '&skip='.($previous - 1);
				$html .= '">'.Z_PREVIOUS_SYMBOL.L_RESULTS_PREVIOUS.Z_LIST_ROWS_PAGE.'</a> ';
			}
			else {
				$html .= '<a class="button inactive">'.Z_PREVIOUS_SYMBOL.L_RESULTS_PREVIOUS.Z_LIST_ROWS_PAGE.'</a> ';
			}
			// next page
			if ($next) {
				$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
				if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
				if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
				$html .= '&skip='.($next - 1);
				$html .= '">'.Z_NEXT_SYMBOL.L_RESULTS_NEXT.Z_LIST_ROWS_PAGE.'</a> ';
			}
			else {
				$html .= '<a class="button inactive">'.Z_NEXT_SYMBOL.L_RESULTS_NEXT.Z_LIST_ROWS_PAGE.'</a> ';
			}
			// skip forward
			if (Z_LIST_ROWS_SKIP) {
				if ($next_skip) {
					$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
					if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
					if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
					$html .= '&skip='.($next_skip - 1);
					$html .= '">'.Z_SKIP_FORWARD_SYMBOL.Z_LIST_ROWS_SKIP.'</a> ';
				}
				else {
					$html .= '<a class="button inactive">'.Z_SKIP_FORWARD_SYMBOL.Z_LIST_ROWS_SKIP.'</a> ';
				}
			}
			// link to last page
			$lastpage = $this->Creator->getUserVar('total_results') - $this->Creator->getUserVar('total_results') % Z_LIST_ROWS_PAGE;
			if ($this->Creator->getUserVar('skip') < $lastpage) {
				$html .= '<a class="button" href="?'.$this->Creator->getUserVar('querystring');
				if ($this->Creator->getUserVar('sort')) $html .= '&sort='.$this->Creator->getUserVar('sort');
				if ($this->Creator->getUserVar('order')) $html .= '&order='.$this->Creator->getUserVar('order');
				$html .= '&skip='.($lastpage);
				$html .= '">'.Z_LAST_SYMBOL.L_RESULTS_LAST.'</a> ';
			}
			else {
				$html .= '<a class="button inactive">'.Z_LAST_SYMBOL.L_RESULTS_LAST.'</a> ';
			}
			$html .= '</p>';
		}
		elseif ($this->Creator->getUserVar('total_results')>0) {
			// no list navigation
			$html .= '<p>'.L_RESULTS.': <b>1 - '.$this->Creator->getUserVar('total_results').'</b> '.L_OF_TOTAL_RESULTS.' <b>'.$this->Creator->getUserVar('total_results').'</b>.</p>';
		}
		return $html;
	}
}
