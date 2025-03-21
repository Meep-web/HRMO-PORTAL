import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("customManageModal");
    const closeModal = document.querySelector(".close-modal");
    const imageUpload = document.getElementById("imageUpload");
    const modalUserImage = document.getElementById("modalUserImage");
    const resetPasswordBtn = document.getElementById("resetPasswordBtn");
    const saveChangesBtn = document.getElementById("saveChangesBtn");

    // Open modal & fetch employee data
    document.querySelectorAll(".manage-button").forEach((button) => {
        button.addEventListener("click", function () {
            const userId = this.getAttribute("data-user-id");

            fetch(`/get-employee/${userId}`)
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) throw new Error("Employee not found.");

                    console.log("Fetched Employee Data:", data.data);

                    document.getElementById("modalUserName").textContent = data.data.name;
                    document.getElementById("modalUserRole").value = data.data.role;
                    modalUserImage.src = data.data.image || "/default-avatar.png";
                    modal.setAttribute("data-user-id", userId);

                    modal.classList.remove("hidden");
                })
                .catch((error) => {
                    console.error("Error fetching employee data:", error);
                    Swal.fire("Error!", "Failed to fetch employee data.", "error");
                });
        });
    });

    // Handle image upload preview
    imageUpload.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => (modalUserImage.src = e.target.result);
            reader.readAsDataURL(file);
        }
    });

    // Close modal
    function closeModalHandler() {
        modal.classList.add("hidden");
    }
    closeModal.addEventListener("click", closeModalHandler);
    window.addEventListener("click", (event) => {
        if (event.target === modal) closeModalHandler();
    });

    // Reset Password
    resetPasswordBtn.addEventListener("click", function () {
        const id = modal.getAttribute("data-user-id");

        if (!id) {
            Swal.fire("Error!", "User ID is missing. Please select a user.", "error");
            return;
        }

        Swal.fire({
            title: "Reset Password?",
            text: "Are you sure you want to reset this user's password?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, reset it!",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/reset-password/${id}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "application/json",
                    },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) throw new Error(data.message || "Failed to reset password.");

                        Swal.fire({
                            title: "Success!",
                            html: `The user's password has been reset.<br><strong>New Password:</strong> <code>${data.newPassword}</code>`,
                            icon: "success",
                        });
                    })
                    .catch((error) => {
                        console.error("Reset Password Error:", error);
                        Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                    });
            }
        });
    });

    // Save Changes (Update Employee)
    saveChangesBtn.addEventListener("click", function () {
        const id = modal.getAttribute("data-user-id");
        const selectedRole = document.getElementById("modalUserRole").value;
        const imageFile = document.getElementById("imageUpload").files[0];

        if (!id) {
            Swal.fire("Error!", "Employee ID is missing.", "error");
            return;
        }

        let formData = new FormData();
        formData.append("id", id);
        formData.append("role", selectedRole);
        if (imageFile) formData.append("profile_picture", imageFile);

        fetch(`/update-employee/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
            .then((response) => {
                if (!response.ok) return response.json().then((data) => Promise.reject(data));
                return response.json();
            })
            .then((data) => {
                if (!data.success) throw new Error(data.message || "Failed to update employee.");

                Swal.fire({
                    title: "Success!",
                    text: "Employee updated successfully.",
                    icon: "success",
                }).then(() => (window.location.href = "/user-accounts"));

                if (data.imagePath) modalUserImage.src = data.imagePath;
            })
            .catch((error) => {
                console.error("Error updating employee:", error);
                Swal.fire("Error!", error.message || "Something went wrong.", "error");
            });
    });
});
