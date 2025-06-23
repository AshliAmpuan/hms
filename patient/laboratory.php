<option value="#" selected disabled>Choose..</option>
<?php
    include('../include/connection.php');
    $category_id = $_GET['category_id'];
    $pet_id = isset($_GET['pet_id']) ? $_GET['pet_id'] : null;
    
    // Get pet species if pet_id is provided
    $pet_species = null;
    if($pet_id) {
        $pet_query = mysqli_query($con, "SELECT species FROM pet WHERE id = $pet_id");
        if($pet_row = mysqli_fetch_array($pet_query)) {
            $pet_species = strtolower($pet_row['species']); // Convert to lowercase for comparison
        }
    }
    
    date_default_timezone_set('Asia/Manila');
    $tdate = date("Y-m-d");
    
    // Modified query to filter by pet species
    $where_clause = "WHERE category_id = $category_id";
    
    // Add species filter if pet species is available
    if($pet_species) {
        $where_clause .= " AND (LOWER(pet_species) = '$pet_species' OR LOWER(pet_species) = 'both' OR pet_species IS NULL OR pet_species = '')";
    }
    
    $query = mysqli_query($con, "SELECT * FROM laboratory $where_clause ORDER BY laboratory_name");
    while($row = mysqli_fetch_array($query)){
        $laboratory_id = $row['id'];
        $capacity = $row['capacity_per_day'];
        $querycountlaboratory = mysqli_query($con, "SELECT laboratory.laboratory_name, laboratory.id FROM laboratory 
        INNER JOIN reservation ON reservation.laboratory_id=laboratory.id WHERE laboratory.id = '$laboratory_id' and tdate = '$tdate'");
        $countlaboratory = mysqli_num_rows($querycountlaboratory);
        if($countlaboratory < $capacity){
            // Display only laboratory name
            $laboratory_display = $row['laboratory_name'];
?>
<option value="<?php echo $row['id']; ?>"><?php echo $laboratory_display; ?></option>
<?php } } ?>