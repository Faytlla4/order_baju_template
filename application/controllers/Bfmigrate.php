<?php defined('BASEPATH') || exit('No direct script access allowed');

Class Bfmigrate extends MX_Controller
{
	public function index()
	{
		$this->load->helper('application');
		$this->load->library('migrations/migrations');

		$type = $this->uri->segment(3);
		$version = (int) $this->uri->segment(4);

		if (empty($type) || empty($version)) {
			echo "Usage: php index.php bfmigrate/index {type} {version}\n";
			echo "Example: php index.php bfmigrate/index master_ukuran_ 2\n";
			return;
		}

		$this->migrations->setVerbose(true);
		$result = $this->migrations->version($version, $type);
		echo "Result: ";
		var_dump($result);
		echo "Errors: ";
		var_dump($this->migrations->getErrors());
	}
}