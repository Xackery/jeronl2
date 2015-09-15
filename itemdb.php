<?php

class ItemDB {
	private $items;

	public function __construct() {
		$itemPath = "stats/items/";
		$files = scandir($itemPath);
		$this->items = array();

		foreach ($files as $file) {

			if ($file == "." || $file == "..") continue;
		    if (filesize("$itemPath/$file") < 100) continue;
			$xml = @simplexml_load_string(file_get_contents("$itemPath/$file"));
			if (empty($xml->item)) continue;
			foreach ($xml->item as $item) {
				if (!empty($item['id'])) {
					$id = (int)$item['id'];
					$this->items[$id] = $item;
					//echo "Added item $id\n";
				} else {
					//echo "Skipped entry, no id";
				}
			}
		}
	}

	public function getSize() {
		return sizeof($this->items);
	}
	public function get($id) {
		return $this->items[$id];
	}

	public function getType($id) {
		/*
		if ($id == "4052") {
			echo "Got request for 4052";
			echo $this->items[(int)$id]['type'];
			exit;
		}
		*/
		if (!empty($this->items[(int)$id])) {
			return $this->items[(int)$id]['type'];		
		}
		echo "Loading $id failed?";
		print_r($this->items[$id]);
		exit;
	}
}

$itemdb = new ItemDB();
//print_r($itemdb->get(22565));