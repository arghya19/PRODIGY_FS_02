<?php
// Include the utils.php file to access the generateUserID function
require_once 'utils.php';
require_once 'db.php';

$conn = connectDB();
// Generate the initial User ID when page loads
$initialUserID = generateUserID($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
            padding: 20px;
            border-radius: 20px;
            position: relative;
            background-color: #1a1a1a;
            color: #fff;
            border: 1px solid #333;
        }

        .title {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -1px;
            position: relative;
            display: flex;
            align-items: center;
            padding-left: 30px;
            color: #00bfff;
        }

        .title::before {
            width: 18px;
            height: 18px;
        }

        .title::after {
            width: 18px;
            height: 18px;
            animation: pulse 1s linear infinite;
        }

        .title::before,
        .title::after {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            border-radius: 50%;
            left: 0px;
            background-color: #00bfff;
        }

        .message,
        .signin {
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.7);
        }

        .signin {
            text-align: center;
        }

        .signin a:hover {
            text-decoration: underline royalblue;
        }

        .signin a {
            color: #00bfff;
        }

        .flex {
            display: flex;
            width: 100%;
            gap: 6px;
        }

        .form label {
            position: relative;
        }

        .form label .input {
            background-color: #333;
            color: #fff;
            width: 100%;
            padding: 20px 45px 05px 10px;
            outline: 0;
            border: 1px solid rgba(105, 105, 105, 0.397);
            border-radius: 10px;
        }

        .form label:not(.password-field) .input {
            padding: 20px 10px 05px 10px;
        }

        .form label .input+span {
            color: rgba(255, 255, 255, 0.5);
            position: absolute;
            left: 10px;
            top: 0px;
            font-size: 0.9em;
            cursor: text;
            transition: 0.3s ease;
        }

        .form label .input:placeholder-shown+span {
            top: 12.5px;
            font-size: 0.9em;
        }

        .form label .input:focus+span,
        .form label .input:valid+span {
            color: #00bfff;
            top: 0px;
            font-size: 0.7em;
            font-weight: 600;
        }

        .input {
            font-size: medium;
        }

        .form label .input:read-only {
            background-color: #2a2a2a;
            color: rgba(255, 255, 255, 0.7);
            cursor: not-allowed;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .submit {
            border: none;
            outline: none;
            padding: 10px;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transition: .3s ease;
            background-color: #00bfff;
            flex: 1;
        }

        .submit:hover {
            background-color: #00bfff96;
        }

        .clear-btn {
            border: none;
            outline: none;
            padding: 10px;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transition: .3s ease;
            background-color: #666;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .clear-btn:hover {
            background-color: #777;
        }

        .password-field {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.5);
            font-size: 16px;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .toggle-password:hover {
            background: rgba(0, 191, 255, 0.1);
            color: #00bfff;
        }

        @keyframes pulse {
            from {
                transform: scale(0.9);
                opacity: 1;
            }

            to {
                transform: scale(1.8);
                opacity: 0;
            }
        }

        /* Custom Alert Modal Styles */
        .custom-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 3000;
        }

        .custom-alert-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .custom-alert {
            background: linear-gradient(145deg, rgba(30, 30, 30, 0.98), rgba(20, 20, 20, 0.98));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(245, 101, 101, 0.3);
            border-radius: 20px;
            width: 90%;
            max-width: 450px;
            padding: 0;
            transform: scale(0.7) translateY(50px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(245, 101, 101, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .custom-alert-overlay.active .custom-alert {
            transform: scale(1) translateY(0);
        }

        .custom-alert-header {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.15), rgba(229, 62, 62, 0.1));
            padding: 25px 30px 20px;
            border-bottom: 1px solid rgba(245, 101, 101, 0.2);
            text-align: center;
            position: relative;
        }

        .custom-alert-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f56565, #e53e3e, #c53030);
        }

        .alert-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #f56565, #e53e3e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            animation: pulse-danger 2s ease-in-out infinite;
            box-shadow:
                0 8px 25px rgba(245, 101, 101, 0.4),
                0 0 0 0 rgba(245, 101, 101, 0.7);
        }

        @keyframes pulse-danger {
            0% {
                box-shadow:
                    0 8px 25px rgba(245, 101, 101, 0.4),
                    0 0 0 0 rgba(245, 101, 101, 0.7);
            }

            50% {
                box-shadow:
                    0 8px 35px rgba(245, 101, 101, 0.6),
                    0 0 0 10px rgba(245, 101, 101, 0);
            }

            100% {
                box-shadow:
                    0 8px 25px rgba(245, 101, 101, 0.4),
                    0 0 0 0 rgba(245, 101, 101, 0);
            }
        }

        .alert-title {
            font-size: 24px;
            font-weight: 700;
            color: #f56565;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .alert-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .custom-alert-body {
            padding: 30px;
            text-align: center;
        }

        .alert-message {
            font-size: 16px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 25px;
            font-weight: 400;
        }

        .alert-warning {
            background: rgba(245, 101, 101, 0.1);
            border: 1px solid rgba(245, 101, 101, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-warning-icon {
            color: #f56565;
            font-size: 18px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .alert-warning-text {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.5;
            text-align: left;
        }

        .alert-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .alert-btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .alert-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .alert-btn:hover::before {
            left: 100%;
        }

        .alert-btn-cancel {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-btn-cancel:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.1));
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
        }

        .alert-btn-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
            border: 1px solid rgba(229, 62, 62, 0.3);
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        .alert-btn-danger:hover {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(245, 101, 101, 0.5);
        }

        .alert-btn-danger:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        /* Success Alert Styles */
        .custom-alert.success {
            border-color: rgba(72, 187, 120, 0.3);
        }

        .custom-alert.success .custom-alert-header {
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.15), rgba(56, 161, 105, 0.1));
            border-bottom-color: rgba(72, 187, 120, 0.2);
        }

        .custom-alert.success .custom-alert-header::before {
            background: linear-gradient(90deg, #48bb78, #38a169, #2f855a);
        }

        .custom-alert.success .alert-icon {
            background: linear-gradient(135deg, #48bb78, #38a169);
            animation: pulse-success 2s ease-in-out infinite;
        }

        @keyframes pulse-success {
            0% {
                box-shadow:
                    0 8px 25px rgba(72, 187, 120, 0.4),
                    0 0 0 0 rgba(72, 187, 120, 0.7);
            }

            50% {
                box-shadow:
                    0 8px 35px rgba(72, 187, 120, 0.6),
                    0 0 0 10px rgba(72, 187, 120, 0);
            }

            100% {
                box-shadow:
                    0 8px 25px rgba(72, 187, 120, 0.4),
                    0 0 0 0 rgba(72, 187, 120, 0);
            }
        }

        .custom-alert.success .alert-title {
            color: #48bb78;
        }

        /* Confirmation Step Styles */
        .confirmation-step {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .confirmation-step.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .confirmation-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(245, 101, 101, 0.3);
            border-radius: 10px;
            color: white;
            font-size: 15px;
            text-align: center;
            font-weight: 500;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .confirmation-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: #f56565;
            box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.2);
        }

        .confirmation-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
            font-weight: 400;
        }


        @media (max-width: 600px) {
            .form {
                max-width: 300px;
                padding: 15px;
            }

            .title {
                font-size: 24px;
            }

            .custom-alert {
                width: 95%;
                margin: 20px;
            }

            .custom-alert-header {
                padding: 25px 20px 15px;
            }

            .custom-alert-body {
                padding: 0 20px 25px;
            }

            .alert-actions {
                flex-direction: column;
            }

            .alert-btn {
                min-width: auto;
            }
        }
    </style>
</head>

<body>
    <form class="form" id="adminRegisterForm">
        <p class="title">Admin Register</p>
        <p class="message">Create admin account and get full system access.</p>

        <label>
            <input class="input" type="text" id="u_id" name="u_id" placeholder="" required readonly value="<?php echo htmlspecialchars($initialUserID); ?>">
            <span>User ID</span>
        </label>

        <label>
            <input class="input" type="text" id="u_name" name="u_name" placeholder="" required>
            <span>Full Name</span>
        </label>

        <label class="password-field">
            <input class="input" type="password" id="u_pass" name="u_pass" placeholder="" required>
            <span>Password</span>
            <button type="button" class="toggle-password" onclick="togglePassword()">
                <i class="fa-solid fa-eye"></i>
            </button>
        </label>

        <div class="button-group">
            <button type="button" class="clear-btn" id="clearBtn">
                <i class="fa-solid fa-broom"></i> Clear All
            </button>
            <button type="submit" class="submit">Create Admin</button>
        </div>
    </form>

    <script>
        // Custom Alert Class
        class CustomAlert {
            constructor() {
                this.currentAlert = null;
                this.createAlertContainer();
            }

            createAlertContainer() {
                const existingContainer = document.getElementById('custom-alert-container');
                if (existingContainer) {
                    existingContainer.remove();
                }

                const container = document.createElement('div');
                container.id = 'custom-alert-container';
                document.body.appendChild(container);
            }

            getIconClass(type) {
                const icons = {
                    success: 'fa-check',
                    error: 'fa-times',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle',
                    danger: 'fa-skull-crossbones'
                };
                return icons[type] || 'fa-info-circle';
            }

            getSubtitle(type) {
                const subtitles = {
                    success: 'Success',
                    error: 'Error',
                    warning: 'Warning',
                    info: 'Information',
                    danger: 'Danger'
                };
                return subtitles[type] || 'Alert';
            }

            getConfirmIcon(type) {
                const icons = {
                    success: 'fa-check',
                    error: 'fa-times',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle',
                    danger: 'fa-trash-alt'
                };
                return icons[type] || 'fa-check';
            }

            show(options) {
                return new Promise((resolve) => {
                    const {
                        title = 'Alert',
                        message = '',
                        type = 'warning',
                        showCancel = true,
                        confirmText = 'Confirm',
                        cancelText = 'Cancel',
                        confirmationType = null,
                        confirmationText = 'DELETE'
                    } = options;

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
                                                <strong>Warning:</strong> This action cannot be undone. All your data, messages, and account information will be permanently deleted.
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

                    const container = document.getElementById('custom-alert-container');
                    if (!container) {
                        this.createAlertContainer();
                        const newContainer = document.getElementById('custom-alert-container');
                        newContainer.innerHTML = alertHTML;
                    } else {
                        container.innerHTML = alertHTML;
                    }

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

                    setTimeout(() => {
                        overlay.classList.add('active');
                    }, 10);

                    if (confirmationType === 'type-delete' && confirmationInput) {
                        confirmationInput.addEventListener('input', (e) => {
                            const isValid = e.target.value.trim().toLowerCase() === confirmationText.toLowerCase();
                            confirmBtn.disabled = !isValid;
                            confirmBtn.style.opacity = isValid ? '1' : '0.5';
                            confirmBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
                        });

                        setTimeout(() => {
                            if (confirmationStep) confirmationStep.classList.add('active');
                            confirmationInput.focus();
                        }, 400);
                    }

                    if (confirmBtn) {
                        confirmBtn.addEventListener('click', () => {
                            if (confirmBtn.disabled) return;

                            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                            confirmBtn.disabled = true;

                            setTimeout(() => {
                                this.close(overlay);
                                resolve(true);
                            }, 800);
                        });
                    }

                    if (cancelBtn) {
                        cancelBtn.addEventListener('click', () => {
                            this.close(overlay);
                            resolve(false);
                        });
                    }

                    const handleEscape = (e) => {
                        if (e.key === 'Escape') {
                            this.close(overlay);
                            resolve(false);
                            document.removeEventListener('keydown', handleEscape);
                        }
                    };
                    document.addEventListener('keydown', handleEscape);

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
                        const container = document.getElementById('custom-alert-container');
                        if (container) {
                            container.innerHTML = '';
                        }
                    }, 300);
                }
                this.currentAlert = null;
            }

            closeCurrentAlert() {
                if (this.currentAlert) {
                    this.close(this.currentAlert);
                }
            }
        }

        // Initialize custom alert
        const customAlert = new CustomAlert();

       // AJAX function to get new User ID from server
        async function getNewUserID() {
            try {
                const response = await fetch('get-new-userid.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data && result.data.next_user_id) {
                        return result.data.next_user_id;
                    }
                }
                return null;
            } catch (error) {
                console.error('Error fetching new User ID:', error);
                return null;
            }
        }

        // Global variables
        let registeredUsers = [];
        let isSubmitting = false;
        let formInitialized = false;

        // Global functions for onclick handlers
        function togglePassword() {
            const passwordField = document.getElementById('u_pass');
            const toggleBtn = document.querySelector('.toggle-password i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleBtn.className = 'fa-solid fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleBtn.className = 'fa-solid fa-eye';
            }
        }

        function validateForm(formData) {
            console.log('Validating form:', formData);

            if (!formData.u_id || !formData.u_name || !formData.u_pass) {
                return 'Please fill in all required fields.';
            }

            if (registeredUsers.some(user => user.u_id === formData.u_id)) {
                return 'User ID already exists. Please choose a different one.';
            }

            if (formData.u_pass.length < 6) {
                return 'Password must be at least 6 characters long.';
            }

            return null;
        }

        // Function to clear form fields and set new User ID
        async function clearFormAndSetNewID() {
            // Clear name and password fields
            document.getElementById('u_name').value = '';
            document.getElementById('u_pass').value = '';
            
            // Reset password field to hidden state
            const passwordField = document.getElementById('u_pass');
            const toggleBtn = document.querySelector('.toggle-password i');
            if (passwordField.type === 'text') {
                passwordField.type = 'password';
                toggleBtn.className = 'fa-solid fa-eye';
            }
            
            // Get and set new User ID
            const newUserID = await getNewUserID();
            if (newUserID) {
                document.getElementById('u_id').value = newUserID;
                console.log('New User ID set:', newUserID);
            } else {
                console.error('Failed to get new User ID from server');
                // Fallback: you might want to show an error or keep the current ID
            }
            
            // Focus on name field for next entry
            document.getElementById('u_name').focus();
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Form submit handler called');

            if (isSubmitting) {
                console.log('Already submitting, ignoring...');
                return false;
            }

            isSubmitting = true;
            console.log('Setting isSubmitting to true');

            const formData = {
                u_id: document.getElementById('u_id').value.trim(),
                u_name: document.getElementById('u_name').value.trim(),
                u_pass: document.getElementById('u_pass').value.trim()
            };

            console.log('Form data:', formData);

            // Client-side validation
            if (!formData.u_id || !formData.u_name || !formData.u_pass) {
                await customAlert.show({
                    title: 'Validation Error',
                    message: 'Please fill in all required fields.',
                    type: 'error',
                    showCancel: false,
                    confirmText: 'OK'
                });
                isSubmitting = false;
                return false;
            }

            if (formData.u_pass.length < 6) {
                await customAlert.show({
                    title: 'Validation Error',
                    message: 'Password must be at least 6 characters long.',
                    type: 'error',
                    showCancel: false,
                    confirmText: 'OK'
                });
                isSubmitting = false;
                return false;
            }

            try {
                // Show loading state
                const submitBtn = document.querySelector('.submit');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Admin...';
                submitBtn.disabled = true;

                // Send data to backend
                const response = await fetch('admin-register-submit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                if (result.success) {
                    // Show success message
                    await customAlert.show({
                        title: 'Success!',
                        message: result.message,
                        type: 'success',
                        showCancel: false,
                        confirmText: 'Great!'
                    });

                    // Clear form and set new User ID
                    if (result.data && result.data.next_user_id) {
                        // Use the User ID returned from the server
                        document.getElementById('u_name').value = '';
                        document.getElementById('u_pass').value = '';
                        document.getElementById('u_id').value = result.data.next_user_id;
                        
                        // Reset password field to hidden state
                        const passwordField = document.getElementById('u_pass');
                        const toggleBtn = document.querySelector('.toggle-password i');
                        if (passwordField.type === 'text') {
                            passwordField.type = 'password';
                            toggleBtn.className = 'fa-solid fa-eye';
                        }
                        
                        console.log('New User ID set from server:', result.data.next_user_id);
                    } else {
                        // Fallback: Clear form and get new User ID separately
                        await clearFormAndSetNewID();
                    }

                    // Focus on name field for next entry
                    document.getElementById('u_name').focus();
                    
                } else {
                    // Show error message from server
                    await customAlert.show({
                        title: 'Registration Failed',
                        message: result.message || 'An error occurred while creating the admin account.',
                        type: 'error',
                        showCancel: false,
                        confirmText: 'OK'
                    });
                }

            } catch (error) {
                console.error('Error submitting form:', error);
                
                // Restore button state
                const submitBtn = document.querySelector('.submit');
                submitBtn.innerHTML = 'Create Admin';
                submitBtn.disabled = false;
                
                await customAlert.show({
                    title: 'Connection Error',
                    message: 'Unable to connect to the server. Please check your connection and try again.',
                    type: 'error',
                    showCancel: false,
                    confirmText: 'OK'
                });
            }
            
            isSubmitting = false;
            return false;
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded');

            if (formInitialized) {
                console.log('Form already initialized');
                return;
            }
            formInitialized = true;

            // User ID is already set from PHP, no need to generate here

            const form = document.getElementById('adminRegisterForm');
            if (form) {
                console.log('Adding submit event listener');
                form.addEventListener('submit', handleFormSubmit);
                form.onsubmit = function () { return false; };
            }

            document.getElementById('clearBtn').addEventListener('click', async function () {
                const result = await customAlert.show({
                    title: 'Clear All Fields?',
                    message: 'Are you sure you want to clear all the form fields? This action cannot be undone.',
                    type: 'warning',
                    showCancel: true,
                    confirmText: 'Clear All',
                    cancelText: 'Cancel'
                });

                if (result) {
                    // Clear form and get new User ID
                    await clearFormAndSetNewID();
                }
            });
        });
    </script>
</body>

</html>