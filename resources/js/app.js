import "./bootstrap";
import { PDFDocument, rgb } from "pdf-lib";
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    // Get all sidebar buttons
    const sidebarButtons = document.querySelectorAll(
        ".nosa-button, .service-records-button, .leave-credits-button, .personalDataSheet-button, .account-management-button"
    );

    // Loop through each button and add an event listener
    sidebarButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // Get the title from the data-title attribute
            const title = button.getAttribute("data-title");

            // Update the page title
            document.getElementById("page-title").innerText = title;

            // Optionally, you could update the <title> tag as well for the browser tab
            document.title = title;
        });
    });

    // Modal functionality
    const modal = document.getElementById("uploadModal");
    const uploadButton = document.getElementById("uploadButton");
    const closeButton = document.querySelector(".close");

    // Open the modal when the upload button is clicked
    if (uploadButton) {
        uploadButton.addEventListener("click", () => {
            modal.style.display = "block";
        });
    }

    // Close the modal when the close button is clicked
    if (closeButton) {
        closeButton.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    // Close the modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Add Another Row functionality
    const addAnotherRowButton = document.getElementById("addAnotherRow");
    if (addAnotherRowButton) {
        addAnotherRowButton.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent form submission

            // Get the table body
            const tableBody = document.querySelector(
                "#salaryAdjustmentTable tbody"
            );

            // Create a new row
            const newRow = document.createElement("tr");

            // Add cells with input fields to the new row
            newRow.innerHTML = `
                <td><input type="text" name="employee_name[]" placeholder="Employee Name" /></td>
                <td><input type="text" name="position[]" placeholder="Position" /></td>
                <td><input type="text" name="department[]" placeholder="Department" /></td>
                <td><input type="number" name="previous_salary[]" placeholder="Previous Salary" /></td>
                <td><input type="number" name="new_salary[]" placeholder="New Salary" /></td>
                <td><input type="date" name="date_of_effectivity[]" /></td>
                <td><input type="date" name="date_released[]" /></td>
                <td><input type="number" name="salary_grade[]" placeholder="Salary Grade" /></td>
                <td><input type="number" name="step_increment[]" placeholder="Step Increment" /></td>
            `;

            // Append the new row to the table body
            tableBody.appendChild(newRow);
        });
    }



    async function addTextToPDF(pdfUrl, textData, pageIndex = 0) {
        // Fetch the existing PDF
        const existingPdfBytes = await fetch(pdfUrl).then((res) =>
            res.arrayBuffer()
        );

        // Load the PDF document
        const pdfDoc = await PDFDocument.load(existingPdfBytes);

        // Get the first page (or specify the page index)
        const pages = pdfDoc.getPages();
        const page = pages[pageIndex];

        // Get the page dimensions
        const { width, height } = page.getSize();

        // Loop through the textData array and add each text to the page
        textData.forEach(({ text, x, y, size = 12, color = rgb(0, 0, 0) }) => {
            page.drawText(text, {
                x: x, // X coordinate (from the left)
                y: height - y, // Y coordinate (from the bottom)
                size: size, // Font size
                color: color, // Text color
            });
        });

        // Save the modified PDF
        const modifiedPdfBytes = await pdfDoc.save();

        // Create a Blob and URL for the modified PDF
        const blob = new Blob([modifiedPdfBytes], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);

        return url;
    }

    // Attach the functionality to the Generate button
    document.addEventListener("click", async function (event) {
        if (
            event.target &&
            event.target.classList.contains("generate-button")
        ) {
            const employeeId = event.target.getAttribute("data-employee-id");

            // Fetch employee data from the backend
            fetch(`/get-employee-data/${employeeId}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            `HTTP error! Status: ${response.status}`
                        );
                    }
                    return response.json();
                })
                .then((employeeData) => {
                    // Check if employeeData is defined and has the required properties
                    if (
                        !employeeData ||
                        !employeeData.newSalary ||
                        !employeeData.previousSalary
                    ) {
                        throw new Error("Invalid employee data received");
                    }

                    // Calculate the difference between newSalary and previousSalary
                    const difference =
                        employeeData.newSalary - employeeData.previousSalary;

                    const textData = [
                        {
                            text: new Date(employeeData.dateReleased).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                            }),
                            x: 400,
                            y: 190,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.employeeName,
                            x: 114,
                            y: 225,
                            size: 12,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.position,
                            x: 114,
                            y: 237,
                            size: 12,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.department,
                            x: 114,
                            y: 248,
                            size: 12,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: `Mr/Mrs: ${employeeData.employeeName}`,
                            x: 114,
                            y: 282,
                            size: 12,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: "115",
                            x: 327,
                            y: 304,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: new Date(
                                "January 3, 2018"
                            ).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }),
                            x: 390,
                            y: 304,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: new Date(
                                employeeData.dateOfEffectivity
                            ).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }),
                            x: 193,
                            y: 327,
                            size: 9,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: new Date(
                                employeeData.dateOfEffectivity
                            ).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }),
                            x: 303,
                            y: 347,
                            size: 8,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.salaryGrade.toString(),
                            x: 268,
                            y: 360,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.stepIncrement.toString(),
                            x: 316,
                            y: 360,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: parseFloat(
                                employeeData.newSalary
                            ).toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }),
                            x: 443,
                            y: 360,
                            size: 10,
                            color: rgb(0, 0, 0),
                        }, // Ensure newSalary is a number
                        {
                            text: "December 31, 2024",
                            x: 279,
                            y: 393,
                            size: 8,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.salaryGrade.toString(),
                            x: 152,
                            y: 407,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.stepIncrement.toString(),
                            x: 197,
                            y: 407,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: parseFloat(
                                employeeData.previousSalary
                            ).toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }),
                            x: 443,
                            y: 405,
                            size: 10,
                            color: rgb(0, 0, 0),
                        }, // Ensure previousSalary is a number
                        {
                            text: new Date(
                                employeeData.dateOfEffectivity
                            ).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                            }),
                            x: 290,
                            y: 439,
                            size: 8,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: difference.toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }),
                            x: 443,
                            y: 439,
                            size: 10,
                            color: rgb(0, 0, 0),
                        }, // Formatted difference
                        {
                            text: employeeData.position,
                            x: 180,
                            y: 598,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: "CESAR V. AREZA",
                            x: 388,
                            y: 562,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                        {
                            text: employeeData.salaryGrade.toString(),
                            x: 180,
                            y: 609,
                            size: 10,
                            color: rgb(0, 0, 0),
                        },
                    ];

                    // Path to the original PDF
                    const pdfUrl = "/docs/NOSA.pdf";

                    // Add text to the PDF and display it
                    addTextToPDF(pdfUrl, textData)
                        .then((modifiedPdfUrl) => {
                            // Display the modified PDF in the iframe
                            const pdfIframe =
                                document.getElementById("pdfIframe");
                            pdfIframe.setAttribute("src", modifiedPdfUrl);

                            // Open the PDF modal
                            const pdfModal =
                                document.getElementById("pdfModal");
                            pdfModal.style.display = "block";
                        })
                        .catch((error) => {
                            console.error("Error modifying PDF:", error);
                            Swal.fire({
                                icon: "error",
                                title: "PDF Generation Failed",
                                text: "Failed to generate the PDF. Please try again.",
                            });
                            
                        });
                })
                .catch((error) => {
                    console.error("Error fetching employee data:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Failed to Fetch Data",
                        text: "Failed to fetch employee data. Please try again.",
                    });
                    
                });
        }
    });

    
    
});

