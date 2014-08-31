<?php

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.0 !');
}

require_once('class/elorating.class.php');

require_once('library/class.upload.php');

$notif = array();

$elo = new EloRating();

if (isset($_POST['addmember'])) {
	
	if (isset($_POST['nama']) && isset($_FILES['urlfoto'])) {
		$upl = new upload($_FILES['urlfoto']);
		if ($upl->uploaded) {
			$upl->file_new_name_body	= 'user_uploaded';
			$upl->image_resize			= true;
			$upl->image_x				= 200;
			$upl->image_ratio_y			= true;
			$upl->file_max_size			= 51200;
			$upl->allowed				= array('image/jpeg', 'image/png');
			$upl->process('images');
			if ($upl->processed) {			
				$nama = htmlspecialchars($_POST['nama']);
				$nama = ucwords($nama);
				$elo->addMember($nama, $upl->file_dst_name);
				$notif[] = 'Successfull! ' . $nama . ' is now member of kickface!';
			} else {
				$notif[] = $upl->error;
			}
			$upl->Clean();
		} else {
			$notif[] = $upl->error;
		}
	} else {
		$notif[] = 'ERROR: no information available!';
	}
}

include('views/formadd.php');

echo '<a href="index.php"<p>Back to vote</p>';