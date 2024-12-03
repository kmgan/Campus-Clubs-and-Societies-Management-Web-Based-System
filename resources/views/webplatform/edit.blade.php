@extends('webplatform.shared.layout')

@section('title', 'Edit Web Content')

@section('content')
    <style>
        body {
            background-color: #f4f4f4;
        }

        h2 {
            margin-top: 1rem;
        }

        .activity-container {
            border: 1px solid #ccc;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .activity-container img {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
    </style>

    <h1 class="fw-bold">Edit Web Content</h1>
    <hr>
    <div class="container-fluid">
        <form id="editWebContentForm" class="needs-validation" enctype="multipart/form-data" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <h2>Logo</h2>
                    <div>
                        @if ($club->logo)
                            <img src="data:image/jpeg;base64,{{ base64_encode($club->logo) }}" alt="{{ $club->name }} Logo"
                                style="max-width: 135px; height: 135px;" id="clubLogoPreview">
                        @else
                            <img id="clubLogoPreview"
                                style="max-width: 135px; height: 135px; display: none; margin-bottom: 10px;" />
                        @endif
                        <input type="file" class="form-control" id="clubLogoInput" name="logo" accept="image/*">
                    </div>

                    <h2>About Us Section</h2>

                    <!-- About Us Image -->
                    <div class="mb-3">
                        <div id="aboutUsImagePreviewContainer">
                            <!-- Initially hidden, shown once a file is selected -->
                            <img id="aboutUsImagePreview" src="{{ asset($club->about_us_img_url) }}"
                                class="img-fluid about-us-img"
                                style="max-width: 400px; height: auto; margin-bottom: 10px; {{ $club->about_us_img_url ? 'display: block;' : 'display: none;' }}">
                        </div>
                        <input type="file" id="aboutUsImage" name="about_us_img_url" class="form-control"
                            accept="image/*">
                    </div>

                    <!-- About Us Description -->
                    <textarea id="aboutDescription" name="description" class="form-control">{!! $club->description !!}</textarea>

                    <h2>What We Do Section</h2>
                    <div id="activitiesContainer">
                        @foreach ($club->club_activity as $activity)
                            <div class="activity-container" data-activity-id="{{ $activity->id }}">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger mt-2 remove-activity-button"><i
                                            class="bi bi-x-lg"></i> Remove</button>
                                </div>
                                <div class="mb-3">
                                    <img src="{{ asset($activity->activity_img_url) }}" alt="{{ $activity->name }}"
                                        class="activity-img-preview">
                                </div>
                                <input type="file" name="activities[{{ $activity->id }}][image]"
                                    class="form-control activity-img-input" accept="image/*">
                                <input type="text" name="activities[{{ $activity->id }}][name]" class="form-control mt-2"
                                    placeholder="Activity Name" value="{{ $activity->name }}">
                                <textarea name="activities[{{ $activity->id }}][description]" class="form-control mt-2"
                                    placeholder="Activity Description">{{ $activity->description }}</textarea>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="addActivityButton"><i
                                class="bi bi-plus-circle"></i> Add Activity</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>Our Highlights Section</h2>
                    <div id="galleryContainer" class="row g-3">
                        @foreach ($club->club_gallery as $image)
                            <div class="col-12 col-md-6 gallery-item" data-gallery-id="{{ $image->id }}">
                                <div class="card">
                                    <!-- Display the existing gallery image if it exists -->
                                    <img src="{{ asset($image->gallery_img_url) }}"
                                        class="card-img-top w-100 gallery-img-preview" alt="Gallery Image"
                                        style="max-height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <!-- File input for selecting new gallery image -->
                                        <input type="file" name="gallery[{{ $image->id }}]"
                                            class="form-control gallery-img-input mt-2" accept="image/*">
                                        <!-- Remove button -->
                                        <button type="button" class="btn btn-danger mt-2 remove-gallery-button">
                                            <i class="bi bi-x-lg"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="addGalleryImageButton"><i
                                class="bi bi-plus-circle"></i> Add Image</button>
                    </div>


                    <h2>Join Us Section</h2>
                    <textarea id="joinUsDescription" name="join_description" class="form-control">{!! $club->join_description !!}</textarea>

                    <h2>Contact Us Section</h2>
                    <div class="mb-3">
                        <label for="clubEmail" class="form-label">Email</label>
                        <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" id="clubEmail"
                            name="email" class="form-control" value="{{ $club->email }}">
                    </div>
                    <div class="mb-3">
                        <label for="clubFacebook" class="form-label">Facebook URL</label>
                        <input type="url" id="clubFacebook" name="facebook_url" class="form-control"
                            value="{{ $club->facebook_url }}">
                    </div>
                    <div class="mb-3">
                        <label for="clubInstagram" class="form-label">Instagram URL</label>
                        <input type="url" id="clubInstagram" name="instagram_url" class="form-control"
                            value="{{ $club->instagram_url }}">
                    </div>
                    <div class="mb-3">
                        <label for="clubLinkedIn" class="form-label">LinkedIn URL</label>
                        <input type="url" id="clubLinkedIn" name="linkedin_url" class="form-control"
                            value="{{ $club->linkedin_url }}">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary me-2" id="resetContentButton">Reset</button>
                <button type="submit" class="btn btn-primary" id="saveContentButton">Save</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxActivities = 6;
            const deletedActivities = []; // Track deleted activity IDs
            const maxGalleryImages = 5;
            const deletedGalleryImages = []; // Track deleted gallery image IDs

            // Initialize TinyMCE for text areas
            tinymce.init({
                selector: '#aboutDescription, #joinUsDescription',
                license_key: 'gpl',
                menubar: true,
                plugins: 'link lists',
                toolbar: 'bold italic underline | bullist numlist | link',
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                },
            });

            document.addEventListener('focusin', (e) => {
                if (e.target.closest(
                        ".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !==
                    null) {
                    e.stopImmediatePropagation();
                }
            });

            // Activity and Gallery Containers
            const activitiesContainer = document.getElementById('activitiesContainer');
            const galleryContainer = document.getElementById('galleryContainer');

            // Add Buttons
            const addActivityButton = document.getElementById('addActivityButton');
            const addGalleryImageButton = document.getElementById('addGalleryImageButton');

            // Save and Reset Buttons
            const saveButton = document.getElementById('saveContentButton');
            const resetButton = document.getElementById('resetContentButton');

            const logoInput = document.getElementById('clubLogoInput');
            const logoPreview = document.getElementById('clubLogoPreview');
            const aboutUsImageInput = document.getElementById('aboutUsImage');
            const aboutUsImagePreview = document.getElementById('aboutUsImagePreview');

            // Function to toggle the "Add Activity" button visibility
            function toggleAddActivityButton() {
                const currentActivities = activitiesContainer.querySelectorAll('.activity-container').length;
                addActivityButton.style.display = currentActivities >= maxActivities ? 'none' : 'inline-block';
            }

            // Function to toggle the "Add Gallery Image" button visibility
            function toggleAddGalleryImageButton() {
                const currentImages = galleryContainer.querySelectorAll('.gallery-item').length;
                addGalleryImageButton.style.display = currentImages >= maxGalleryImages ? 'none' : 'inline-block';
            }

            // Initial button visibility checks
            toggleAddActivityButton();
            toggleAddGalleryImageButton();

            // Function to dynamically set `required` attributes based on field values
            function handleActivityFieldValidation(container) {
                const inputs = container.querySelectorAll('input, textarea'); // Select all inputs and textareas

                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        const isAnyFieldFilled = Array.from(inputs).some(field => field.value
                            .trim() !== '' || (field.type === 'file' && field.files.length > 0));
                        inputs.forEach(field => {
                            field.required =
                                isAnyFieldFilled; // Set required if any field is filled
                        });
                    });
                });
            }

            // Apply validation to existing activities
            document.querySelectorAll('.activity-container').forEach(container => {
                handleActivityFieldValidation(container);
            });

            // Apply validation for new activities
            addActivityButton.addEventListener('click', function() {
                const activityId = `new-${Date.now()}`;
                const activityHTML = `
    <div class="activity-container" data-activity-id="${activityId}">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-danger mt-2 remove-activity-button"><i class="bi bi-x-lg"></i> Remove</button>
        </div>
        <div class="mb-3">
            <img src="" alt="Activity Image" class="activity-img-preview" style="display: none;">
        </div>
        <input type="file" name="activities[${activityId}][image]" class="form-control activity-img-input" accept="image/*">
        <input type="text" name="activities[${activityId}][name]" class="form-control mt-2" placeholder="Activity Name">
        <textarea name="activities[${activityId}][description]" class="form-control mt-2" placeholder="Activity Description"></textarea>
    </div>
    `;
                activitiesContainer.insertAdjacentHTML('beforeend', activityHTML);

                // Apply validation logic to the new activity container
                const newContainer = activitiesContainer.lastElementChild;
                handleActivityFieldValidation(newContainer);
                toggleAddActivityButton();
            });

            // Remove individual activity logic
            activitiesContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-activity-button')) {
                    const activityContainer = event.target.closest('.activity-container');
                    const activityId = activityContainer.dataset.activityId;

                    if (!activityId.startsWith('new-')) {
                        deletedActivities.push(activityId);
                    }

                    activityContainer.remove();
                    toggleAddActivityButton();
                }
            });

            // Remove gallery image logic
            galleryContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-gallery-button')) {
                    const galleryItem = event.target.closest('.gallery-item');
                    const galleryId = galleryItem.dataset.galleryId;

                    // Add the gallery ID to the deletedGalleryImages array
                    if (galleryId) {
                        deletedGalleryImages.push(galleryId);
                    }

                    // Remove the gallery item from the DOM
                    galleryItem.remove();
                    toggleAddGalleryImageButton();
                }
            });

            // Activity image file input change handler
            activitiesContainer.addEventListener('change', function(event) {
                if (event.target.classList.contains('activity-img-input')) {
                    const fileInput = event.target;
                    const previewImg = fileInput.closest('.activity-container').querySelector(
                        '.activity-img-preview');
                    const file = fileInput.files[0];
                    const maxSizeInMB = 10; // 10 MB
                    const maxSizeInBytes = maxSizeInMB * 1024 * 1024; // Convert MB to bytes
                    const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];

                    if (file) {
                        // Check file format
                        if (!allowedFormats.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file format',
                                text: 'Please upload a valid image file (JPEG, PNG, JPG).',
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImg.style.display = 'none'; // Hide the preview if invalid format
                        } else if (file.size > maxSizeInBytes) {
                            // Check if file size is too large
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large',
                                text: `The selected file exceeds the maximum size limit of ${maxSizeInMB}MB.`,
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImg.style.display = 'none'; // Hide the preview if file is too large
                        } else {
                            // If file is valid, preview it
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                                previewImg.style.display = 'block'; // Show the preview image
                            };
                            reader.readAsDataURL(file);
                        }
                    } else {
                        previewImg.style.display = 'none'; // Hide preview if no file selected
                    }
                }
            });

            // Function to toggle the "Add Gallery Image" button visibility
            function toggleAddGalleryImageButton() {
                const currentImages = galleryContainer.querySelectorAll('.gallery-item').length;
                addGalleryImageButton.style.display = currentImages >= maxGalleryImages ? 'none' : 'inline-block';
            }

            // Gallery image file input change handler
            galleryContainer.addEventListener('change', function(event) {
                if (event.target.classList.contains('gallery-img-input')) {
                    const fileInput = event.target;

                    // Select the correct preview image for this specific gallery item
                    const previewImg = fileInput.closest('.gallery-item').querySelector(
                        '.gallery-img-preview');
                    const file = fileInput.files[0];

                    const maxSizeInMB = 10; // 10 MB
                    const maxSizeInBytes = maxSizeInMB * 1024 * 1024; // Convert MB to bytes
                    const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];

                    if (file) {
                        // Check file format
                        if (!allowedFormats.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file format',
                                text: 'Please upload a valid image file (JPEG, PNG, JPG).',
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImg.style.display = 'none'; // Hide the preview if invalid format
                        } else if (file.size > maxSizeInBytes) {
                            // Check if file size is too large
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large',
                                text: `The selected file exceeds the maximum size limit of ${maxSizeInMB}MB.`,
                            });
                            fileInput.value = ''; // Clear the file input
                            previewImg.style.display = 'none'; // Hide the preview if file is too large
                        } else {
                            // If file is valid, preview it
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result; // Update the preview image source
                                previewImg.style.display = 'block'; // Make sure the preview is visible
                            };
                            reader.readAsDataURL(file);
                        }
                    } else {
                        previewImg.style.display = 'none'; // Hide preview if no file selected
                    }
                }
            });

            // Add new gallery image logic
            addGalleryImageButton.addEventListener('click', function() {
                const galleryHTML = `
        <div class="col-12 col-md-6 gallery-item">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Initially empty preview image that will display once a file is selected -->
                    <img src="" alt="Gallery Image" class="gallery-img-preview" style="display:none; max-height: 200px; object-fit: cover;">
                    <!-- File input for the gallery image -->
                    <input type="file" name="gallery[]" class="form-control gallery-img-input mt-2" accept="image/*">
                    <!-- Remove button for this gallery item -->
                    <button type="button" class="btn btn-danger mt-2 remove-gallery-button">
                        <i class="bi bi-x-lg"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
                galleryContainer.insertAdjacentHTML('beforeend', galleryHTML);
                toggleAddGalleryImageButton();
            });


            // If there is an existing logo, display it as a preview
            if (logoPreview.src) {
                logoPreview.style.display = 'block';
            }

            // Logo file input change handler
            logoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const maxSizeInMB = 2; // 2 MB
                const maxSizeInBytes = maxSizeInMB * 1024 * 1024; // Convert MB to bytes
                const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];

                if (file) {
                    // Check file format
                    if (!allowedFormats.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid file format',
                            text: 'Please upload a valid image file (JPEG, PNG, JPG).',
                        });
                        this.value = ''; // Clear the file input
                    } else if (file.size > maxSizeInBytes) {
                        // Check if file size is too large
                        Swal.fire({
                            icon: 'error',
                            title: 'File too large',
                            text: `The selected file exceeds the maximum size limit of ${maxSizeInMB}MB.`,
                        });
                        this.value = ''; // Clear the file input
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            logoPreview.src = e.target.result;
                            logoPreview.style.display = 'block'; // Show the preview image
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            // About Us image file input change handler
            aboutUsImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const maxSizeInMB = 10; // 10 MB
                const maxSizeInBytes = maxSizeInMB * 1024 * 1024; // Convert MB to bytes
                const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];

                if (file) {
                    // Check file format
                    if (!allowedFormats.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid file format',
                            text: 'Please upload a valid image file (JPEG, PNG, JPG).',
                        });
                        this.value = ''; // Clear the file input
                    } else if (file.size > maxSizeInBytes) {
                        // Check if file size is too large
                        Swal.fire({
                            icon: 'error',
                            title: 'File too large',
                            text: `The selected file exceeds the maximum size limit of ${maxSizeInMB}MB.`,
                        });
                        this.value = ''; // Clear the file input
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            aboutUsImagePreview.src = e.target.result;
                            aboutUsImagePreview.style.display = 'block'; // Show the preview image
                        };
                        reader.readAsDataURL(file);
                    }
                } else {
                    // If no file selected, hide the preview
                    aboutUsImagePreview.style.display = 'none';
                }
            });

            const form = document.getElementById('editWebContentForm');
            const emailField = document.getElementById('clubEmail');

            // Email validation function using regex
            function isValidEmail(email) {
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return emailPattern.test(email);
            }

            // Save button logic
            saveButton.addEventListener('click', function() {
                const form = document.getElementById('editWebContentForm');

                // Manually trigger form validation
                if (!form.checkValidity()) {
                    form.classList.add('was-validated'); // Apply validation feedback

                    // Find the first invalid field
                    const firstInvalidField = form.querySelector(':invalid');
                    if (firstInvalidField) {
                        // Focus on the first invalid field
                        firstInvalidField.focus();

                        // Smoothly scroll to the first invalid field
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return; // Prevent further execution
                }

                const activities = activitiesContainer.querySelectorAll('.activity-container');
                const galleryImages = galleryContainer.querySelectorAll('.gallery-image-container');
                const email = emailField.value.trim();

                // Remove empty activities
                activities.forEach((activity) => {
                    const imageInput = activity.querySelector('.activity-img-input');
                    const nameInput = activity.querySelector('input[name*="[name]"]');
                    const descriptionInput = activity.querySelector(
                        'textarea[name*="[description]"]');

                    // Check if all fields are empty for this activity
                    const isEmpty =
                        (!imageInput.files.length || imageInput.files[0]?.size === 0) &&
                        !nameInput.value.trim() &&
                        !descriptionInput.value.trim();

                    if (isEmpty) {
                        activity.remove(); // Remove empty activity from DOM
                    }
                });

                // Remove empty gallery images
                galleryImages.forEach((image) => {
                    const imageInput = image.querySelector('.gallery-img-input');

                    if (!imageInput.files.length || imageInput.files[0]?.size === 0) {
                        image.remove(); // Remove empty image container from DOM
                    }
                });

                // Re-create FormData after removing empty activities and gallery images
                const formData = new FormData(form);

                // Append deleted activities to FormData
                if (deletedActivities.length > 0) {
                    deletedActivities.forEach((id) => {
                        formData.append('deleted_activities[]', id);
                    });
                }

                // Append deleted gallery images to FormData
                if (deletedGalleryImages.length > 0) {
                    deletedGalleryImages.forEach((id) => {
                        formData.append('deleted_gallery_images[]', id);
                    });
                }

                // Gallery Images - Appending files to FormData
                galleryImages.forEach((image) => {
                    const fileInput = image.querySelector('.gallery-img-input');
                    if (fileInput.files.length && fileInput.files[0].size > 0) {
                        const galleryId = image.getAttribute('data-gallery-id');
                        formData.append(`gallery[${galleryId}][image]`, fileInput.files[
                            0]); // Append the image file to FormData
                    }
                });

                // Send the form data to the server
                fetch("{{ route('iclub.webContent.save', ['clubId' => $club->id]) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? 'Saved!' : 'Error!',
                            text: data.message || 'Content updated successfully.',
                        }).then(() => {
                            if (data.success) {
                                deletedActivities.length = 0;
                                deletedGalleryImages.length = 0;
                                location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Unexpected error occurred.',
                        });
                    });
            });

            // Reset button logic
            resetButton.addEventListener('click', function() {
                location.reload(); // Reload the page to reset all changes
            });
        });
    </script>
@endsection
