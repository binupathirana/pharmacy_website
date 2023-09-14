<?php include('partials/menu.php'); ?>

	<div class="main-content">
		<div class="wrapper">
			<h1>Update Category</h1>

			<br><br>
			
			<?php
				
				//Check whether the id is set or not
				if(isset($_GET['id']))
				{
					//get the id and all other details
					$id = $_GET['id'];
					
					//Create sql query to get all other details
					$sql = "SELECT * FROM tblcategory WHERE id=$id";
					
					//Execute the query
					$res = mysqli_query($con, $sql);
					
					//Count the rows to check whether the id is valid or not
					$count = mysqli_num_rows($res);
					
					if($count==1)
					{
						//get all the data
						$row = mysqli_fetch_assoc($res);
						$title = $row['title'];
						$current_image = $row['imageName'];
						$featured = $row['featured'];
						$active = $row['active'];
					}
					else
					{
						//redirect to manage category with session message
						$_SESSION['no-category-found'] = "<div class='error'>Category not Found!</div>";
						header('location:'.SITEURL.'admin/manage-category.php');
					}
				
				}
				else
				{
					//redirect to manage category page
					header('location:'.SITEURL.'admin/manage-category.php');
				}
			?>

			<!--Add Category form Starts-->
			<form action="" method="POST" enctype="multipart/form-data">
				<table class="tbl-30">
					<tr>
						<td>Title: </td>
						<td>
							<input type="text" name="title" value="<?php echo $title; ?>">
						</td>
					</tr>
					
					<tr>
						<td>Current Image: </td>
						<td>
							<?php
								if($current_image != "")
								{
									//display the image
									?>
							
									<img src="<?php echo SITEURL; ?>images/category/<?php echo $current_image; ?>" width="100px">
									<?php
								}
								else
								{
									//display message
									echo "<div class='error'>Image not Added!</div>";
								}
							?>
						</td>
					</tr>
					
					<tr>
						<td>New Image: </td>
						<td>
							<input type="file" name="image">
						</td>
					</tr>

					<tr>
						<td>Featured: </td>
						<td>
							<input <?php if($featured=="Yes"){echo "checked";}?> type="radio" name="featured" value="Yes">Yes
							<input <?php if($featured=="No"){echo "checked";}?> type="radio" name="featured" value="No">No
						</td>
					</tr>

					<tr>
						<td>Active: </td>
						<td>
							<input <?php if($active=="Yes"){echo "checked";}?> type="radio" name="active" value="Yes">Yes
							<input <?php if($active=="No"){echo "checked";}?> type="radio" name="active" value="No">No
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<input type="hidden" name="current_image" value="<?php echo $current_image; ?>">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
							<input type="submit" name="submit" value="Update Category" class="btn-secondary">
						</td>
					</tr>
				</table>
			</form>
			
			<?php
			
				if(isset($_POST['submit']))
				{
					//Get all the values from our form
					$id = $_POST['id'];
					$title = $_POST['title'];
					$current_image = $_POST['current_image'];
					$featured = $_POST['featured'];
					$active = $_POST['active'];
					
					//Updating new image if selected
					//Check whether image is selected or not
					if(isset($_FILES['image']['name']))
					{
						//Get the Image details
						$image_name = $_FILES['image']['name'];
						
						//Check whether the image is available or not
						if($image_name != "")
						{
							//Image available
							//Upload the new image and remove current image
							//Auto rename our image
							//get the extension of our image
							$ext = end(explode('.', $image_name));

							//rename the image
							$image_name = "Medicine_Category_".rand(000,999).'.'.$ext; 

							$source_path = $_FILES['image']['tmp_name'];

							$destination_path = "../images/category/".$image_name;

							//Finally upload the image
							$upload = move_uploaded_file($source_path, $destination_path);

							//check whether the image is uploaded or not
							//And if the image is not uploaded then we will stop the process and redirect with error message
							if($upload==false)
							{
								$_SESSION['upload'] = "<div class='error'>Failed to upload Image.</div>";

								//Redirect to manage category page
								header('location:'.SITEURL.'admin/manage-category.php');

								//Stop the process
								die();
							}
							
							//Remove the current image if available
							if($current_image != "")
							{
								$remove_path = "../images/category/".$current_image;
								$remove = unlink($remove_path);

								//Check whether the image is removed or not
								//If failed to remove, then display message and stop process
								if($remove == false)
								{
									//Failed to remove image
									$_SESSION['failed-remove'] = "<div class='error'>Failed to Remove Current Image.</div>";
									header('location:'.SITEURL.'admin/manage-category.php');
									die(); //stop the process
								}
							}
							
						}
						else
						{
							$image_name = $current_image;
						}
					}
					else
					{
						$image_name = $current_image;
					}
					
					//Updating the database
					$sql2 = "UPDATE tblcategory SET
						title = '$title',
						imageName = '$image_name',
						featured = '$featured',
						active = '$active'
						WHERE id=$id
					";
					
					//Execute the query
					$res2 = mysqli_query($con, $sql2);
					
					//Redirect to manage category with message
					//Check whether executed or not
					if($res2 ==  true)
					{
						//Category updated
						$_SESSION['update'] = "<div class='success'>Category Updated Successfully!</div>";
						header('location:'.SITEURL.'admin/manage-category.php');
					}
					else
					{
						//Failed to update category
						$_SESSION['update'] = "<div class='error'>Failed to Update Category!</div>";
						header('location:'.SITEURL.'admin/manage-category.php');
					}
				}
			
			?>
		
	</div>
</div>
<!--Main Content section Ends-->
	
<?php include('partials/footer.php'); ?>