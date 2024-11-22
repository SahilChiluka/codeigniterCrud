
<script>
	$(document).on('click','.edit',function(e) {
		e.preventDefault();
		var id = $(this).parent().siblings()[0].value;
		$.ajax({
			url: "<?php echo base_url(); ?>"+"/getSingleUser/"+id,
			method: "GET",
			success: function(result) {
				var res = JSON.parse(result);
				$(".updateId").val(res.id);
				$(".updateUsername").val(res.username);
				$(".updateAge").val(res.age);
				$(".updateEmail").val(res.email);
			}
		})
	})

	$(document).on('click','.delete',function(e) {
		e.preventDefault();
		var id = $(this).parent().siblings()[0].value;
		var confirmation = confirm("Are you sure you want to delete?");
		if(confirmation) {
			$.ajax({
				url: "<?php echo base_url(); ?>"+"/deleteUser",
				method: "POST",
				data: {id : id},
				success: function(res) {
					if(res.includes("1")) {
						return window.location.href = window.location.href;
					}
				}
			})
		}
	})

	$(document).on('click','.delete_all_data', function() {
		var confirmation = confirm("Are you sure you want to delete?");
		if(confirmation) {
			var checkboxes = $(".data_checkbox:checked");
			console.log(checkboxes);

			if(checkboxes.length > 0) {
				var ids = [];
				checkboxes.each(function() {
					ids.push($(this).val());
				})
				console.log(ids);

				$.ajax({
					url: "<?php echo base_url(); ?>"+"/deleteMultiUser",
					method: "POST",
					data : {ids : ids},
					success: function(res) {
						if(res.includes("1")) {
							return window.location.href = window.location.href;
						}
						checkboxes.each(function() {
							$(this).parent().parent().parent().hide(100);
						})
					}
				})
			}
		}
	})
</script>

<div class="container-xl">
	<div class="table-responsive d-flex flex-column">
		<?php 
			if(session()->getFlashdata("success")) {
		?>
		<div class="alert w-50 align-self-center alert-success alert-dismissible fade show" role="alert">
			<?php echo session()->getFlashdata("success"); ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="table-wrapper">
		<div class="table-title">
    <div class="row align-items-center">
        <div class="col-sm-6">
        <h2>User Details</h2>
        </div>
        <div class="col-sm-6 text-right">
		<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i></a>
		<a href="#deleteMultiEmployeeModal" class="delete_all_data btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i><span>Delete All</span></a>
			<form>
				<input type="text" name="search" class="form-control d-inline-block" style="width: auto; display: inline-block; margin-right: 10px; height:32px;" placeholder="Search...">
				<button type="submit" class="btn btn-primary d-inline-block" style="width:auto; display: inline-block;">Search</button>					
			</form>
		</div>
    	</div>
		</div>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>
							<span class="custom-checkbox">
								<input type="checkbox" id="selectAll">
								<label for="selectAll"></label>
							</span>
						</th>
						<th>Name</th>
						<th>Age</th>
						<th>Email</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if($users) {
							foreach($users as $user) {
					?>
					<tr>
                        <input type="hidden" id="userId" name="id" value = "<?php echo $user['id']; ?>" >
						<td>
							<span class="custom-checkbox">
								<input type="checkbox" id="data_checkbox" class="data_checkbox" name="data_checkbox" value="<?php echo $user['id']; ?>">
								<label for="data_checkbox"></label>
							</span>
						</td>
						<td><?= $user['username']; ?></td>
						<td><?php echo $user['age']; ?></td>
						<td><?php echo $user['email']; ?></td>
						<td>
							<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
							<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
						</td>
					</tr>
					<?php 
							}
						}
					?>
				</tbody>
			</table>
			<?php 
				if(count($users)== 0) { 
			?>
				<h5 style="text-align:center;">No Users Found.</h5>
			<?php } ?>
			<div class="d-flex justify-content-center align-items-center">
				<ul class="pagination">
					<?= $pager->links('group', 'bs_pagination') ?>
				</ul>
			</div>
		</div>
	</div>        
</div>
<!-- Add Modal HTML -->
<div id="addEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action = "<?php echo base_url().'/saveUser'; ?>" method = "POST" >
				<div class="modal-header">						
					<h4 class="modal-title">Add User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" name="username" required>
					</div>
					<div class="form-group">
						<label>Age</label>
						<input type="text" class="form-control" name="age" required>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="email" class="form-control" name="email" required>
					</div>				
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" name="submit" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-success" value="Add">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action = "<?php echo base_url().'/updateUser'; ?>" method = "POST">
				<div class="modal-header">						
					<h4 class="modal-title">Edit User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
                    <input type="hidden" name="updateId" class = "updateId" >					
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control updateUsername" name = "username" required>
					</div>
					<div class="form-group">
						<label>Age</label>
						<input type="text" class="form-control updateAge" name = "age"  required>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="text" class="form-control updateEmail" name = "email"  required>	
                    </div>			
				</div>
				<div class="modal-footer">
					<input type="button" name = "submit" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-info" value="Save">
				</div>
			</form>
		</div>
	</div>
</div>


