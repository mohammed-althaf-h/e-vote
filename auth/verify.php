<?php
    include '   ../db.php';
    
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
    
        // Prepare the SQL statement
        $sql = "UPDATE users SET verified = 1 WHERE verification_code = ?";
        $stmt = $conn->prepare($sql);
    
        if ($stmt) {
            // Bind the parameter
            $stmt->bind_param("s", $code);
    
            // Execute the query
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "Email verified successfully. You can now login.";
                } else {
                    echo "Verification code not found.";
                }
            } else {
                echo "Error executing query: " . $stmt->error;
            }
    
            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Invalid verification code.";
    }
    ?>
