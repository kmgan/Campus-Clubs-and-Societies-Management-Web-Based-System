@extends('webplatform.shared.layout')

@section('title', 'User')

@section('content')
    <style>
        body {
            background-color: #f4f4f4;
        }

        table.dataTable.display th.dt-type-numeric {
            text-align: left !important;
        }

        table.dataTable.display td.dt-type-numeric {
            text-align: left !important;
        }

        .table-striped>tbody>tr:nth-child(odd)>td,
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: white;
            box-shadow: none !important;
        }

        .table-striped>tbody>tr:nth-child(even)>td,
        .table-striped>tbody>tr:nth-child(even)>th {
            background-color: #ECF7FF;
        }

        table.dataTable.display th {
            background-color: #006DEB;
            font-size: 1.25rem !important;
            font-weight: 600;
        }

        .text-right {
            text-align: right !important;
            padding-right: 1rem;
        }
    </style>

    <h1 class="fw-bold">Users</h1>

    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="table-responsive">
                <table id="usersTable" class="display compact table table-striped" style="width:100%">
                    <thead>
                        <th class="text-white">Name</th>
                        <th class="text-white">Sunway Imail</th>
                        <th class="text-white">Role</th>
                        <th class="text-white">Club Name</th>
                        <th class="text-white"></th>
                    </thead>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User <i class="bi bi-plus-circle text-white"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="add_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="add_username" required>
                            <div class="invalid-feedback">Please enter a valid username.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="add_name" required>
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_sunway_imail" class="form-label">Sunway Imail</label>
                            <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                class="form-control" id="add_sunway_imail" required>
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_role" class="form-label">Role</label>
                            <select id="add_role" class="form-select" required>
                                <option value="">Select a role</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        <div class="mb-3 club-select" style="display: none;">
                            <label for="add_club_id" class="form-label">Club (Optional)</label>
                            <select id="add_club_id" class="form-select">
                                <option value="">No Club</option>
                            </select>
                        </div>
                        <div class="mb-3 student-fields" style="display: none;">
                            <label for="add_student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="add_student_id">
                            <div class="invalid-feedback">Please enter a valid student ID.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="add_personal_email" class="form-label">Personal Email</label>
                            <input type="email" class="form-control" id="add_personal_email">
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="add_phone" class="form-label">Phone No.</label>
                            <input type="text" class="form-control" id="add_phone">
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="add_course_of_study" class="form-label">Course of Study</label>
                            <input type="text" class="form-control" id="add_course_of_study">
                            <div class="invalid-feedback">Please enter a valid course of study.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" class="needs-validation" novalidate>
                        <input type="hidden" id="userId">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" required>
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sunway_imail" class="form-label">Sunway Imail</label>
                            <input type="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                class="form-control" id="edit_sunway_imail" required>
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select id="edit_role" class="form-select" required>
                                <option value="">Select a role</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        <div class="mb-3 club-select" style="display: none;">
                            <label for="edit_club_id" class="form-label">Club (Optional)</label>
                            <select id="edit_club_id" class="form-select">
                                <option value="">No Club</option>
                            </select>
                        </div>
                        <div class="mb-3 student-fields" style="display: none;">
                            <label for="edit_student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="edit_student_id">
                            <div class="invalid-feedback">Please enter a valid student ID.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="edit_personal_email" class="form-label">Personal Email</label>
                            <input type="email" class="form-control" id="edit_personal_email">
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="edit_phone" class="form-label">Phone No.</label>
                            <input type="text" class="form-control" id="edit_phone">
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                        </div>
                        <div class="mb-3 personal-fields" style="display: none;">
                            <label for="edit_course_of_study" class="form-label">Course of Study</label>
                            <input type="text" class="form-control" id="edit_course_of_study">
                            <div class="invalid-feedback">Please enter a valid course of study.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table = $('#usersTable').DataTable({
                "processing": true,
                "responsive": true,
                "ajax": "{{ route('iclub.users.data') }}",
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "role",
                        "render": function(data) {
                            switch (data) {
                                case 'admin':
                                    return 'Admin';
                                case 'club_manager':
                                    return 'Club Manager';
                                case 'user':
                                    return 'User';
                                default:
                                    return 'No Role Assigned';
                            }
                        }
                    },
                    {
                        "data": "club_name"
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "searchable": false,
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return `
                                <i class="bi bi-pencil-square" style="color: #AB6906; cursor: pointer" onclick="editUser(${row.id})"></i>
                                <i class="bi bi-trash-fill text-danger ms-2" style="cursor: pointer" onclick="deleteUser(${row.id})"></i>
                            `;
                        }
                    }
                ],
                "order": [
                    [1, "asc"]
                ],
                search: {
                    return: true
                }
            });

            $('#add_role').on('change', function() {
                handleRoleChange(this.value); // Call the function when the role changes
            });

            $('#edit_role').on('change', function() {
                handleRoleChange(this.value); // Call the function when the role changes
            });

        });

        function handleRoleChange(role) {
            if (role === 'club_manager') {
                // Show the club selection field only for club managers
                $('.club-select').show();
                $('.student-fields').hide();
                $('.personal-fields').hide();
            } else if (role === 'user') {
                // Show student and personal fields only for users
                $('.student-fields').show();
                $('.personal-fields').show();
                $('.club-select').hide();
            } else if (role === 'admin') {
                // Hide all unnecessary fields for admin
                $('.club-select, .student-fields, .personal-fields').hide();
            } else {
                // Default behavior: hide all fields
                $('.club-select, .student-fields, .personal-fields').hide();
            }
        }

        function addUser() {
            $.ajax({
                url: "{{ route('iclub.users.add') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    username: $('#add_username').val(),
                    name: $('#add_name').val(),
                    email: $('#add_sunway_imail').val(),
                    student_id: $('#add_student_id').val(),
                    personal_email: $('#add_personal_email').val(),
                    phone: $('#add_phone').val(),
                    course_of_study: $('#add_course_of_study').val(),
                    role: $('#add_role').val(),
                    club_id: $('#add_club_id').val()
                },
                success: function(response) {
                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    $('#usersTable').DataTable().ajax.reload();

                    Swal.fire({
                        title: 'Added!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    console.error('Error adding user:', err);
                    Swal.fire('Error!', 'An error occurred while adding the user.', 'error');
                }
            });
        }

        function editUser(userId) {
            $.ajax({
                url: `/iclub/user/${userId}`,
                type: 'GET',
                success: function(userData) {
                    // Modify populateRolesAndClubs to return a Promise
                    populateRolesAndClubs()
                        .then(() => {
                            // Debugging: Log available options
                            console.log('Available role options:',
                                $('#edit_role option').map(function() {
                                    return $(this).val();
                                }).get()
                            );

                            // Set form values using userData instead of user
                            $('#userId').val(userData.id);
                            $('#edit_name').val(userData.name);
                            $('#edit_sunway_imail').val(userData.email);
                            $('#edit_student_id').val(userData.student_id);
                            $('#edit_personal_email').val(userData.personal_email);
                            $('#edit_phone').val(userData.phone);
                            $('#edit_course_of_study').val(userData.course_of_study);

                            // Attempt multiple ways to set the role
                            $('#edit_role').val(userData.role);
                            $('#edit_role').find(`option[value="${userData.role}"]`).prop('selected', true);

                            // Force change event
                            $('#edit_role').trigger('change');

                            // Club selection
                            if (userData.club_id) {
                                $('#edit_club_id').val(userData.club_id);
                            }

                            // Visibility logic
                            if (userData.role === 'club_manager') {
                                $('.club-select').show();
                                $('.student-fields').hide();
                                $('.personal-fields').hide();
                            } else if (userData.role === 'user') {
                                $('.student-fields').show();
                                $('.personal-fields').show();
                                $('.club-select').hide();
                            } else {
                                $('.club-select, .student-fields, .personal-fields').hide();
                            }

                            // Show modal
                            $('#editUserModal').modal('show');
                        })
                        .catch(error => {
                            console.error('Error in populating roles and clubs:', error);
                        });
                },
                error: function(err) {
                    console.error('Error fetching user data:', err);
                    Swal.fire('Error!', 'An error occurred while fetching the user data.', 'error');
                }
            });
        }

        $('#editUserForm').on('submit', function(event) {
            event.preventDefault();

            if (this.checkValidity()) {
                addUser(this);
            } else {
                this.classList.add('was-validated'); // Apply validation feedback

                // Find the first invalid field
                const firstInvalidField = this.querySelector(':invalid');
                if (firstInvalidField) {
                    // Focus on the first invalid field
                    firstInvalidField.focus();

                    // Smoothly scroll to the first invalid field
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

        });

        function saveUser() {
            const userId = $('#userId').val();

            $.ajax({
                url: `/iclub/user/${userId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#edit_name').val(),
                    email: $('#edit_sunway_imail').val(),
                    student_id: $('#edit_student_id').val(),
                    personal_email: $('#edit_personal_email').val(),
                    phone: $('#edit_phone').val(),
                    course_of_study: $('#edit_course_of_study').val(),
                    role: $('#edit_role').val(),
                    club_id: $('#edit_club_id').val()
                },
                success: function(response) {
                    $('#editUserModal').modal('hide');
                    $('#usersTable').DataTable().ajax.reload();

                    Swal.fire({
                        title: 'Updated!',
                        text: 'The user details have been updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    console.error('Error updating user data:', err);
                    Swal.fire('Error!', 'An error occurred while updating the user details.',
                        'error');
                }
            });
        }

        $('#addUserForm').on('submit', function(event) {
            event.preventDefault();

            if (this.checkValidity()) {
                addUser(this);
            } else {
                this.classList.add('was-validated'); // Apply validation feedback

                // Find the first invalid field
                const firstInvalidField = this.querySelector(':invalid');
                if (firstInvalidField) {
                    // Focus on the first invalid field
                    firstInvalidField.focus();

                    // Smoothly scroll to the first invalid field
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

        });

        function deleteUser(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/iclub/user/${userId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#usersTable').DataTable().ajax.reload();

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'User deleted successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(err) {
                            console.error('Error deleting user:', err);
                            Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                        }
                    });
                }
            });
        }

        function populateRolesAndClubs() {
            return new Promise((resolve, reject) => {
                const roleDisplayNames = {
                    "admin": "Admin",
                    "club_manager": "Club Manager",
                    "user": "User"
                };

                // Fetch roles and clubs in parallel
                const fetchRoles = $.ajax({
                    url: '/iclub/role/data',
                    type: 'GET'
                });

                const fetchClubs = $.ajax({
                    url: '/iclub/club/data',
                    type: 'GET'
                });

                $.when(fetchRoles, fetchClubs)
                    .then((rolesResponse, clubsResponse) => {
                        const rolesData = rolesResponse[0];
                        const clubsData = clubsResponse[0];

                        console.log('Roles data:', rolesData);
                        console.log('Clubs data:', clubsData);

                        // Populate roles
                        const addRoleSelect = $('#add_role');
                        const editRoleSelect = $('#edit_role');
                        addRoleSelect.empty();
                        editRoleSelect.empty();

                        // Default option
                        addRoleSelect.append('<option value="">Select a role</option>');
                        editRoleSelect.append('<option value="">Select a role</option>');

                        // Populate role options
                        rolesData.roles.forEach(role => {
                            const displayName = roleDisplayNames[role.name] || role.name;
                            const optionHtml = `<option value="${role.name}">${displayName}</option>`;
                            addRoleSelect.append(optionHtml);
                            editRoleSelect.append(optionHtml);
                        });

                        // Populate clubs
                        const addClubSelect = $('#add_club_id');
                        const editClubSelect = $('#edit_club_id');
                        addClubSelect.empty();
                        editClubSelect.empty();

                        // Default option
                        addClubSelect.append('<option value="">No Club</option>');
                        editClubSelect.append('<option value="">No Club</option>');

                        // Populate club options
                        clubsData.clubs.forEach(club => {
                            const optionHtml = `<option value="${club.id}">${club.name}</option>`;
                            addClubSelect.append(optionHtml);
                            editClubSelect.append(optionHtml);
                        });

                        resolve();
                    })
                    .fail((err) => {
                        console.error('Error fetching roles or clubs:', err);
                        Swal.fire('Error!', 'An error occurred while fetching roles or clubs.', 'error');
                        reject(err);
                    });
            });
        }

        $('#addUserModal').on('show.bs.modal', function() {
            populateRolesAndClubs();
        });

        $('#addUserModal, #editUserModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('form').removeClass('was-validated');
        });

        (() => {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection
