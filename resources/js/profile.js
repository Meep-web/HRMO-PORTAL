import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    const profileImageInput = document.getElementById("profileImageInput");
    const profileImage = document.getElementById("profileImage");
    const saveProfileButton = document.querySelector(".save-btn");
    const openModalBtn = document.getElementById("openModalBtn");
    const modal = document.getElementById("profileModal");
    const closeModalBtn = document.querySelector(".close-btn");

    // Handle profile image preview
    if (profileImageInput && profileImage) {
        profileImageInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    profileImage.src = e.target.result; // Show preview
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Save profile
    if (saveProfileButton) {
        saveProfileButton.addEventListener("click", saveProfile);
    }

    function saveProfile() {
        const employeeName = document.getElementById("employeeName").value;
        const fileInput = document.getElementById("profileImageInput");

        let formData = new FormData();
        formData.append("employeeName", employeeName);
        if (fileInput.files.length > 0) {
            formData.append("profileImage", fileInput.files[0]); // Append image file
        }

        fetch("/profile/update", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: formData, // Send as FormData (not JSON)
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Profile Updated!",
                        text: "Your profile has been updated successfully.",
                    }).then(() => {
                        window.location.href = "/profile"; // Redirect to profile
                    });

                    if (data.newImage) {
                        profileImage.src = data.newImage; // Update profile image immediately
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Update Failed",
                        text: data.message,
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                });
            });
    }

    if (openModalBtn && modal) {
        openModalBtn.addEventListener("click", function () {
            modal.style.display = "block"; // Show modal
        });
    }

    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener("click", function () {
            modal.style.display = "none"; // Hide modal
        });

        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none"; // Hide when clicking outside
            }
        });
    }

    // Toggle password visibility
    document.querySelectorAll(".toggle-password").forEach((icon) => {
        icon.addEventListener("click", function () {
            let targetId = this.getAttribute("data-target");
            let passwordField = document.getElementById(targetId);

            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                this.classList.replace("fa-eye-slash", "fa-eye");
            }
        });
    });

    document
    .getElementById("changePasswordForm")
    .addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        let password = document.getElementById("password").value;
        let confirmPassword = document.getElementById("confirmPassword").value;

        let errors = [];

        // Check for minimum length
        if (password.length < 8) {
            errors.push("Password must be at least 8 characters long.");
        }

        // Check for at least one uppercase letter
        if (!/[A-Z]/.test(password)) {
            errors.push("Password must contain at least one uppercase letter.");
        }

        // Check for at least one lowercase letter
        if (!/[a-z]/.test(password)) {
            errors.push("Password must contain at least one lowercase letter.");
        }

        // Display errors if any
        if (errors.length > 0) {
            Swal.fire("Error", errors.join("<br>"), "error");
            return;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            Swal.fire("Error", "Passwords do not match!", "error");
            return;
        }

        fetch("/update-password", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                password,
                password_confirmation: confirmPassword,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire("Success", data.success, "success");
                    document.getElementById("profileModal").style.display = "none"; // Close modal
                } else {
                    Swal.fire("Error", data.error || "An error occurred", "error");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                Swal.fire("Error", "Something went wrong!", "error");
            });
    });

});
