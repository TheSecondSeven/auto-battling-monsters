<div class="users view">
<h2>New Stuff!</h2>
	<?php if(!empty($new_skills)) {
		echo '<h3><ul>';
		foreach($new_skills as $new_skill) {
			echo '<li>New '.$new_skill['Skill']['rarity'].' '.$new_skill['Skill']['Type']['name'].' Skill: '.$new_skill['Skill']['name'].'!<br>'.$new_skill['Skill']['description'].'</li>'; 
		}
		echo '</ul>';
	} ?>
	<?php if(!empty($new_augments)) {
		echo '<h3><ul>';
		foreach($new_augments as $new_augment) {
			echo '<li>New '.$new_augment['Augment']['rarity'].' Rune: '.$new_augment['Augment']['name'].'!<br>'.$new_augment['Augment']['description'].'</li>'; 
		}
		echo '</ul>';
	} ?>
	<?php if(!empty($new_ultimates)) {
		echo '<h3><ul>';
		foreach($new_ultimates as $new_ultimate) {
			echo '<li>New '.$new_ultimate['Ultimate']['rarity'].' '.$new_ultimate['Ultimate']['Type']['name'].' Ultimate: '.$new_ultimate['Ultimate']['name'].'!<br>'.$new_ultimate['Ultimate']['description'].'</li>'; 
		}
		echo '</ul>';
	} ?>
	
	<?php if(!empty($new_monsters)) {
		echo '<h3><ul>';
		foreach($new_monsters as $new_monster) {
			echo '<li>New '.$new_monster['Type']['name'];
			if(!empty($new_monster['SecondaryType']['name'])) {
				echo '/'.$new_monster['SecondaryType']['name'].' Dual Type';
			}
			echo ' Monster Acquired!</li>'; 
		}
		echo '</ul>';
	} ?>
	<br>
	<a href="/my-monsters/">Go To My Monsters</a>
</div>
</div>
