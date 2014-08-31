<?php include('views/header.inc'); ?>

<div class="utama">
    <div class="judul"><img src="images/kick.png"/> kickface</div>
    <div class="frontman">
    Choose between these two!<br />
    Simply by clicking the pic, or 'I'm not interested!'
    </div>
    <div class="kotak">
        <div class="kotak1">
            <form id="ktk1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" value="<?php echo $elo->randomID_A; ?>" name="id_A" />
                <input type="hidden" value="<?php echo $elo->randomID_B; ?>" name="id_B" />
                <input type="hidden" value="Submit" name="pilihan1" />
                <a href="javascript:document.forms['ktk1'].submit();">
                	<div class="kotak1_img" style="background-image:url(images/<?php echo $elo->PlayerStatsA['URLfoto']; ?>); height:250px; -moz-border-radius:7px; -webkit-border-radius:7px;"></div>
                </a>
            </form>
            <div style="font-size:11px;">
                <?php echo $elo->PlayerStatsA['nama']; ?>
            </div>
        </div>
        <div class="circleK">or</div>
        <div class="kotak2">
            <form id="ktk2" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" value="<?php echo $elo->randomID_A; ?>" name="id_A" />
                <input type="hidden" value="<?php echo $elo->randomID_B; ?>" name="id_B" />
                <input type="hidden" value="Submit" name="pilihan2" />
                <a href="javascript:document.forms['ktk2'].submit();">
                	<div class="kotak1_img" style="background-image:url(images/<?php echo $elo->PlayerStatsB['URLfoto']; ?>); height:250px; -moz-border-radius:7px; -webkit-border-radius:7px;"></div>
                </a>
            </form>
            <div style="font-size:11px;">
                <?php echo $elo->PlayerStatsB['nama']; ?>
            </div>
        </div>
            <form id="ktk3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" value="<?php echo $elo->randomID_A; ?>" name="id_A" />
                <input type="hidden" value="<?php echo $elo->randomID_B; ?>" name="id_B" />
                <input type="hidden" value="Submit" name="pilihan3" />
                <a href="javascript:document.forms['ktk3'].submit();"><div class="button orange">I'm not interested!</div></a>
            </form>
    </div>
</div>

<?php include('views/footer.inc'); ?>