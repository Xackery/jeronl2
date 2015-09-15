<?php
include_once('itemdb.php');

echo "Loaded ".$itemdb->getSize()." items\n";


$etcCount = 0; //How many ETC Items were removed
$npcPath = "stats/npcs/";
$files = scandir($npcPath);

$outPath = "stats/npc/";
foreach ($files as $file) {

	if ($file == "." || $file == "..") continue;
    if (filesize("$npcPath/$file") < 100) continue;

	$xml = @simplexml_load_string(file_get_contents("$npcPath/$file"));

	if (empty($xml->npc)) continue;
	foreach ($xml->npc as $npc) {
		if (empty($npc->drop_lists->death->group)) continue;
		echo $npc['id'].": ";
		for ($g = 0; $g < sizeof($npc->drop_lists->death->group); $g++) {
			$group = $npc->drop_lists->death->group[$g];

		//	echo $npc['id']." has drops!";
			
			if ($group['chance'] < 0.5) {
				echo "Modified chance of a group drop";
				$group['chance'] = 0.5;
			}
			$wasEdited = false;
		//	echo "size before: ".sizeof($group->item);
			for ($i = 0; $i < sizeof($group->item); $i++) {
				$item = $group->item[$i];

				if (empty($item['id'])) {
					echo "Item found with no ID, removing";
					unset($group->item[$i]);
					$wasEdited = true;
					continue;
				}
				if ($item['id'] == 57) continue;
				if (strtolower($itemdb->getType($item['id'])) == strtolower("etcitem")) {
		//			echo "found etcitem, removing";
					unset($group->item[$i]);
					$etcCount++;
					$wasEdited = true;
					continue;
				}
			}

			if ($wasEdited) {
				if (sizeof($group->item) < 1) { //No items remain in group, remove group
//					echo "Removed empty group for ".$npc['id'];
					unset($npc->drop_lists->death->group[$g]);
					continue;
				}
				$newChance = round(100/sizeof($group->item),4);
				foreach ($group->item as $item) {
					$item['chance'] = $newChance;
				}
//				echo "Changed chances for items in ".$npc['id']." group to $newChance";
//				echo "Size after:".sizeof($group->item)."\n";
			}
		}

		echo "\n";
	}

	file_put_contents("$outPath/$file", $xml->asXML());
}