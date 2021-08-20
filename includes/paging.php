<?php
class Paging {

	public $page;
	public $pagesize;
	public $itemcount;
	private $rowcolour;

	// Constructor
	public function __construct($page = 1, $pagesize = 17, $itemcount = 0) {
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->itemcount = $itemcount;
		$this->rowcolour = "greybg";
	}

	// ********************************************************************************
	// Public instance functions
	public function pageCount() {

		return ceil($this->itemcount / $this->pagesize);
	}

	public function offset() {

		return $this->pagesize * ($this->page - 1);
	}

	public function previousPage() {

		return $this->page - 1;
	}

	public function nextPage() {

		return $this->page + 1;
	}

	public function isFirstPage() {

		return $this->page <= 1 ? true : false;
	}

	public function isLastPage() {

		return $this->page >= $this->pageCount() ? true : false;
	}

	public function pagingNav($actionURL) {

		$return = '
			<div class="buttonblock">';
		if ($this->pageCount() > 1) {
			if ($this->isFirstPage()) {
				$return .= '<< first&nbsp;&nbsp;< previous&nbsp;&nbsp;';
			} else {
				$return .= '<a href="'. $actionURL. '?page=1"><< first</a>&nbsp;&nbsp;';
				$return .= '<a href="'. $actionURL. '?page='. $this->previousPage(). '">< previous</a>&nbsp;&nbsp;';
			}
			$fromPage = $this->page - 10;
			$toPage = $this->page + 10;
			$fromPage = $fromPage > 0 ? $fromPage : 1;
			$toPage = $toPage <= $this->pageCount() ? $toPage : $this->pageCount();
			for ($i = $fromPage; $i <= $toPage; $i++) {
				if ($i == $this->page) {
					$return .= $i;
				} else {
					$return .= '<a href="'. $actionURL. '?page='. $i. '">'. $i. '</a>';
				}
				$return .= '&nbsp;&nbsp;';
			}
			if ($this->isLastPage()) {
				$return .= 'next >&nbsp;&nbsp;last >>&nbsp;&nbsp;';
			} else {
				$return .= '<a href="'. $actionURL. '?page='. $this->nextPage(). '">next ></a>&nbsp;&nbsp;';
				$return .= '<a href="'. $actionURL. '?page='. $this->pageCount(). '">last >></a>';
			}
		}
		$return .= '</div>';
		return $return;
	}

	public function colourNewRow() {

		$this->rowcolour = $this->rowcolour == "whitebg" ? "greybg" : "whitebg";
		return $this->rowcolour;
	}
}
?>