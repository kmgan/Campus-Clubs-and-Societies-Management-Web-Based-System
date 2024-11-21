@extends('webplatform.shared.layout')

@section('title', 'Users')

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
                        <th class="text-white">Email</th>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="add_name" required>
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="add_email" required>
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
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" required>
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
                        <button type="submit" class="btn btn-primary">Save changes</button>
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
                        "data": "club_name",
                        "render": function(data) {
                            return data ? data : '';
                        }
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
                },
            });

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

        function populateRolesAndClubs() {
            // Populate roles dynamically
            $.ajax({
                url: '/iclub/role/data',
                type: 'GET',
                success: function(rolesData) {
                    const addRoleSelect = $('#add_role');
                    const editRoleSelect = $('#edit_role');
                    addRoleSelect.empty();
                    editRoleSelect.empty();
                    addRoleSelect.append('<option value="">Select a role</option>');
                    editRoleSelect.append('<option value="">Select a role</option>');
                    rolesData.roles.forEach(role => {
                        addRoleSelect.append(
                            `<option value="${role.name}">${role.name}</option>`
                        );
                        editRoleSelect.append(
                            `<option value="${role.name}">${role.name}</option>`
                        );
                    });
                },
                error: function(err) {
                    console.error('Error fetching roles:', err);
                    Swal.fire('Error!', 'An error occurred while fetching roles.', 'error');
                }
            });

            // Populate clubs dynamically
            $.ajax({
                url: '/iclub/club/data',
                type: 'GET',
                success: function(clubsData) {
                    const addClubSelect = $('#add_club_id');
                    const editClubSelect = $('#edit_club_id');
                    addClubSelect.empty();
                    editClubSelect.empty();
                    addClubSelect.append('<option value="">No Club</option>');
                    editClubSelect.append('<option value="">No Club</option>');
                    clubsData.clubs.forEach(club => {
                        addClubSelect.append(
                            `<option value="${club.id}">${club.name}</option>`
                        );
                        editClubSelect.append(
                            `<option value="${club.id}">${club.name}</option>`
                        );
                    });
                },
                error: function(err) {
                    console.error('Error fetching clubs:', err);
                    Swal.fire('Error!', 'An error occurred while fetching clubs.', 'error');
                }
            });
        }

        function handleRoleChange(roleSelect) {
            const clubSelectContainer = roleSelect.closest('.modal-body').querySelector('.club-select');
            if (roleSelect.value === 'Club Manager') {
                $(clubSelectContainer).show();
            } else {
                $(clubSelectContainer).hide();
            }
        }

        $('#add_role').on('change', function() {
            handleRoleChange(this);
        });

        $('#edit_role').on('change', function() {
            handleRoleChange(this);
        });

        function editUser(userId) {
            $.ajax({
                url: `/iclub/user/${userId}`,
                type: 'GET',
                success: function(data) {
                    $('#userId').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);

                    $('#editUserModal').modal('show');
                    populateRolesAndClubs();
                },
                error: function(err) {
                    console.error('Error fetching user data:', err);
                    Swal.fire(
                        'Error!',
                        'An error occurred while fetching the user data.',
                        'error'
                    );
                }
            });
        };


        $('#editUserForm').on('submit', function(event) {
            event.preventDefault();

            if (this.checkValidity()) {
                saveUser();
            }

            this.classList.add('was-validated');
        });

        function saveUser() {
            const userId = $('#userId').val();

            $.ajax({
                url: `/iclub/user/${userId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#edit_name').val(),
                    email: $('#edit_email').val(),
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
                addUser();
            }

            this.classList.add('was-validated');
        });

        function addUser() {
            $.ajax({
                url: "{{ route('iclub.users.add') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#add_name').val(),
                    email: $('#add_email').val(),
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

        function deleteUser(userId) {
            // Confirm with the user before deleting
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
                        success: function(result) {
                            // Reload the table after deletion
                            $('#usersTable').DataTable().ajax.reload();

                            Swal.fire({
                                title: 'Deleted!',
                                text: result.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(err) {
                            // Show error message
                            Swal.fire(
                                'Error!',
                                err.responseJSON.message ||
                                'An error occurred while deleting the user.',
                                'error'
                            );
                            console.error(err);
                        }
                    });
                }
            });
        };


        $('#addUserModal, #editUserModal').on('show.bs.modal', function() {
            populateRolesAndClubs();
        });

        $('#addUserModal, #editUserModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $(this).find('form').removeClass('was-validated');
        });
    </script>

@endsection
