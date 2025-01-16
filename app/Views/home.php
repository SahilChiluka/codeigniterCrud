<script>
	$(document).on('click', '.edit', function (e) {
		e.preventDefault();
		var id = $(this).parent().siblings()[0].value;
		$.ajax({
			url: "<?php echo base_url(); ?>" + "/getSingleUser/" + id,
			method: "GET",
			success: function (result) {
				var res = JSON.parse(result);
				$(".updateId").val(res.id);
				$(".updateUsername").val(res.username);
				$(".updateAge").val(res.age);
				$(".updateEmail").val(res.email);
			}
		})
	});

	$(document).on('click', '.delete', function (e) {
		e.preventDefault();
		var id = $(this).parent().siblings()[0].value;
		var confirmation = confirm("Are you sure you want to delete?");
		if (confirmation) {
			$.ajax({
				url: "<?php echo base_url(); ?>" + "/deleteUser",
				method: "POST",
				data: { id: id },
				success: function (res) {
					if (res.includes("1")) {
						return window.location.href = window.location.href;
					}
				}
			})
		}
	});

	$(document).on('click', '.delete_all_data', function () {
		var confirmation = confirm("Are you sure you want to delete?");
		if (confirmation) {
			var checkboxes = $(".data_checkbox:checked");
			console.log(checkboxes);

			if (checkboxes.length > 0) {
				var ids = [];
				checkboxes.each(function () {
					ids.push($(this).val());
				})
				console.log(ids);

				$.ajax({
					url: "<?php echo base_url(); ?>" + "/deleteMultiUser",
					method: "POST",
					data: { ids: ids },
					success: function (res) {
						if (res.includes("1")) {
							return window.location.href = window.location.href;
						}
						checkboxes.each(function () {
							$(this).parent().parent().parent().hide(100);
						})
					}
				})
			}
		}
	});

	$(document).on('click', '.upload', function() {
		$.ajax({
			url: "<?php echo base_url(); ?>" + "home",
			method: "GET",
			success:function(res) {
				console.log("clicked upload");
				if (res.includes("1")) {
					setTimeout(1000, ()=> {
						return window.location = "/home";
					})
				}
			}
		})
	})

	$(document).ready(function () {
		$('.js-example-basic-single').select2();
	});

</script>

<div class="container-xl">
	<div class="table-responsive d-flex flex-column">
		<?php
		if (session()->getFlashdata("success")) {
			?>
			<div class="alert w-50 align-self-center alert-success alert-dismissible fade show" role="alert">
				<?php echo session()->getFlashdata("success"); ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<form>
					<a href="/errordata">Download error data</a>
				</form>
			</div>
		<?php } ?>
		<?php if (session()->getFlashdata('message')) { ?>
			<div class="alert w-50 align-self-center alert-success alert-dismissible fade show" role="alert">
				<?php echo session()->getFlashdata('message'); ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		<?php } ?>
		
		<div class="table-wrapper">
			<div class="table-title">
				<a style="color:red; font-size:16px" href="/logout">Logout</a>
				<div class="row align-items-center col-sm-10 d-flex">
					<div style="display:flex;" class="col-sm-6">
						<h2 style="font-size:25px; margin-left: -15px">
							<a style="color:white; text-decoration:none;" href="/home">User Details</a>
						</h2>
					</div>
					<div class="function-buttons col-sm-2 ml-5" style="">
						<div class=" text-right d-flex mt-2" style="gap:10px">
							<form>
								<div class="filter-btn  mr-5 d-flex ">
									<input type="text" name="search" class="form-control d-inline-block"
									style="padding:5px 10px; width:12rem" placeholder="Search...">
									<button type="submit " style="padding:5px 10px "
									class="btn btn-primary ">Search</button>
								</div>
							</form>
							<div class="only-btns d-flex ">
								<a aria-pressed="true" href="#addEmployeeModal" style=""
									class="btn btn-success  align-items-center d-flex justify-content-center " data-toggle="modal"><i
										class="material-icons">&#xE147;</i>Add</a>
								<a aria-pressed="true" href="#deleteMultiEmployeeModal" style=" align-items:center;"
									class="delete_all_data btn btn-danger  d-flex justify-content-center" data-toggle="modal"><i
										class="material-icons">&#xE15C;</i><span>Delete</span></a>
								<a href="#filterEmployeeModal" style="padding:5px 10px; display:flex; align-items: center;"
									class="btn btn-info  btn-lg active" aria-pressed="true" data-toggle="modal">
									Filter
								</a>
							</div>
						</div>
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
					foreach ($users as $user) {
						?>
						<tr>
							<input type="hidden" id="userId" name="id" value="<?php echo $user['id']; ?>">
							<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="data_checkbox" class="data_checkbox" name="data_checkbox"
										value="<?php echo $user['id']; ?>">
									<label for="data_checkbox"></label>
								</span>
							</td>
							<td><?= $user['username']; ?></td>
							<td><?php echo $user['age']; ?></td>
							<td><?php echo $user['email']; ?></td>
							<td>
								<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons"
										data-toggle="tooltip" title="Edit">&#xE254;</i></a>
								<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons"
										data-toggle="tooltip" title="Delete">&#xE872;</i></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php if (count($users) == 0) { ?>
				<h5 style="text-align:center;">No Users Found.</h5>
			<?php } ?>
			<div class="d-flex justify-content-center align-items-center">
				<ul class="pagination">
					<?= $pager->links('group', 'bs_pagination') ?>
				</ul>
			</div>
			<button style="background-color:transparent;">
				<a style="color: green; text-decoration:none;" href="/download">Download</a>
			</button>
			<button style="background-color:transparent;">
				<a style="color: blue; text-decoration:none;" href="#uploadData" data-toggle="modal">Upload</a>
			</button>
		</div>
	</div>
</div>
<!-- Add Modal HTML -->
<div id="addEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form>
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
			<form action="<?php echo base_url() . '/updateUser'; ?>" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">Edit User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="updateId" class="updateId">
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control updateUsername" name="username" required>
					</div>
					<div class="form-group">
						<label>Age</label>
						<input type="text" class="form-control updateAge" name="age" required>
					</div>
					<div class="form-group">
						<label>Email</label>
						<input type="text" class="form-control updateEmail" name="email" required>
					</div>
				</div>
				<div class="modal-footer">
					<input type="button" name="submit" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-info" value="Save">
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Filter Modal HTML -->
<div id="filterEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<h4 class="modal-title">Filter Data</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<select class="js-example-basic-single" name="username">
							<option value="">Filter by Name...</option>
							<?php foreach ($all_users as $user): ?>
								<option value="<?= $user['username']; ?>"><?= $user['username']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<select class="js-example-basic-single" name="age">
							<option value="">Filter by Age...</option>
							<?php foreach ($all_users as $user): ?>
								<option value="<?= $user['age']; ?>"><?= $user['age']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<select class="js-example-basic-single" name="email">
							<option value="">Filter by Email...</option>
							<?php foreach ($all_users as $user): ?>
								<option value="<?= $user['email']; ?>"><?= $user['email']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" name="submit" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-success filter" value="Filter">
				</div>
			</form>
		</div>
	</div>
</div>

<div id="uploadData" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="<?= base_url('upload') ?>" method="POST" enctype="multipart/form-data">
				<div class="modal-header">
					<h4 class="modal-title">Upload CSV File</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="file" name="uploadfile" id="uploadfile" accept=".csv">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<input type="submit" class="btn btn-success upload" value="Upload">
				</div>
			</form>
		</div>
	</div>
</div>