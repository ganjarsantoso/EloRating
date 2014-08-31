<?php include('views/header.inc'); ?>

<div class="judul"><img src="images/kick.png"/> kickface</div>
<div class="datagrid">
    <div class="frontman">Thanks for your vote!</div>
    <div class="frontman">Here's our top 10 list</div>
	<table>
    	<thead>
    	<tr>
        	<td>No</td>
            <td>Nama</td>
            <td>Point</td>
            <td>Level of beauty</td>
        </tr>
        </thead>
        <tbody>
   		<?php
		if ($elo->databaseConnection()) {
			$query_user = $elo->db_connection->prepare('SELECT * FROM members ORDER BY EloPoint DESC LIMIT 10');
			$query_user->execute();
						
			$i=1;
			while ($row = $query_user->fetch()) {
				echo '<tr>';
				echo '<td>' . $i++ . '</td>';
				echo '<td>' . $row['nama'] . '</td>';
				echo '<td>' . round($row['EloPoint'],1) . '</td>';
				echo '<td>' . $elo->beautyLevel($row['EloPoint']) . '</td>';
				echo '</tr>';
			}			
		}
		?>
        </tbody>
    </table>
</div>

<div class="frontman"><a href="">Back to vote!</a></div>

<?php include('views/footer.inc');            	