<?php

$inPath = "skillTrees";

$file = "classSkillTree.xml";
$outPath = "";

$xml = @simplexml_load_string(file_get_contents("$inPath/$file"));

$modCount = 0;
		

for ($s = 0; $s < sizeof($xml->skillTree); $s++) {
	$skillTree = $xml->skillTree[$s];

//	echo "$s skillTree ".$skillTree['classId'].", size: ".sizeof($skillTree->skill)."\n";
	for ($i = 0; $i < sizeof($xml->skillTree[$s]->skill); $i++) {
		$skill = $xml->skillTree[$s]->skill[$i];

		if (empty($skill['levelUpSp']) || !isset($skill['levelUpSp']) ) continue;

		if (((int)$skill['levelUpSp']) > 5000000) {
			echo "* Reduced skill ".$skill['skillName']." (".$skill['skillId'].") SP cost from ".number_format((double)$skill['levelUpSp'])." to 5,000,000\n";
			$modCount++;
			$xml->skillTree[$s]->skill[$i]['levelUpSp'] = 5000000;

		}
	}	
}

file_put_contents("$file", $xml->asXML());
echo "Done with $modCount modifications.\n";