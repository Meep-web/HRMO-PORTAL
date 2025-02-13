<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information Report</title>
    <style>
        /* Add your print-specific styles here */
        @media print {
            body {
                font-family: Arial, sans-serif;
            }

            #report-container {
                width: 100%;
                padding: 20px;
                box-sizing: border-box;
            }

            h2, h3 {
                margin-bottom: 10px;
            }

            p {
                margin: 5px 0;
            }

            .no-print {
                display: none; /* Hide non-printable elements like buttons */
            }
        }
    </style>
</head>
<body>
    <div id="report-container">
        <h2>Personal Information Report</h2>
        <p><strong>Name:</strong> {{ $personalInfo->first_name }} {{ $personalInfo->last_name }}</p>
        <p><strong>Birth Date:</strong> {{ $personalInfo->date_of_birth }}</p>
        <p><strong>Gender:</strong> {{ $personalInfo->gender }}</p>
        <p><strong>Civil Status:</strong> {{ $personalInfo->civil_status }}</p>

        <h3>Family Background</h3>
        <p><strong>Spouse:</strong> {{ $familyBackground->spouseSurname }} {{ $familyBackground->spouseFirstName }}</p>
        <p><strong>Father:</strong> {{ $familyBackground->fatherSurname }} {{ $familyBackground->fatherFirstName }}</p>
        <p><strong>Mother:</strong> {{ $familyBackground->motherSurname }} {{ $familyBackground->motherFirstName }}</p>

        <h3>Educational Background</h3>
        <p><strong>Highest Level of Education:</strong> {{ $education[0]->school }} ({{ $education[0]->yearGraduated }})</p>

        <h3>Work Experience</h3>
        <p><strong>Position Title:</strong> {{ $workExperience[0]->positionTitle }}</p>
    </div>
</body>
</html>
