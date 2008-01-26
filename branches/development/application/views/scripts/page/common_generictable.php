<?php 
	function generictable($table, $_this) {
		if ($table == NULL)
		{
			echo "	<p>No Data!</p>";
		}
		else
		{
	?>
		<table border=1>
			<tr>
	<?php
		foreach ($table[0] as $key => $value) {
			echo "			<th>".$_this->escape($key)."</th>";
		}
	?>
			</tr>
	<?php
		foreach ($table as $row) {
			echo "		<tr>";
			foreach ($row as $key => $value) {
				echo "			<td>".$_this->escape($value)."</td>";
			}
			echo "		</tr>";
		}
	?>
		</table>
	<?php
		}
	}
?>