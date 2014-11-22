<div class="area-top clearfix">
    <div class="pull-left header">
        <h3 class="title">
            <i class="icon-user"></i>
            <?= $user['User']['username'] ?>
        </h3>
    </div>
</div>
<div class="row">
	<div class="col-lg-6">

		<div class="box">
			<div class="box-content">
				<div class="padded">
					<div class="form-group">
						<label class="control-label col-lg-2">Avatar</label>
						<div class="col-lg-10">
							<?//= $currentUser["avatar"] ?>
						</div>
					</div>
				</div>
				<div class="padded">
					<div class="form-group">
						<label class="control-label col-lg-2">Username</label>
						<div class="col-lg-10">
							<?= $user['User']['username'] ?>
						</div>
					</div>
				</div>
				<div class="padded">
					<div class="form-group">
						<label class="control-label col-lg-2">Email</label>
						<div class="col-lg-10">
							<?= $user['User']["email"] ?>
						</div>
					</div>
				</div>
			</div>
		</div>
        <ul class="action-nav-normal" style="text-align: left;">

            <li class="action-nav-button">
                <a href="/messages/send/<?= $user['User']['id'] ?>" class="tip" title="" data-original-title="Messages">
                    <i class="icon-comments-alt"></i>
                </a>
            </li>
            <? if($user['User']['id'] == $currentUser['id']) { ?>
                <li class="action-nav-button">
                    <a href="/users/edit/<?= $user['User']['id'] ?>" class="tip" title="" data-original-title="Messages">
                        <i class="icon-edit"></i>
                    </a>
                </li>
            <? } ?>

        </ul>
	</div>
</div>
