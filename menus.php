<?php include 'templates/header.php'; ?>

<h2>Menus</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Price</th>
      <th>Image</th>
      <th>Category</th>
      <th>Available</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Fetch all categories
    $categories_json = @file_get_contents('http://localhost:3000/api/categories');
    $categories = $categories_json ? json_decode($categories_json, true) : [];
    $categoryMap = [];

    if ($categories !== NULL) {
      foreach ($categories as $category) {
        $categoryMap[$category['category_id']] = $category['category_name'];
      }
    }

    // Fetch all menu items
    $json = @file_get_contents('http://localhost:3000/api/menu_items');
    if ($json !== FALSE) {
      $menus = json_decode($json, true);

      if ($menus !== NULL) {
        foreach ($menus as $menu) {
          $category_name = isset($categoryMap[$menu['category_id']])
            ? htmlspecialchars($categoryMap[$menu['category_id']])
            : 'Unknown';
          $isActive = $menu['is_active'] ? 'checked' : '';

          echo "<tr>
                  <td>" . htmlspecialchars($menu['item_id']) . "</td>
                  <td>" . htmlspecialchars($menu['item_name']) . "</td>
                  <td>" . htmlspecialchars($menu['price']) . "</td>
                  <td><img src='http://localhost:3000/uploads/" . 
                       htmlspecialchars($menu['image_source']) . 
                       "' alt='Menu Image' style='width: 140px; height: 140px; object-fit: cover;'></td>
                  <td>" . $category_name . "</td>
                  <td>
                    <form class='availability-form' data-id='" . htmlspecialchars($menu['item_id']) . "'>
                      <input type='checkbox' class='availability-toggle' $isActive>
                      <button type='button' class='update-availability'>Update</button>
                    </form>
                  </td>
                  <td>
                    <button class='edit-menu' 
                            data-id='" . htmlspecialchars($menu['item_id']) . "' 
                            data-name='" . htmlspecialchars($menu['item_name']) . "' 
                            data-price='" . htmlspecialchars($menu['price']) . "' 
                            data-category='" . htmlspecialchars($menu['category_id']) . "'>
                      Edit
                    </button>
                    <button class='delete-menu' data-id='" . htmlspecialchars($menu['item_id']) . "'>
                      Delete
                    </button>
                  </td>
                </tr>";
        }
      }
    }
    ?>
  </tbody>
</table>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);
  background-color:white; padding:20px; border-radius:8px; box-shadow:0px 0px 15px rgba(0, 0, 0, 0.1);">
  <h2>Edit Menu</h2>
  <form id="editForm">
    <label for="editName">Name:</label>
    <input type="text" id="editName" required><br><br>

    <label for="editPrice">Price:</label>
    <input type="number" id="editPrice" required><br><br>

    <label for="editCategory">Category:</label>
    <select id="editCategory">
  <?php
  foreach ($categories as $category) {
    echo "<option value='{$category['category_id']}'>" . 
         htmlspecialchars($category['category_name']) . "</option>";
  }
  ?>
</select><br><br>
    <label for="editImage">Image:</label>
    <input type="file" id="editImage" name="image"><br><br>
    </input>
    <button type="submit">Save Changes</button>
    <button type="button" onclick="closeModal()">Cancel</button>
  </form>
</div>

<script>
  function openModal() {
    document.getElementById('editModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    let currentItemId;

    // Handle Edit Button Click
    // Handle Edit Button Click
document.querySelectorAll('.edit-menu').forEach(button => {
  button.addEventListener('click', () => {
    currentItemId = button.getAttribute('data-id');
    const currentName = button.getAttribute('data-name');
    const currentPrice = button.getAttribute('data-price');
    const currentCategory = button.getAttribute('data-category'); // Category ID

    // Pre-fill form fields
    document.getElementById('editName').value = currentName;
    document.getElementById('editPrice').value = currentPrice;

    // Set the correct category as selected
    const editCategorySelect = document.getElementById('editCategory');
    for (let option of editCategorySelect.options) {
      if (option.value === currentCategory) {
        option.selected = true;
        break;
      }
    }

    openModal();
  });
});

    // Handle Form Submission
    document.getElementById('editForm').addEventListener('submit', (event) => {
  event.preventDefault();

  const newName = document.getElementById('editName').value;
  const newPrice = parseFloat(document.getElementById('editPrice').value);
  const newCategory = document.getElementById('editCategory').value; // Get category_id

  fetch(`http://localhost:3000/api/menu_items/${currentItemId}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      item_name: newName,
      price: newPrice,
      category_id: newCategory // Ensure category_id is sent
    })
  })
  .then(response => response.json())
  .then(data => {
    alert(data.message || 'Menu updated successfully');
    location.reload();
  })
  .catch(error => console.error('Error updating menu:', error));

  closeModal();
});

    // Handle Delete Button Click
    document.querySelectorAll('.delete-menu').forEach(button => {
      button.addEventListener('click', () => {
        const itemId = button.getAttribute('data-id');
        if (confirm('Are you sure you want to delete this menu item?')) {
          fetch(`http://localhost:3000/api/menu_items/${itemId}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`Failed to delete item with ID ${itemId}`);
            }
            return response.json();
          })
          .then(data => {
            alert(data.message || 'Menu item deleted successfully');
            location.reload();
          })
          .catch(error => {
            console.error('Error deleting menu item:', error);
            alert('Failed to delete menu item. Please try again.');
          });
        }
      });
    });

    // Handle Update Button Click for Availability
    document.querySelectorAll('.update-availability').forEach(button => {
      button.addEventListener('click', (event) => {
        const form = event.target.closest('.availability-form');
        const itemId = form.getAttribute('data-id');
        const isActive = form.querySelector('.availability-toggle').checked;

        fetch(`http://localhost:3000/api/menu_items/${itemId}/availability`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message || 'Availability updated successfully');
        })
        .catch(error => {
          console.error('Error updating availability:', error);
          alert('Failed to update availability. Please try again.');
        });
      });
    });
  });
</script>

<?php include 'templates/footer.php'; ?>
