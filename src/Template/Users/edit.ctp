<!-- app/View/Users/add.ctp -->

<h3>Edit Profile</h3><br>

<?= $this->Form->create('User', array('id' => 'profile-form', "class" => "form-horizontal fill-up validatable")); ?>
<div class="row">
	<div class="col-lg-6">

		<div class="box">
			<div class="box-content">

				<div class="padded">

					<div class="form-group">
						<label class="control-label col-lg-2">Avatar</label>
						<div class="col-lg-10">
							<input type="file" name="avatar" class="validate[required]" data-prompt-position="topLeft"/>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-2">Email</label>
						<div class="col-lg-10">
							<input type="text" name="email" value="<?= $user->email ?>"/>
						</div>
					</div>

				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-blue">Save changes</button>
				</div>
				</form>

			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	$(document).ready( function(){
		$('#profile-form').submit( function() {
			this.submit();
		});
	});
</script>