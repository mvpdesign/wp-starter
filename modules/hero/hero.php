<?php
$heroImage = get_sub_field('hero_image');
$heroText = get_sub_field('hero_text');
?>

<div class="Hero">
	<div class="Hero-bgImage" style="background-image: url(<?= $heroImage['url']; ?>)">
		<?= $heroText; ?>
	</div>
</div>
