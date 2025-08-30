<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Include database connection and utility functions
require_once __DIR__ . "/backend/db.php";
require_once __DIR__ . "/backend/utils.php";
$conn = connectDB();

// Example: Logged in user ID from session
$admin_id = (string)($_SESSION['admin_id'] ?? '');
$username = (string)($_SESSION['username'] ?? '');

// Total employees
$total_emp = (string)getTotalEmployees($conn);

$e_nextEmp_id = generateEmployeeID($conn);

$lastEmp = getLastEmployee($conn) ?? null;
$e_name_last = $lastEmp['e_name'] ?? null;
$e_lastEmp_created = $lastEmp['created_at'] ?? null;


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management Dashboard</title>
    <link rel="stylesheet" href="./css/admin-dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-logo">
                <h2>EmpManage</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('dashboard')">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active" onclick="showSection('employees')">
                        <span class="nav-icon">👥</span>
                        Employees
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="showSection('settings')">
                        <span class="nav-icon">⚙️</span>
                        Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" onclick="handleLogout()">
                        <span class="nav-icon">🚪</span>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Topbar -->
        <header class="topbar">
            <h1>Employee Management</h1>
            <div class="user-info">
                <span>
                    <?php echo htmlspecialchars($admin_id);
 ?>,
                    <?php echo htmlspecialchars($username);
;?>
                </span>
                <div class="user-avatar">USR</div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div id="successMessage" class="success-message"></div>

            <!-- Dashboard Section -->
            <section id="dashboardSection" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Dashboard Overview</h1>
                    <p class="content-subtitle">Welcome to your Employee Management System</p>
                </div>

                <!-- Dashboard Stats -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="font-size: 2.5rem; color: #3b82f6; margin-bottom: 0.5rem;" id="totalEmployees"><?php echo htmlspecialchars($total_emp);?>
                            </h3>
                            <p style="color: #6b7280; font-weight: 600;">Total Employees</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;">5</h3>
                            <p style="color: #6b7280; font-weight: 600;">Departments</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 0.5rem;">3</h3>
                            <p style="color: #6b7280; font-weight: 600;">New This Month</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h3 style="font-size: 2.5rem; color: #8b5cf6; margin-bottom: 0.5rem;">98%</h3>
                            <p style="color: #6b7280; font-weight: 600;">Employee Satisfaction</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activities</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div
                                style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                                <div
                                    style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    👤</div>
                                <div>
                                    <p style="font-weight: 600; margin: 0;">
                                        
                                        <?php
    if (!empty($e_name_last)) {
        echo 'New employee name: ' . htmlspecialchars(ucwords($e_name_last));
    } else {
        echo 'No employee found.';
    }
    ?>
                                    </p>
                                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                                        <?php
    if (!empty($e_lastEmp_created)) {
        echo 'Created at: ' . htmlspecialchars($e_lastEmp_created);
    }
    ?>
                                    </p>
                                </div>
                            </div>
                            <!-- <div
                                style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                                <div
                                    style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    ✏️</div>
                                <div>
                                    <p style="font-weight: 600; margin: 0;">Sarah Johnson profile updated</p>
                                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">5 hours ago</p>
                                </div>
                            </div>
                            <div
                                style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                                <div
                                    style="width: 40px; height: 40px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    📊</div>
                                <div>
                                    <p style="font-weight: 600; margin: 0;">Monthly report generated</p>
                                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">1 day ago</p>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Employees Section -->
            <section id="employeesSection" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">Employee Management</h1>
                    <p class="content-subtitle">Manage your team members and their information</p>
                </div>

                <!-- Add Employee Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Employee</h3>
                    </div>
                    <div class="card-body">
                        <form id="employeeForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="employeeId" class="form-label">Employee ID</label>
                                   <input type="text" id="employeeId" name="employeeId" class="form-input" value="<?php echo htmlspecialchars($e_nextEmp_id ?? ''); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-input" required oninput="limitTo10Digits(this)">
                                </div>
                                <div class="form-group">
                                    <label for="department" class="form-label">Designation</label>
                                    <select id="department" name="department" class="form-input" required>
                                        <option value="">Select Department</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Sales">Sales</option>
                                        <option value="HR">Human Resources</option>
                                        <option value="Finance">Finance</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-primary">Add Employee</button>
                                <button type="button" id="cancelEdit" class="btn btn-secondary"
                                    style="display: none;">Cancel Edit</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search and Employee List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employee Directory</h3>
                    </div>
                    <div class="card-body">
                        <div class="search-container">
                            <input type="text" id="searchInput" class="search-input"
                                placeholder="Search by name, email, or ID...">
                            <span class="search-icon">🔍</span>
                        </div>

                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name & Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTableBody">
                                    <!-- Sample data will be populated here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Controls -->
                        <div
                            style="display: flex; justify-content: between; align-items: center; margin-top: 2rem; padding: 1rem 0; border-top: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span style="color: #6b7280; font-size: 0.875rem; margin-right: 10px;" id="paginationInfo">
                                    Showing 1-10 of 30 employees
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;" id="paginationControls">
                                <button id="prevPage" class="btn btn-sm btn-secondary"
                                    onclick="changePage(currentPage - 1)" disabled>
                                    ← Previous
                                </button>
                                <span style="display: flex; gap: 0.25rem;" id="pageNumbers">
                                    <!-- Page numbers will be populated here -->
                                </span>
                                <button id="nextPage" class="btn btn-sm btn-secondary"
                                    onclick="changePage(currentPage + 1)">
                                    Next →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settingsSection" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Settings</h1>
                    <p class="content-subtitle">Configure your application preferences</p>
                </div>

                <!-- Account Management -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account Management</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <!-- Delete Account -->
                            <div
                                style="padding: 1.5rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #dc2626;">⚠️ Danger Zone</h4>
                                <p style="margin: 0 0 1rem 0; color: #7f1d1d; font-size: 0.875rem;">
                                    Once you delete your account, there is no going back. This action cannot be undone
                                    and will permanently delete your admin account and all associated data.
                                </p>
                                <div style="margin-bottom: 1rem;">
                                    <p style="margin: 0 0 0.5rem 0; color: #7f1d1d; font-weight: 600;">This action will:
                                    </p>
                                    <ul style="margin: 0; color: #7f1d1d; font-size: 0.875rem;">
                                        <li>Permanently delete your admin account</li>
                                        <li>Remove all your personal settings</li>
                                        <li>Revoke all access permissions</li>
                                        <li>Cannot be recovered or undone</li>
                                    </ul>
                                </div>
                                <button class="btn btn-danger" id="deleteAdmin">
                                    🗑️ Delete My Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

<!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Employee</h3>
                <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editEmployeeForm">
                <input type="hidden" id="editIndex">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="editEmployeeId" class="form-label">Employee ID</label>
                        <input type="text" id="editEmployeeId" name="employeeId" class="form-input" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editName" class="form-label">Full Name</label>
                        <input type="text" id="editName" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail" class="form-label">Email Address</label>
                        <input type="email" id="editEmail" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone" class="form-label">Phone Number</label>
                        <input type="tel" id="editPhone" name="phone" class="form-input" required oninput="limitTo10Digits(this)">
                    </div>
                    <div class="form-group">
                        <label for="editDepartment" class="form-label">Department</label>
                        <select id="editDepartment" name="department" class="form-input" required>
                            <option value="">Select Department</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Sales">Sales</option>
                            <option value="HR">Human Resources</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="button" id="updateBtn" class="btn btn-primary" onclick="updateEmployee()">Update Employee</button>
                </div> 
            </form>
        </div>
    </div>
        <script src="admin-dashboard.js"></script>
</body>

</html>