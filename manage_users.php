<?php 
session_start();

// Check if the user is logged in and is an owner
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'owner') {
    // Redirect to the login page if not logged in or not an owner
    header("Location: login.php");
    exit();
}

require 'vendor/autoload.php';
use GuzzleHttp\Client;

include 'templates/header.php'; 
?>

<h2>Manage Users</h2>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Role</th>
            <th>Verified</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $client = new Client();

        // Fetch users from the API
        try {
            $response = $client->get('http://localhost:3000/api/users');
            $users = json_decode($response->getBody(), true);

            if (is_array($users) && !empty($users)) {
                foreach ($users as $user) {
                    // Only show users that are not members or owners
                    if ($user['role'] !== 'member' && $user['role'] !== 'owner') {
                        $verifiedStatus = $user['verified'] ? 'Yes' : 'No';
                        echo "<tr>
                                <td>{$user['member_id']}</td>
                                <td>{$user['name']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['username']}</td>
                                <td>{$user['role']}</td>
                                <td>{$verifiedStatus}</td>
                                <td>
                                    <button onclick=\"verifyUser('{$user['member_id']}')\" " . ($user['verified'] ? 'disabled' : '') . ">Verify</button>
                                    <button onclick=\"deleteUser('{$user['member_id']}')\">Delete</button>
                                </td>
                              </tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='7'>No users found.</td></tr>";
            }
        } catch (Exception $e) {
            echo "<tr><td colspan='7'>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
function verifyUser(memberId) {
    fetch('http://localhost:3000/api/verifyUser', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user_id: memberId }) // use memberId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User verified successfully');
            location.reload();  // Refresh the page to update the user list
        } else {
            alert('Error verifying user: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error verifying user: ' + error);
    });
}

function deleteUser(memberId) {
    console.log('Attempting to delete user with ID:', memberId); // Log for debugging
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('http://localhost:3000/api/deleteUser', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: memberId }) // use memberId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully');
                location.reload();  // Refresh the page to update the user list
            } else {
                alert('Error deleting user: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error deleting user: ' + error);
        });
    }
}
</script>

<?php include 'templates/footer.php'; ?>
