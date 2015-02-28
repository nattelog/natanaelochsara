<?php 

include("../server.php");

$stmt = $conn->prepare("SELECT guest_id FROM households WHERE serial_no=?");
$stmt->bind_param("i", $serial_no);

$serial_no = 458177;

$stmt->execute();

//$query = mysqli_query($conn, $sql);

if (!$stmt) send_status("php_error", mysqli_error($conn));

else {
    $result = $stmt->fetch_array(MYSQLI_ASSOC);
    
    if (count($result) > 1) send_status("database_error", "There are more than one record with serialnumber: $serial_no in table 'households'.");
    
    else if (count($result) == 0) send_status("database_error", "There are no records with serialnumber: $serial_no in table 'households'");
    
    else {
        
    }
    
}

$stmt->close();
$conn->close();

?>