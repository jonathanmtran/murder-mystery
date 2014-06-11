<?php
class ShowsProvider {
	private $url;

	function __construct($page_url) {
		$this->url = $page_url;
	}

	public function getShows() {
		if(strlen($this->url) == 0)
			return array();

		// Get Page
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		$response = curl_exec($ch);
		curl_close($ch);

		// Parse response as XML
		libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML($response);

		// Locate all <select> elements
		$selects = $doc->getElementsByTagName('select');

		$shows = array();

		foreach($selects as $select) {
			// Find <select> with name of showid
			if($select->getAttribute('name') == 'showid') {
				// Get the <option>s
				$options = $select->getElementsByTagName('option');

				foreach($options as $option) {
					// Please Select A Show Date
					if($option->getAttribute('value') == '')
						continue;

					$showid = (int)$option->getAttribute('value');

					// Extract information about show
					preg_match('/(.+) @ (.+) - Theme: (.+)| SOLD OUT./', $option->nodeValue, $matches);

					// Just in case something gets funky
					if(count($matches) != 4)
						continue;

					$soldout = $option->getAttribute('disabled');

					$shows[] = array(
						'showid' => $showid,
						'date' => $matches[1],
						'time' => $matches[2],
						'theme' => $matches[3],
						'soldout' => strlen($soldout) > 0 ? 1 : 0,
						'raw' => $option->nodeValue,
					);
				}
			}
		}

		return $shows;
	}
}
