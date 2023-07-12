<?php
include_once "config.php";

// Assuming $outgoing_id is defined and set correctly

$sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} ORDER BY user_id DESC";
$query = mysqli_query($conn, $sql);
$output = "";

if (mysqli_num_rows($query) == 0) {
    $output .= "No users are available to chat";
} elseif (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $user_id = $row['unique_id'];
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$outgoing_id} AND outgoing_msg_id = {$user_id})
                 OR (incoming_msg_id = {$user_id} AND outgoing_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);

        if ($query2) {
            if (mysqli_num_rows($query2) > 0) {
                $row2 = mysqli_fetch_assoc($query2);
                $result = $row2['msg'];
                $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;
                $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "You: " : "";
            } else {
                $msg = "No message available";
                $you = "";
            }

            $offline = ($row['status'] == "Offline now") ? "offline" : "";
            $hid_me = ($outgoing_id == $row['unique_id']) ? "hide" : "";

            $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                        <div class="content">
                        <img src="php/images/'. $row['img'] .'" alt="">
                        <div class="details">
                            <span>'. $row['fname']. " " . $row['lname'] .'</span>
                            <p>'. $you . $msg .'</p>
                        </div>
                        </div>
                        <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                    </a>';
        } else {
            // Handle the case when the query fails
            $output .= "Error retrieving messages: " . mysqli_error($conn);
        }
    }
}

echo $output;
?>
