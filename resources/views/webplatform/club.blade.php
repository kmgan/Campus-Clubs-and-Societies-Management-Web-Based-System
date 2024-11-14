@extends('webplatform.shared.layout')

@section('title', 'Clubs')

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

    <h1 class="fw-bold">Members</h1>

    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="table-responsive">
                <table id="memberTable" class="display compact table table-striped" style="width:100%">
                    <thead>
                        <th class="text-white"></th>
                        <th class="text-white">Name</th>
                        <th class="text-white">Student ID</th>
                        <th class="text-white">Sunway Imail</th>
                        <th class="text-white">Phone No.</th>
                        <th class="text-white"></th>
                    </thead>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    Add Member <i class="bi bi-plus-circle text-white"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMemberModalLabel">Edit Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editMemberForm" class="needs-validation" novalidate>
                        <input type="hidden" id="memberId">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" required>
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="edit_student_id" required>
                            <div class="invalid-feedback">Please enter a valid student ID.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sunway_imail" class="form-label">Sunway Imail</label>
                            <input type="email" class="form-control" id="edit_sunway_imail" required>
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone No.</label>
                            <input type="text" class="form-control" id="edit_phone" required>
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_personal_email" class="form-label">Personal Email</label>
                            <input type="email" class="form-control" id="edit_personal_email">
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_course_of_study" class="form-label">Course of Study</label>
                            <input type="text" class="form-control" id="edit_course_of_study" required>
                            <div class="invalid-feedback">Please enter a valid course.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Add Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addMemberForm" class="needs-validation" novalidate>
                        <!-- Member input fields as in the Edit modal -->
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="add_name" required>
                            <div class="invalid-feedback">Please enter a valid name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="add_student_id" required>
                            <div class="invalid-feedback">Please enter a valid student ID.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_sunway_imail" class="form-label">Sunway Imail</label>
                            <input type="email" class="form-control" id="add_sunway_imail" required>
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_phone" class="form-label">Phone No.</label>
                            <input type="text" class="form-control" id="add_phone" required>
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_personal_email" class="form-label">Personal Email</label>
                            <input type="email" class="form-control" id="add_personal_email">
                            <div class="invalid-feedback">Please enter a valid email format.</div>
                        </div>
                        <div class="mb-3">
                            <label for="add_course_of_study" class="form-label">Course of Study</label>
                            <input type="text" class="form-control" id="add_course_of_study" required>
                            <div class="invalid-feedback">Please enter a valid course.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            let table = $('#memberTable').DataTable({
                "processing": true,
                "responsive": true,
                "ajax": "{{ route('clubMembers.data') }}",
                "columns": [{
                        "className": 'dt-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "student_id"
                    },
                    {
                        "data": "sunway_imail"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "searchable": false,
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return `
                        <i class="bi bi-pencil-square" style="color: #AB6906; cursor: pointer" onclick="editMember(${row.id})"></i>
                        <i class="bi bi-trash-fill text-danger ms-2" style="cursor: pointer" onclick="deleteMember(${row.id})"></i>
                    `;
                        }
                    },
                    {
                        "data": "created_at",
                        "visible": false,
                        "render": function(data, type, row) {
                            if (data) {
                                return moment(data).format(
                                    'DD/MM/YYYY');
                            }
                            return '';
                        }
                    }
                ],
                "order": [
                    [6, "desc"]
                ],
                search: {
                    return: true
                },
            });



            // Add event listener for opening and closing details
            $('#memberTable tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Row is open, close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Row is closed, open it and display additional data
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });

        // Format function for row details
        function format(d) {
            // Use Moment.js to format the created_at field
            const formattedDate = d.created_at ? moment(d.created_at).format('DD/MM/YYYY') : '';

            // Return HTML with formatted date
            return `<table class="table">
            <tr>
                <td>Personal Email:</td>
                <td>${d.personal_email}</td>
            </tr>
            <tr>
                <td>Course of Study:</td>
                <td>${d.course_of_study}</td>
            </tr>
            <tr>
                <td>Date Joined:</td>
                <td>${formattedDate}</td>
            </tr>
        </table>`;
        }

        function deleteMember(memberId) {
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
                        url: `/club-members/${memberId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            // Reload the table after deletion
                            $('#memberTable').DataTable().ajax.reload();

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
                                'An error occurred while deleting the member.',
                                'error'
                            );
                            console.error(err);
                        }
                    });
                }
            });
        }

        (() => {
            'use strict';
            // Enforces strict mode, which helps catch common coding mistakes and prevents unsafe actions like defining global variables.

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation');
            // Selects all form elements with the `needs-validation` class and stores them in the `forms` NodeList.

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event
                            .preventDefault(); // Prevents the form from being submitted if validation fails.
                        event
                            .stopPropagation(); // Stops the event from bubbling up further, ensuring no other handlers are triggered.
                    }

                    form.classList.add('was-validated');
                    // Adds the `was-validated` class to the form, triggering Bootstrap’s custom styles for validation feedback.
                }, false);
            });
        })();

        $('#addMemberModal, #editMemberModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Resets the form fields
            $(this).find('form').removeClass('was-validated'); // Removes validation styles
        });


        function editMember(memberId) {
            // Fetch member data via AJAX
            $.ajax({
                url: `/club-members/${memberId}`,
                type: 'GET',
                success: function(data) {
                    // Populate modal fields with member data
                    $('#memberId').val(data.id);
                    $('#edit_name').val(data.name); // Corrected to match ID
                    $('#edit_student_id').val(data.student_id); // Corrected to match ID
                    $('#edit_sunway_imail').val(data.sunway_imail); // Corrected to match ID
                    $('#edit_phone').val(data.phone); // Corrected to match ID
                    $('#edit_personal_email').val(data.personal_email); // Corrected to match ID
                    $('#edit_course_of_study').val(data.course_of_study); // Corrected to match ID

                    // Open the modal
                    $('#editMemberModal').modal('show');
                },
                error: function(err) {
                    console.error('Error fetching member data:', err);
                    Swal.fire(
                        'Error!',
                        'An error occurred while fetching the member data.',
                        'error'
                    );
                }
            });
        }

        document.getElementById('editMemberForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            if (this.checkValidity()) {
                saveMember(); // Only call saveMember if form is valid
            }

            this.classList.add('was-validated'); // Show validation feedback
        });

        function saveMember() {
            const memberId = $('#memberId').val();

            // Send the updated data via AJAX
            $.ajax({
                url: `/club-members/${memberId}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#name').val(),
                    student_id: $('#student_id').val(),
                    sunway_imail: $('#sunway_imail').val(),
                    phone: $('#phone').val(),
                    personal_email: $('#personal_email').val(),
                    course_of_study: $('#course_of_study').val(),
                },
                success: function(response) {
                    // Close the modal
                    $('#editMemberModal').modal('hide');

                    // Reload the DataTable to reflect changes
                    $('#memberTable').DataTable().ajax.reload();

                    Swal.fire({
                        title: 'Updated!',
                        text: 'The member details have been updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    console.error('Error updating member data:', err);
                    Swal.fire(
                        'Error!',
                        'An error occurred while updating the member details.',
                        'error'
                    );
                }
            });
        }

        document.getElementById('addMemberForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            if (this.checkValidity()) {
                addMember(); // Only call addMember if the form is valid
            }

            this.classList.add('was-validated'); // Show validation feedback
        });


        function addMember() {
            $.ajax({
                url: "{{ route('clubMembers.add') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#add_name').val(),
                    student_id: $('#add_student_id').val(),
                    sunway_imail: $('#add_sunway_imail').val(),
                    phone: $('#add_phone').val(),
                    personal_email: $('#add_personal_email').val(),
                    course_of_study: $('#add_course_of_study').val(),
                },
                success: function(response) {
                    // Close the modal
                    $('#addMemberModal').modal('hide');

                    // Clear form fields
                    $('#addMemberForm')[0].reset();

                    // Reload the DataTable to reflect the new member
                    $('#memberTable').DataTable().ajax.reload();

                    // Show success alert
                    Swal.fire({
                        title: 'Added!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(err) {
                    console.error('Error adding member:', err);
                    Swal.fire(
                        'Error!',
                        'An error occurred while adding the member.',
                        'error'
                    );
                }
            });
        }
    </script>
@endsection
