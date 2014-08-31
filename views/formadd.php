<?php include('views/header.inc'); ?>

<div class="judul"><img src="images/kick.png"/> kickface</div>
<div class="">
	<div class="">
    <?php
	if (!empty($notif)) {
		foreach ($notif as $val => $key) {
			echo '<p>' . $key . '</p>';
		}
	}
	?>
    </div>
	<div class="">Add member</div>
    <div class="">
    	<form method="post"  enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF']; ?>">
        	<table class="">
            	<tr>
                	<td>
                    	Nama
                    </td>
                    <td>
                    	<input type="text" name="nama">
                    </td>
                </tr>
                	<td>
                    	Foto
                    </td>
                    <td>
                    	<input type="file" name="urlfoto">
                    </td>
                </tr>
            	<tr>
                	<td>
                    	Password
                    </td>
                    <td>
                    	<input type="password" name="pass">
                    </td>
                </tr>
            </table>
            <input type="submit" name="addmember">
        </form>
    </div>
</div>

<?php include('views/footer.inc'); ?>