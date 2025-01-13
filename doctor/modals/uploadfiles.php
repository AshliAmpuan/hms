<div class="modal fade" tabindex="-1" role="dialog" id="uploadFile<?php echo $row['id'] ?>">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
												<label for="recipient-name" class="col-form-label">Upload Results</label><br>
                        <input type="file" name="files[]" multiple="multiple" class="form-control">
											</div>
                      
                    </div>
                  </div>
                  
              </div>
              <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="upload" class="btn btn-primary">Save changes</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <?php

            if(isset($_POST['upload']))
            {
                $id = $_POST['id'];
                if(isset($_FILES['files'])){
                    // Loop through each file
                    $totalFiles = count($_FILES['files']['name']); // Get the total number of files
                    for($i = 0; $i < $totalFiles; $i++){
                        // Check if file is uploaded
                        if($_FILES['files']['error'][$i] == 0){
                            $tmp_name = $_FILES['files']['tmp_name'][$i];
                            $name = $_FILES['files']['name'][$i];
                            $size = $_FILES['files']['size'][$i];
                            $type = $_FILES['files']['type'][$i];
                            
                            // Specify the directory where you want to save the uploaded files
                            $uploadDirectory = "uploads/";
                
                            // Create the upload directory if it doesn't exist
                            if(!is_dir($uploadDirectory)){
                                mkdir($uploadDirectory, 0777, true);
                            }
                            $filename = time() . basename($name);
                            // Define the path to save the file
                            $filePath = $uploadDirectory . $filename;
                            
                            // Move the file to the upload directory
                            if(move_uploaded_file($tmp_name, $filePath)){

                                date_default_timezone_set('Asia/Manila');
                                $tdate = date("Y-m-d");

                                mysqli_query($con, "INSERT INTO reservation_results (`reservation_id`, `file`, `tdate`) VALUES ('$id', '$filename', '$tdate')");

                                // echo "File '$name' uploaded successfully!<br>";
                            } else {
                                echo "Error uploading '$name'.<br>";
                            }
                        } else {
                            echo "Error with file '$name'.<br>";
                        }
                    }
                    $reference = $_GET['reference'];
                    echo "<script>alert('Upload Success')</script>";
                    echo "<script>window.location.replace('viewtransaction.php?reference=$reference&accepted=1')</script>";
                    
                } else {
                    echo "No files uploaded.";
                }
                
            }
            
        
        ?>