<?php
require_once("page.php");

class Session {
	
	public $id;
	public $name;
	public $level;
	private $loggedin;

	function __construct() {

		session_start();
		$this->checkLogin();
	}
	
	public function isLoggedIn() {
		
		return $this->loggedin;
	}
	
	public function login($user) {

		if ($user) {
			$this->id = $user->id;
			$this->name = $user->name;
			$this->level = $user->accesslevel;
			$_SESSION['id'] = $user->id;
			$_SESSION['name'] = $user->name;
			$_SESSION['level'] = $user->accesslevel;
			$this->loggedin = true;
		}
	}
	
	public function logout() {

		unset($_SESSION['id']);
		unset($_SESSION['name']);
		unset($_SESSION['level']);
		unset($this->id);
		unset($this->name);
		unset($this->level);
		$this->loggedin = false;
	}

	private function checkLogin() {
		
		if (isset($_SESSION['id'])) {
			$this->id = $_SESSION['id'];
			$this->name = $_SESSION['name'];
			$this->level = $_SESSION['level'];
			$this->loggedin = true;
		} else {
			unset($this->id);
			unset($this->name);
			unset($this->level);
			$this->loggedin = false;
		}
	}

	public function isAuthorised() {

		global $currentSite;

		$isAuthorised = false;

		$url = $_SERVER['REQUEST_URI'];
		$url = substr($url, strrpos($url, '/') + 1, strlen($url) - strrpos($url, '/'));
		if (empty($url)) {
			$url = 'index.php';
		}
		$url = strtolower($url);
		if ($url == 'index.php' && empty($_REQUEST['id'])) {
			if (!empty($currentSite->homepageid)) {
				$url .= '?id='. $currentSite->homepageid;
			}
		}
		if (!$page = Page::findByURL($url)) {
			$url = $_SERVER['SCRIPT_NAME'];
			$url = substr($url, strrpos($url, '/') + 1, strlen($url) - strrpos($url, '/'));
			if ($url == 'index.php' && !empty($_REQUEST['id'])) {
				$url .= '?id='. htmlspecialchars($_REQUEST['id'], ENT_QUOTES);
			}
			$page = Page::findByURL($url);
		}
		if ($page) {
			$chklevel = '99';
			if (isset($_SESSION['level']) && !empty($_SESSION['level'])) {
				$chklevel = $_SESSION['level'];
			}
			if ($page->levelonly) {
				if ($page->levelnumber == $chklevel) {
					$isAuthorised = true;
				}
			} else if ($page->levelnumber >= $chklevel) {
				$isAuthorised = true;
			}
		}
		return $isAuthorised;
	}
}

$session = new Session();
?>