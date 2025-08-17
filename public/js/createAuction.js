function sanitizeInput(input) {

    input = String(input);

    const map = new Map([
        ['&', '&amp;'],
        ['<', '&lt;'],
        ['>', '&gt;'],
        ['"', '&quot;'],
        ["'", '&#x27;'],
        ['/', '&#x2F;']
    ]);

    return input.replace(/[&<>"'/]/g, match => map.get(match));
}

function showNotification(message) {
    const notification = document.createElement("div");
    notification.textContent = message;

    notification.style.position = "absolute";
    notification.style.top = "10px";
    notification.style.left = "50%";
    notification.style.transform = "translateX(-50%)";
    notification.style.backgroundColor = "#f8d7da";
    notification.style.color = "#721c24";
    notification.style.padding = "10px 20px";
    notification.style.border = "1px solid #f5c6cb";
    notification.style.borderRadius = "5px";
    notification.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
    notification.style.zIndex = "1000";
    notification.style.opacity = "1"; 
    notification.style.transition = "opacity 0.5s ease-in-out"; 
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = "0";  
    }, 2000);

    notification.addEventListener("transitionend", () => {
        notification.remove();
    });
}

document.addEventListener("DOMContentLoaded", function() {
    
    function handleInputLimit(inputElement, counterElement, maxLength) {
        inputElement.addEventListener("input", function () {

            inputElement.value = sanitizeInput(inputElement.value);

            const currentLength = inputElement.value.length;

            if (currentLength > maxLength) {
                const allowedLength = maxLength - (currentLength - inputElement.value.length);
                inputElement.value = inputElement.value.substring(0, allowedLength);
            }

            counterElement.textContent = `${inputElement.value.length}/${maxLength}`;
        });
    }

    const titleInput = document.getElementById("title");
    const titleCounter = document.createElement("div");
    titleCounter.classList.add("char-counter");
    titleCounter.textContent = "0/50";
    titleInput.parentElement.appendChild(titleCounter);

    handleInputLimit(titleInput, titleCounter, 50);

    const descriptionInput = document.getElementById("description");
    const descriptionCounter = document.createElement("div");
    descriptionCounter.classList.add("char-counter");
    descriptionCounter.textContent = "0/500";
    descriptionInput.parentElement.appendChild(descriptionCounter);

    handleInputLimit(descriptionInput, descriptionCounter, 500);

    const startingPriceInput = document.getElementById("initValue");

    startingPriceInput.addEventListener("input", function () {
        startingPriceInput.value = sanitizeInitValue(startingPriceInput.value);
    });

    function sanitizeInitValue(input) {
        let sanitized = input.replace(/[^0-9.]/g, "");

        const firstDotIndex = sanitized.indexOf(".");
        if (firstDotIndex !== -1) {
            sanitized = sanitized.slice(0, firstDotIndex + 1) + sanitized.slice(firstDotIndex + 1).replace(/\./g, "");
        }

        // Remove leading zeros, unless the number starts with "0."
        sanitized = sanitized.replace(/^0+(?!\.)/, "");

        return sanitized;
    }

    const photosInput = document.getElementById("photos");
    const photoPreview = document.getElementById("photo-preview");
    const selectedFiles = []; // Keep track of all selected files

    photosInput.addEventListener("change", function () {
        const files = Array.from(photosInput.files);

        // Check if adding these files will exceed the limit
        if (selectedFiles.length + files.length > 8) {
            showNotification("You can only upload up to 8 photos.");
            return;
        }

        // Add new files to the selected files list
        files.forEach(file => {
            if (!selectedFiles.includes(file) && file.type.startsWith("image/")) {
                selectedFiles.push(file);

                // Display the preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;

                    // Add a remove button to delete specific images
                    const wrapper = document.createElement("div");
                    wrapper.style.position = "relative";
                    wrapper.style.display = "inline-block";
                    wrapper.appendChild(img);

                    const removeButton = document.createElement("button");
                    removeButton.textContent = "X";
                    removeButton.style.position = "absolute";
                    removeButton.style.top = "5px";
                    removeButton.style.right = "5px";
                    removeButton.style.background = "red";
                    removeButton.style.color = "white";
                    removeButton.style.border = "none";
                    removeButton.style.borderRadius = "50%";
                    removeButton.style.cursor = "pointer";

                    removeButton.addEventListener("click", function () {
                        // Remove the file from the selectedFiles array
                        const index = selectedFiles.indexOf(file);
                        if (index > -1) selectedFiles.splice(index, 1);

                        // Remove the image from the preview
                        wrapper.remove();
                    });

                    wrapper.appendChild(removeButton);
                    photoPreview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            } else if (!file.type.startsWith("image/")) {
                showNotification(`${file.name} is not a valid image file.`);
            }
        });

        // Clear the file input so the same files can be reselected if needed
        photosInput.value = "";
    });

    const start_datetime = document.getElementById("start_datetime");
    const end_datetime = document.getElementById("end_datetime");

    const submitButton = document.getElementById("submit-button");
    const form = document.getElementById("create-auction-form");

    submitButton.addEventListener("click", function (event) {
        event.preventDefault();

        const title = sanitizeInput(titleInput.value).trim();
        const description =  sanitizeInput(descriptionInput.value).trim();
        const startingPrice = sanitizeInput(startingPriceInput.value).trim();
        const startDateTime = start_datetime.value.trim();
        const endDateTime = end_datetime.value.trim();
        const subCategory = document.getElementById("subCategorySelect").value.trim();
        const state = document.getElementById("state").value.trim();

        if (!title || !description || !startingPrice || !startDateTime || !endDateTime || !subCategory || !state) {
            showNotification("Please fill out all fields.");
            return;
        }

        if(startDateTime >= endDateTime){
            showNotification("End date must be after start date.");
            return;
        }

        if(endDateTime <= new Date().toISOString().slice(0, 16) || startDateTime <= new Date().toISOString().slice(0, 16)){
            showNotification("Date must be in the future.");
            return;
        }

        if(selectedFiles.length === 0){
            showNotification("Please upload at least one photo.");
            return;
        }

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));

        photosInput.files = dataTransfer.files;

        form.submit();
        });

});
