<?php
	$output = `rm -rf *`;
	echo "<br>".$output;
	$output = `git clone -b Development https://github.com/The-Problem/The-Problem.git`;
	echo "<br>".$output;
	$output = `mv The-Problem/* ..`;
	echo "<br>".$output;
	$output = `rm -rf The-Problem`;
	echo "<br>".$output;
	echo '<h2>Updated with GitHub Repository</h2>';
?>
