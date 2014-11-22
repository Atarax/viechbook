<!-- app/View/Users/add.ctp -->
<div class="users form">
	<?/*php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
		<?php echo $this->Form->input('username');
		echo $this->Form->input('password');
		echo $this->Form->input('role', array(
			'options' => array('admin' => 'Admin', 'author' => 'Author')
		));
		?>
    </fieldset>
	<?php echo $this->Form->end(__('Submit')); */?>
</div>

<h3>Registration</h3><br>

<?= $this->Form->create('User', array("class" => "form-horizontal fill-up validatable")); ?>

<div class="row">
    <div class="col-lg-6">

        <div class="box">
            <div class="box-content">

               <? /* <form class="form-horizontal fill-up validatable"> */ ?>

                    <div class="padded">

                        <div class="form-group">
                            <label class="control-label col-lg-2">Username</label>
                            <div class="col-lg-10">
                                <input type="text" name="username" class="validate[required]" data-prompt-position="topLeft"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">Email</label>
                            <div class="col-lg-10">
								<?//= $this->Form->input('', array( "id" => "username", "class" => "validate[required]")) ?>
                                <input type="text" name="email" class="validate[required]" data-prompt-position="topLeft"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">Password</label>
                            <div class="col-lg-10">
                                <input type="password" name="password" id="password" class="validate[required,minSize[4]]">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">Confirmation</label>
                            <div class="col-lg-10">
                                <input type="password" id="password_confirmation" class="validate[required,equals[password],minSize[4]]"/>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-blue">Save changes</button>
                        <button type="button" class="btn btn-default">Cancel</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>