<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .avatar-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 3px solid #3498db;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .editable {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        .editable:hover {
            background-color: #f9f9f9;
        }
        .editable:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52,152,219,0.5);
        }
        .add-field {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .add-field:hover {
            background-color: #2980b9;
        }
        .custom-field {
            display: flex;
            margin-bottom: 10px;
        }
        .custom-field input {
            flex: 1;
            margin-right: 10px;
        }
        .remove-field {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .success-message {
            color: #2ecc71;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    @if (isset($profile))
    <div class="profile-container">
        <div class="avatar-container">
            <img id="avatar" class="avatar" src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : '/placeholder.svg' }}" alt="Profile Avatar">
            <input type="file" id="avatarUpload" style="display: none;">
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <div id="name" class="editable" contenteditable="true" data-field="name">{{ $profile->name ?? 'Click to add name' }}</div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <div id="email" class="editable" contenteditable="true" data-field="email">{{ $profile->email ?? 'Click to add email' }}</div>
        </div>
        <div class="form-group">
            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" class="editable" data-field="birthday" value="{{ $profile->birthday ? $profile->birthday->format('Y-m-d') : '' }}">
        </div>
        <div class="form-group">
            <label>Custom Fields:</label>
            <div id="customFields">
                <!-- Existing custom fields, if any -->
                @if (isset($profile->bio))
                    @foreach ($profile->bio as $customField)
                    <div class="custom-field">
                        <input type="text" class="custom-field-name" placeholder="Field Name" value="{{ $customField['name'] }}">
                        <input type="text" class="custom-field-content" placeholder="Field Content" value="{{ $customField['content'] }}">
                        <button class="remove-field">Remove</button>
                    </div>
                    @endforeach
                @endif
            </div>
            <button id="addCustomField" class="add-field">+ Add Custom Field</button>
        </div>
        <div id="successMessage" class="success-message" style="display: none;"></div>
    </div>
    @else
    <div class="profile-container">
        <div class="avatar-container">
            <img id="avatar" class="avatar" src="/placeholder.svg" alt="Profile Avatar">
            <input type="file" id="avatarUpload" style="display: none;">
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <div id="name" class="editable" contenteditable="true">Click to add name</div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <div id="email" class="editable" contenteditable="true">Click to add email</div>
        </div>
        <div class="form-group">
            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" class="editable">
        </div>
        <div class="form-group">
            <label>Custom Fields:</label>
            <div id="customFields"></div>
            <button id="addCustomField" class="add-field">+ Add Custom Field</button>
        </div>
        <div id="successMessage" class="success-message" style="display: none;"></div>
    </div>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "http://127.0.0.1:8000/api/profile";

    // Select elements
    const avatar = document.getElementById("avatar");
    const avatarUpload = document.getElementById("avatarUpload");
    const customFieldsContainer = document.getElementById("customFields");
    const addCustomFieldBtn = document.getElementById("addCustomField");
    const editableFields = document.querySelectorAll(".editable");

    // Collect all field values
    const collectPayload = () => {
        const name = document.getElementById("name").textContent.trim();
        const email = document.getElementById("email").textContent.trim();
        const birthday = document.getElementById("birthday").value;
        const avatarFile = avatar.dataset.file || null;

        // Collect custom fields
        const customFields = [];
        customFieldsContainer.querySelectorAll(".custom-field").forEach(field => {
            const fieldName = field.querySelector(".custom-field-name").value.trim();
            const fieldContent = field.querySelector(".custom-field-content").value.trim();
            if (fieldName && fieldContent) {
                customFields.push({ name: fieldName, content: fieldContent });
            }
        });

        return {
            name,
            email,
            birthday,
            avatar: avatarFile,
            bio: customFields
        };
    };

    // Send data to API
    const sendPayload = async () => {
        const payload = collectPayload();
        const formData = new FormData();
        formData.append('name', payload.name);
        formData.append('email', payload.email);
        formData.append('birthday', payload.birthday);
        formData.append('bio', JSON.stringify(payload.bio));

        if (avatarUpload.files[0]) {
            formData.append('avatar', avatarUpload.files[0]);
        }

        try {
            const response = await fetch(apiUrl, {
                method: "POST",
                body: formData
            });

            const result = await response.json();
            if (response.ok) {
                document.getElementById("successMessage").textContent = "Profile updated successfully!";
                document.getElementById("successMessage").style.display = "block";
            } else {
                console.error(result);
            }
        } catch (error) {
            console.error("Error:", error);
        }
    };

    // Handle avatar upload
    avatar.addEventListener("click", () => avatarUpload.click());
    avatarUpload.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                avatar.src = reader.result;
                avatar.dataset.file = file;
                sendPayload();
            };
            reader.readAsDataURL(file);
        }
    });

    // Listen for changes in editable fields
    editableFields.forEach(field => {
        field.addEventListener("input", sendPayload);
    });

    // Listen for changes in birthday input
    document.getElementById("birthday").addEventListener("change", sendPayload);

    // Handle adding custom fields
    addCustomFieldBtn.addEventListener("click", () => {
        const fieldDiv = document.createElement("div");
        fieldDiv.classList.add("custom-field");

        fieldDiv.innerHTML = `
            <input type="text" class="custom-field-name" placeholder="Field Name">
            <input type="text" class="custom-field-content" placeholder="Field Content">
            <button class="remove-field">Remove</button>
        `;

        customFieldsContainer.appendChild(fieldDiv);

        // Add event listener for the remove button
        fieldDiv.querySelector(".remove-field").addEventListener("click", () => {
            fieldDiv.remove();
            sendPayload();
        });

        // Send updated payload whenever custom fields are changed
        fieldDiv.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", sendPayload);
        });
    });

    // Add event listeners for existing remove buttons
    customFieldsContainer.querySelectorAll(".remove-field").forEach(button => {
        button.addEventListener("click", (e) => {
            e.target.parentElement.remove();
            sendPayload();
        });
    });
});
    </script>
</body>
</html>

