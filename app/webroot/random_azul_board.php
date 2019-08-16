<?php
	$colors = [
		'blue',
		'red',
		'yellow',
		'black',
		'white'
	];
	
	$grid = [];
	$row = 0;
	while($row < 5) {
		$row_made = true;
		foreach($colors as $color) {
			$column_found = false;
			$columns = [
				0,
				1,
				2,
				3,
				4
			];
			while(count($columns) > 0 && $column_found == false) {
				$valid_column = true;
				$k = array_rand($columns);
				$v = $columns[$k];
				
				if(isset($grid[$row][$v])) {
					$valid_column = false;
				}
				if($valid_column == true) {
					foreach($grid as $rows) {
						if($rows[$v] == $color) {
							$valid_column = false;
							break;
						}
					}
				} 
				if($valid_column == false) {
					unset($columns[$k]);
					$columns = array_values($columns);
					if(count($columns) == 0) {
						$row_made = false;
					}
				}else{
					$column_found = true;
					$grid[$row][$v] = $color;
				}
			}
			if($row_made == false) {
				break;
			}
		}
		if($row_made) {
			ksort($grid[$row]);
			$row++;
		}else{
			unset($grid[$row]);
		}
	}
	
	echo '<table>';
	foreach($grid as $row) {
		echo '<tr>';
		foreach($row as $color) {
			echo '<td><img style="width:100px; height:100px;" src="/img/azul_'.$color.'.png"></td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	
	?>