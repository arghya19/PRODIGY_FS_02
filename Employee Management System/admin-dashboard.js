// Custom Alert Class
class CustomAlert {
    constructor() {
        this.currentAlert = null;
        this.createAlertContainer();
    }

    createAlertContainer() {
        // Remove existing container if it exists
        const existingContainer = document.getElementById('custom-alert-container');
        if (existingContainer) {
            existingContainer.remove();
        }

        // Create new container
        const container = document.createElement('div');
        container.id = 'custom-alert-container';
        document.body.appendChild(container);
    }

    show(options) {
        return new Promise((resolve) => {
            const {
                title = 'Alert',
                message = '',
                type = 'warning', // 'warning', 'danger', 'success', 'info', 'error'
                showCancel = true,
                confirmText = 'Confirm',
                cancelText = 'Cancel',
                confirmationType = null, // 'type-delete' for typing confirmation
                confirmationText = 'DELETE'
            } = options;

            // Create alert HTML
            const alertHTML = `
                <div class="custom-alert-overlay" id="alert-overlay">
                    <div class="custom-alert ${type}">
                        <div class="custom-alert-header">
                            <div class="alert-icon">
                                <i class="fas ${this.getIconClass(type)}"></i>
                            </div>
                            <div class="alert-title">${title}</div>
                            <div class="alert-subtitle">${this.getSubtitle(type)}</div>
                        </div>
                        <div class="custom-alert-body">
                            <div class="alert-message">${message}</div>
                            
                            ${type === 'danger' ? `
                                <div class="alert-warning">
                                    <i class="fas fa-exclamation-triangle alert-warning-icon"></i>
                                    <div class="alert-warning-text">
                                        <strong>Warning:</strong> This action cannot be undone. All your data and account information will be permanently deleted.
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${confirmationType === 'type-delete' ? `
                                <div class="confirmation-step" id="confirmation-step">
                                    <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 10px; font-size: 14px;">
                                        Type "<strong>${confirmationText}</strong>" to confirm:
                                    </p>
                                    <input type="text" class="confirmation-input" id="confirmation-input" 
                                           placeholder="Type ${confirmationText} here..." autocomplete="off">
                                </div>
                            ` : ''}
                            
                            <div class="alert-actions">
                                ${showCancel ? `
                                    <button class="alert-btn alert-btn-cancel" id="alert-cancel">
                                        <i class="fas fa-times"></i>
                                        ${cancelText}
                                    </button>
                                ` : ''}
                                <button class="alert-btn alert-btn-${type}" id="alert-confirm" ${confirmationType ? 'disabled' : ''}>
                                    <i class="fas ${this.getConfirmIcon(type)}"></i>
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Insert into container
            const container = document.getElementById('custom-alert-container');
            if (!container) {
                this.createAlertContainer();
                const newContainer = document.getElementById('custom-alert-container');
                newContainer.innerHTML = alertHTML;
            } else {
                container.innerHTML = alertHTML;
            }

            // Get elements
            const overlay = document.getElementById('alert-overlay');
            const confirmBtn = document.getElementById('alert-confirm');
            const cancelBtn = document.getElementById('alert-cancel');
            const confirmationStep = document.getElementById('confirmation-step');
            const confirmationInput = document.getElementById('confirmation-input');

            if (!overlay) {
                console.error('Alert overlay not found');
                resolve(false);
                return;
            }

            // Show alert
            setTimeout(() => {
                overlay.classList.add('active');
            }, 10);

            // Handle confirmation input if required
            if (confirmationType === 'type-delete' && confirmationInput) {
                confirmationInput.addEventListener('input', (e) => {
                    const isValid = e.target.value.trim().toLowerCase() === confirmationText.toLowerCase();
                    confirmBtn.disabled = !isValid;
                    confirmBtn.style.opacity = isValid ? '1' : '0.5';
                    confirmBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
                });

                // Focus on input after animation
                setTimeout(() => {
                    if (confirmationStep) confirmationStep.classList.add('active');
                    confirmationInput.focus();
                }, 400);
            }

            // Handle confirm button
            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    if (confirmBtn.disabled) return;

                    // Add loading state
                    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    confirmBtn.disabled = true;

                    // Close alert with slight delay for better UX
                    setTimeout(() => {
                        this.close(overlay);
                        resolve(true);
                    }, 800);
                });
            }

            // Handle cancel button
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    this.close(overlay);
                    resolve(false);
                });
            }

            // Handle escape key
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    this.close(overlay);
                    resolve(false);
                    document.removeEventListener('keydown', handleEscape);
                }
            };
            document.addEventListener('keydown', handleEscape);

            // Handle click outside
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    this.close(overlay);
                    resolve(false);
                }
            });

            this.currentAlert = overlay;
        });
    }

    close(overlay) {
        if (overlay) {
            overlay.classList.remove('active');
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.remove();
                }
            }, 400);
        }
    }

    getIconClass(type) {
        const icons = {
            warning: 'fa-exclamation-triangle',
            danger: 'fa-trash-alt',
            success: 'fa-check-circle',
            info: 'fa-info-circle',
            error: 'fa-exclamation-circle'
        };
        return icons[type] || icons.info;
    }

    getConfirmIcon(type) {
        const icons = {
            warning: 'fa-check',
            danger: 'fa-trash-alt',
            success: 'fa-check',
            info: 'fa-check',
            error: 'fa-check'
        };
        return icons[type] || icons.info;
    }

    getSubtitle(type) {
        const subtitles = {
            warning: 'Confirmation Required',
            danger: 'Destructive Action',
            success: 'Success',
            info: 'Information',
            error: 'Error Occurred'
        };
        return subtitles[type] || subtitles.info;
    }

    // Utility methods for common alert types
    async confirmDelete(title = 'Delete Account', message = 'Are you sure you want to delete your account?') {
        return await this.show({
            title,
            message,
            type: 'danger',
            confirmText: 'Delete Forever',
            cancelText: 'Keep Account',
            confirmationType: 'type-delete',
            confirmationText: 'DELETE'
        });
    }

    async success(title = 'Success', message = 'Operation completed successfully!') {
        return await this.show({
            title,
            message,
            type: 'success',
            showCancel: false,
            confirmText: 'Got it!'
        });
    }

    async warning(title = 'Warning', message = 'Are you sure you want to continue?') {
        return await this.show({
            title,
            message,
            type: 'warning',
            confirmText: 'Continue',
            cancelText: 'Cancel'
        });
    }

    async error(title = 'Error', message = 'An error occurred') {
        return await this.show({
            title,
            message,
            type: 'error',
            showCancel: false,
            confirmText: 'OK'
        });
    }
}

// Create global instance
const customAlert = new CustomAlert();



// Show different sections
function showSection(sectionName) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });

    // Show selected section
    document.getElementById(sectionName + 'Section').style.display = 'block';

    // Update active nav link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    event.target.classList.add('active');

    // Update employee count if showing dashboard
    if (sectionName === 'dashboard') {
        updateEmployeeCount();
    }
}

// Function to fetch and set next employee ID
async function loadNextEmployeeId() {
    try {
        console.log('Fetching next employee ID...'); // Debug log
        const response = await fetch('backend/get-new-employee-id.php');
        const data = await response.json();

        console.log('Response from server:', data); // Debug log

        if (data.success && data.nextId) {
            document.getElementById('employeeId').value = data.nextId;
            console.log('Set employee ID to:', data.nextId); // Debug log
        } else {
            console.error('Failed to fetch next employee ID:', data.message);
        }
    } catch (error) {
        console.error('Error fetching next employee ID:', error);
    }
}

async function updateEmployee() {
    console.log("🚀 UPDATE EMPLOYEE FUNCTION STARTED");
    console.log("==========================================");

    const updateBtn = document.getElementById("updateBtn");
    const originalText = updateBtn.textContent;
    updateBtn.textContent = "Updating...";
    updateBtn.disabled = true;

    try {
        // === STEP 1: GET FORM DATA ===
        console.log("📝 STEP 1: Getting form data...");
        const employeeId = document.getElementById("editEmployeeId").value.trim();
        const name = document.getElementById("editName").value.trim();
        const email = document.getElementById("editEmail").value.trim();
        const phone = document.getElementById("editPhone").value.trim();
        const department = document.getElementById("editDepartment").value.trim();

        console.log("Form Data Retrieved:");
        console.log("- Employee ID:", employeeId);
        console.log("- Name:", name);
        console.log("- Email:", email);
        console.log("- Phone:", phone);
        console.log("- Department:", department);

        // === STEP 2: VALIDATION ===
        console.log("\n✅ STEP 2: Validating form data...");
        if (!name || !email || !department) {
            console.error("❌ VALIDATION FAILED: Missing required fields");
            console.log("- Name present:", !!name);
            console.log("- Email present:", !!email);
            console.log("- Department present:", !!department);
            await customAlert.error("Validation Error", "Name, Email, and Department are required!");
            return;
        }
        console.log("✅ Validation passed");

        const updateData = {
            employeeId,
            name,
            email,
            phone,
            department
        };

        // === FIXED: CORRECT URL PATH ===
        console.log("\n🎯 STEP 3: Using correct file path...");
        const targetUrl = "backend/update-employee.php"; // REMOVED the leading ./
        console.log("Target URL:", targetUrl);

        // === STEP 4: SEND UPDATE REQUEST ===
        console.log("\n📤 STEP 4: Sending POST request...");
        console.log("Request Details:");
        console.log("- URL:", targetUrl);
        console.log("- Method: POST");
        console.log("- Headers: Content-Type: application/json");
        console.log("- Body:", JSON.stringify(updateData, null, 2));

        const response = await fetch(targetUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(updateData)
        });

        console.log("\n📥 STEP 5: Processing response...");
        console.log("Response Details:");
        console.log("- Status Code:", response.status);
        console.log("- Status Text:", response.statusText);
        console.log("- OK:", response.ok);

        if (!response.ok) {
            console.error("❌ HTTP ERROR RESPONSE");
            const errorText = await response.text();
            console.error("Error response body:", errorText);

            if (response.status === 404) {
                await customAlert.error("File Not Found",
                    "update-employee.php returned 404.\n" +
                    "Check if the file exists in the backend folder.");
            } else {
                await customAlert.error("HTTP Error",
                    `Server returned ${response.status}: ${response.statusText}`);
            }
            return;
        }

        // === STEP 6: PARSE JSON RESPONSE ===
        console.log("\n📋 STEP 6: Parsing JSON response...");
        const responseText = await response.text();
        console.log("Raw response text:", responseText);

        let data;
        try {
            data = JSON.parse(responseText);
            console.log("Parsed JSON data:", data);
        } catch (parseError) {
            console.error("❌ JSON PARSE ERROR:");
            console.error("Parse error:", parseError.message);
            console.error("Raw response:", responseText);
            await customAlert.error("Invalid Response",
                "Server returned invalid JSON response.");
            return;
        }

        // === STEP 7: HANDLE SUCCESS/FAILURE ===
        console.log("\n🎉 STEP 7: Handling result...");
        if (data.success) {
            console.log("✅ UPDATE SUCCESSFUL");
            console.log("Success message:", data.message);
            if (data.affected_rows) {
                console.log("Affected rows:", data.affected_rows);
            }

            await customAlert.success("Success", "Employee updated successfully!");
            closeEditModal();
            console.log("Refreshing employee table...");
            await renderEmployeeTable();

            // Clean the URL after successful update
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        } else {
            console.error("❌ UPDATE FAILED");
            console.error("Error message:", data.message);
            await customAlert.error("Update Failed", data.message || "Failed to update employee");
        }

    } catch (error) {
        console.error("\n💥 FATAL ERROR IN UPDATE FUNCTION:");
        console.error("Error name:", error.name);
        console.error("Error message:", error.message);
        console.error("Error stack:", error.stack);

        if (error.message.includes('404')) {
            await customAlert.error("File Not Found",
                "The update-employee.php file could not be found.\n" +
                "Check if the file exists in the backend folder.");
        } else if (error.message.includes('Failed to fetch')) {
            await customAlert.error("Network Error",
                "Could not connect to the server.\n" +
                "Check if your web server is running.");
        } else {
            await customAlert.error("Unexpected Error",
                "An unexpected error occurred: " + error.message);
        }
    } finally {
        console.log("\n🏁 UPDATE FUNCTION COMPLETED");
        console.log("==========================================");
        updateBtn.textContent = originalText;
        updateBtn.disabled = false;
    }
}

// Add these event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Existing code...
    loadNextEmployeeId();
    renderEmployeeTable();
    setupSearch();

    // Clean URL on page load if it has parameters
    if (window.location.search) {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    }

    // Add event listener for modal form submission
    const editForm = document.getElementById("editEmployeeForm");
    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();
            updateEmployee();
        });
    }

    // Handle delete admin account
    const deleteBtn = document.getElementById("deleteAdmin");
    if (deleteBtn) {
        deleteBtn.addEventListener("click", function (e) {
            e.preventDefault();
            handleDeleteAdmin();
        });
    }

    // Add event listener for Enter key in modal
    const modalInputs = document.querySelectorAll("#editModal input, #editModal select");
    modalInputs.forEach(input => {
        input.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                updateEmployee();
            }
        });
    });

    // Your existing form submission code...
    const form = document.getElementById("employeeForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = {
                employeeId: document.getElementById("employeeId").value.trim(),
                name: document.getElementById("name").value.trim(),
                email: document.getElementById("email").value.trim(),
                phone: document.getElementById("phone").value.trim(),
                department: document.getElementById("department").value.trim(),
            };

            if (!formData.name) {
                customAlert.show({
                    title: "Error",
                    message: "Name is required",
                    type: "error",
                    showCancel: false
                });
                return;
            }
            if (!formData.email) {
                customAlert.show({
                    title: "Error",
                    message: "Email is required",
                    type: "error",
                    showCancel: false
                });
                return;
            }
            if (!formData.department) {
                customAlert.show({
                    title: "Error",
                    message: "Department is required",
                    type: "error",
                    showCancel: false
                });
                return;
            }

            // disable button during request
            const submitBtn = form.querySelector("button[type='submit']");
            const originalText = submitBtn.textContent;
            submitBtn.textContent = "Saving...";
            submitBtn.disabled = true;

            fetch("backend/add-employee.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;

                    if (data.success) {
                        customAlert.show({
                            title: "Success",
                            message: "Employee added successfully!",
                            type: "success",
                            showCancel: false,
                        }).then(() => {
                            form.reset();
                            loadNextEmployeeId();
                            // Clean the URL after successful update
                            if (window.history.replaceState) {
                                window.history.replaceState(null, null, window.location.pathname);
                            }
                            console.log('Employee added, refreshing ID...');
                            location.reload();
                        });
                    } else {
                        customAlert.show({
                            title: "Error",
                            message: data.message || "Something went wrong",
                            type: "error",
                            showCancel: false,
                        });
                    }
                })
                .catch(error => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    console.error("Error:", error);
                    customAlert.show({
                        title: "Error",
                        message: "Request failed: " + error,
                        type: "error",
                        showCancel: false,
                    });
                });
        });
    }
});

// Fixed closeEditModal function
function closeEditModal() {
    const modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.remove('active');

        // Clear form
        const form = document.getElementById("editEmployeeForm");
        if (form) {
            form.reset();
        }

        // Clear hidden index
        const editIndex = document.getElementById("editIndex");
        if (editIndex) {
            editIndex.value = "";
        }
    }
}

// Cancel edit mode
function cancelEdit() {
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const employeeForm = document.getElementById("employeeForm");
    if (cancelEditBtn) cancelEditBtn.style.display = 'none';
    if (employeeForm) employeeForm.reset();
}


// Close modal when clicking outside
window.addEventListener('click', function (e) {
    if (e.target === editModal) {
        closeEditModal();
    }
});

// Global variables for pagination
let currentPage = 1;
let employeesPerPage = 10;
let totalEmployees = 0;
let allEmployees = [];

// Function to render employee table
async function renderEmployeeTable() {
    try {
        console.log('Fetching employees data...');

        // Fetch employees data from backend
        const response = await fetch('backend/get-employees.php');

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Employees data received:', data);

        if (data.success) {
            allEmployees = data.employees || [];
            totalEmployees = allEmployees.length;

            // Render the table with current page data
            displayEmployeesTable();
            updatePaginationControls();
        } else {
            console.error('Failed to fetch employees:', data.message);
            showErrorInTable('Failed to load employees: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error fetching employees:', error);
        showErrorInTable('Error loading employees. Please try again later.');
    }
}

// Function to display employees in table
function displayEmployeesTable() {
    const tableBody = document.getElementById('employeeTableBody');

    if (!tableBody) {
        console.error('Employee table body not found');
        return;
    }

    // Calculate pagination
    const startIndex = (currentPage - 1) * employeesPerPage;
    const endIndex = startIndex + employeesPerPage;
    const currentEmployees = allEmployees.slice(startIndex, endIndex);

    // Clear existing table content
    tableBody.innerHTML = '';

    if (currentEmployees.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                    No employees found
                </td>
            </tr>
        `;
        return;
    }

    // Generate table rows
    currentEmployees.forEach((employee, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div style="font-weight: 600; color: #1f2937;">
                    ${escapeHtml(employee.e_id || 'N/A')}
                </div>
            </td>
            <td>
                <div style="display: flex; flex-direction: column;">
                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                        ${escapeHtml(employee.e_name || 'N/A')}
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">
                        ${escapeHtml(employee.e_email || 'N/A')}
                    </div>
                </div>
            </td>
            <td>
                <div style="color: #374151;">
                    ${escapeHtml(employee.e_phno || 'N/A')}
                </div>
            </td>
            <td>
                <div style="color: #374151;">
                    ${escapeHtml(employee.e_desig || 'N/A')}
                </div>
            </td>
            <td>
                <div style="display: flex; gap: 0.5rem;">
                    <button 
                        class="btn btn-sm btn-primary" 
                        onclick="editEmployee(${startIndex + index})"
                        title="Edit Employee"
                    >
                        Edit
                    </button>
                    <button 
                        class="btn btn-sm btn-danger" 
                        onclick="deleteEmployee('${escapeHtml(employee.e_id)}', ${startIndex + index})"
                        title="Delete Employee"
                    >
                        Delete
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Function to update pagination controls
function updatePaginationControls() {
    const totalPages = Math.ceil(totalEmployees / employeesPerPage);

    // Update pagination info
    const paginationInfo = document.getElementById('paginationInfo');
    if (paginationInfo) {
        const startIndex = (currentPage - 1) * employeesPerPage + 1;
        const endIndex = Math.min(currentPage * employeesPerPage, totalEmployees);
        paginationInfo.textContent = `Showing ${startIndex}-${endIndex} of ${totalEmployees} employees`;
    }

    // Update previous button
    const prevButton = document.getElementById('prevPage');
    if (prevButton) {
        prevButton.disabled = currentPage === 1;
    }

    // Update next button
    const nextButton = document.getElementById('nextPage');
    if (nextButton) {
        nextButton.disabled = currentPage === totalPages || totalPages === 0;
    }

    // Update page numbers
    const pageNumbers = document.getElementById('pageNumbers');
    if (pageNumbers) {
        pageNumbers.innerHTML = '';

        // Show page numbers (limit to 5 pages around current page)
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        // Adjust start page if we're near the end
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}`;
            pageButton.textContent = i;
            pageButton.onclick = () => changePage(i);
            pageNumbers.appendChild(pageButton);
        }
    }
}

// Function to change page
function changePage(newPage) {
    const totalPages = Math.ceil(totalEmployees / employeesPerPage);

    if (newPage < 1 || newPage > totalPages) {
        return;
    }

    currentPage = newPage;
    displayEmployeesTable();
    updatePaginationControls();
}

// Function to show error in table
function showErrorInTable(errorMessage) {
    const tableBody = document.getElementById('employeeTableBody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: #ef4444;">
                    ${escapeHtml(errorMessage)}
                </td>
            </tr>
        `;
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    if (text == null) return 'N/A';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Function to update employee count in dashboard
function updateEmployeeCount() {
    const totalEmployeesElement = document.getElementById('totalEmployees');
    if (totalEmployeesElement) {
        totalEmployeesElement.textContent = totalEmployees;
    }
}

// Fixed editEmployee function
function editEmployee(index) {
    const employee = allEmployees[index];
    if (!employee) return;

    // Store index in hidden input
    document.getElementById("editIndex").value = index;

    // Fill modal fields with correct property names
    document.getElementById("editEmployeeId").value = employee.e_id || '';
    document.getElementById("editName").value = employee.e_name || '';
    document.getElementById("editEmail").value = employee.e_email || '';
    document.getElementById("editPhone").value = employee.e_phno || '';

    // Fixed: Use e_desig instead of e_department to match your database
    document.getElementById("editDepartment").value = employee.e_desig || '';

    // Show modal
    const modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.add("active");
    }
}

//Delete Employee
async function deleteEmployee(employeeId, index) {
    const confirmed = await customAlert.confirmDelete(
        'Delete Employee',
        `Are you sure you want to delete employee ${employeeId}?`
    );

    if (confirmed) {
        try {
            const response = await fetch('backend/delete-employee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ employeeId })
            });

            const data = await response.json();
            if (data.success) {
                await customAlert.success("Success", "Employee deleted successfully!");
                await renderEmployeeTable();

                // Clean URL after successful deletion
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.pathname);
                }
                location.reload();
            } else {
                await customAlert.error("Delete Failed", data.message || "Failed to delete employee");
            }
        } catch (error) {
            console.error('Delete error:', error);
            await customAlert.error("Error", "Failed to delete employee");
        }
    }
}

// Search functionality
// Search functionality
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();

            if (searchTerm === '') {
                // Show all employees
                displayEmployeesTable();
            } else {
                // Filter employees
                const filteredEmployees = allEmployees.filter(employee =>
                    (employee.e_name && employee.e_name.toLowerCase().includes(searchTerm)) ||
                    (employee.e_email && employee.e_email.toLowerCase().includes(searchTerm)) ||
                    (employee.e_id && employee.e_id.toLowerCase().includes(searchTerm)) ||
                    (employee.e_phno && employee.e_phno.toLowerCase().includes(searchTerm)) ||
                    (employee.e_desig && employee.e_desig.toLowerCase().includes(searchTerm))
                );

                // Update table with filtered results
                const tableBody = document.getElementById('employeeTableBody');
                if (tableBody) {
                    tableBody.innerHTML = '';

                    if (filteredEmployees.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                                    No employees found matching "${escapeHtml(searchTerm)}"
                                </td>
                            </tr>
                        `;
                    } else {
                        filteredEmployees.forEach((employee, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <div style="font-weight: 600; color: #1f2937;">
                                        ${escapeHtml(employee.e_id || 'N/A')}
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                                            ${escapeHtml(employee.e_name || 'N/A')}
                                        </div>
                                        <div style="color: #6b7280; font-size: 0.875rem;">
                                            ${escapeHtml(employee.e_email || 'N/A')}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="color: #374151;">
                                        ${escapeHtml(employee.e_phno || 'N/A')}
                                    </div>
                                </td>
                                <td>
                                    <div style="color: #374151;">
                                        ${escapeHtml(employee.e_desig || 'N/A')}
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button 
                                            class="btn btn-sm btn-primary" 
                                            onclick="editEmployee(${allEmployees.findIndex(emp => emp.e_id === employee.e_id)})"
                                            title="Edit Employee"
                                        >
                                            Edit
                                        </button>
                                        <button 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteEmployee('${escapeHtml(employee.e_id)}', ${index})"
                                            title="Delete Employee"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    }
                }
            }
        });
    }
}

function limitTo10Digits(input) {
    // Remove any non-digit characters
    input.value = input.value.replace(/\D/g, "");

    // Limit to max 10 digits
    if (input.value.length > 10) {
        input.value = input.value.slice(0, 10);
    }
}

// In your main JavaScript file
function logout() {
    customAlert.show({
        title: "Confirm Logout",
        message: "Are you sure you want to logout?",
        type: "warning",
        confirmText: "Logout",
        cancelText: "Cancel"
    }).then((confirmed) => {
        if (confirmed) {
            // Redirect to logout.php
            window.location.href = "logout.php";
        }
    });
}

async function handleDeleteAdmin() {
    const confirmed = await customAlert.confirmDelete(
        "Delete Account",
        "Are you sure you want to permanently delete your admin account? This cannot be undone."
    );

    if (confirmed) {
        try {
            const response = await fetch("backend/delete-admin.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" }
            });

            const data = await response.json();
            if (data.success) {
                await customAlert.success("Success", "Your account has been permanently deleted.");

                // Redirect after successful deletion
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.pathname);
                }
                window.location.href = "login.html";
            } else {
                await customAlert.error("Delete Failed", data.message || "Failed to delete account");
            }
        } catch (error) {
            console.error("Delete error:", error);
            await customAlert.error("Error", "Failed to delete account");
        }
    }
}
