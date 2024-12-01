@extends('webplatform.shared.layout')

@section('title', 'Member')

@section('content')
    <style>
        body {
            background-color: #f4f4f4;
        }

        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
            width: 400px;
        }

        table.dataTable.display th.dt-type-numeric,
        table.dataTable.display td.dt-type-numeric {
            text-align: left !important;
        }

        .table-striped>tbody>tr:nth-child(odd)>td,
        .table-striped>tbody>tr:nth-child(odd)>th {
            background-color: white;
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

        .role-dropdown {
            font-size: 1rem;
        }
    </style>

    <h1 class="fw-bold">Members</h1>
    <hr>

    <div class="container-fluid">
        <div class="row">
            <div class="col text-end">
                <!-- Member Approval Button with Dropdown -->
                <div class="dropdown position-relative">
                    <button class="btn btn-primary mb-3 dropdown-toggle" type="button" id="memberApprovalButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Member Approval
                        <!-- The Badge to Show Pending Members Count -->
                        <span id="pendingApprovalBadge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="left: 80%; display: none;">
                            0
                        </span>
                    </button>
                    <ul id="pendingMembersList" class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="memberApprovalButton">
                        <!-- Pending Members will be listed here -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table id="memberTable" class="display compact table table-striped" style="width:100%">
                    <thead>
                        <th class="text-white"></th>
                        <th class="text-white">Name</th>
                        <th class="text-white">Student ID</th>
                        <th class="text-white">Sunway Imail</th>
                        <th class="text-white">Role</th>
                        <th class="text-white">Course of Study</th>
                        <th class="text-white"></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                // Fetch pending members and populate the dropdown
                $.ajax({
                    url: "{{ route('iclub.pendingMembers.data') }}",
                    type: "GET",
                    success: function(response) {
                        let membersList = response.members;
                        let pendingMembersList = $('#pendingMembersList');
                        let pendingApprovalBadge = $('#pendingApprovalBadge');

                        // Clear any existing items
                        pendingMembersList.empty();

                        // Check if there are any pending members
                        if (membersList.length > 0) {
                            // Update the badge with the count of pending members
                            pendingApprovalBadge.text(membersList.length);
                            pendingApprovalBadge
                                .show(); // Show the badge if there are pending members

                            // Loop through the list of pending members and create list items
                            membersList.forEach(function(member) {
                                let listItem = `
                    <li id="member-${member.id}" class="dropdown-item d-flex justify-content-between align-items-center">
                        <span>${member.name} (Student ID: ${member.student_id})</span>
                        <div>
                            <!-- Approve Button with Tick Icon -->
                            <button class="btn btn-outline-success btn me-2 border-0" title="Approve" onclick="approveMember(${member.id}, event)">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <!-- Reject Button with Cross Icon -->
                            <button class="btn btn-outline-danger btn border-0" title="Reject" onclick="rejectMember(${member.id}, event)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </li>
                    `;
                                pendingMembersList.append(listItem);
                            });
                        } else {
                            // If no pending members, display a message
                            let noMembersItem = `
                    <li class="dropdown-item text-center">
                        No members waiting for approval.
                    </li>
                `;
                            pendingMembersList.append(noMembersItem);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching pending members:', error);
                    }
                });
            });


            // Initialize DataTable
            let table = $('#memberTable').DataTable({
                "processing": true,
                "responsive": true,
                "ajax": "{{ route('iclub.clubMembers.data') }}",
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
                        "data": "email"
                    },
                    {
                        "data": "role",
                        "render": function(data, type, row) {
                            return `
                                <select class="form-select role-dropdown" onchange="changeRole(${row.id}, this.value)">
                                    <option value="President" ${data === 'President' ? 'selected' : ''}>President</option>
                                    <option value="Vice President" ${data === 'Vice President' ? 'selected' : ''}>Vice President</option>
                                    <option value="Secretary" ${data === 'Secretary' ? 'selected' : ''}>Secretary</option>
                                    <option value="Treasurer" ${data === 'Treasurer' ? 'selected' : ''}>Treasurer</option>
                                    <option value="Public Relations" ${data === 'Public Relations' ? 'selected' : ''}>Public Relations</option>
                                    <option value="Event" ${data === 'Event' ? 'selected' : ''}>Event</option>
                                    <option value="Media" ${data === 'Media' ? 'selected' : ''}>Media</option>
                                    <option value="Member" ${data === 'Member' ? 'selected' : ''}>Member</option>
                                </select>
                            `;
                        }
                    },
                    {
                        "data": "course_of_study"
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "searchable": false,
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return `
                                <i class="bi bi-trash-fill text-danger ms-2" style="cursor: pointer" onclick="deleteMember(${row.id})"></i>
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

            // Add event listener for opening and closing details
            $('#memberTable tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });

        // Format function for row details
        function format(d) {
            const formattedDate = d.created_at ? moment(d.created_at).format('DD/MM/YYYY') : '';
            return `
                <table class="table">
                    <tr>
                        <td>Personal Email:</td>
                        <td>${d.personal_email}</td>
                    </tr>
                    <tr>
                        <td>Phone No.:</td>
                        <td>${d.phone}</td>
                    </tr>
                    <tr>
                        <td>Date Joined:</td>
                        <td>${formattedDate}</td>
                    </tr>
                </table>
            `;
        }

        // Handle role change
        function changeRole(memberId, newRole) {
            $.ajax({
                url: `/iclub/clubMember/${memberId}/role`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    role: newRole
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Role updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#memberTable').DataTable().ajax.reload();
                },
                error: function(err) {
                    Swal.fire(
                        'Error!',
                        err.responseJSON.message || 'An error occurred while updating the role.',
                        'error'
                    );
                }
            });
        }

        // Function to approve a member
        function approveMember(memberId) {
            event.stopPropagation();
            $.ajax({
                url: `/iclub/pendingMember/${memberId}/approve`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    // Remove the approved member from the pending list in the dropdown
                    $(`#member-${memberId}`).remove();

                    // Update the badge count
                    let currentCount = parseInt($('#pendingApprovalBadge').text());
                    let newCount = currentCount - 1;

                    if (newCount > 0) {
                        $('#pendingApprovalBadge').text(newCount);
                    } else {
                        // Hide the badge if no pending members are left
                        $('#pendingApprovalBadge').hide();

                        // Show "No members waiting for approval" message if list is empty
                        let noMembersItem = `
                    <li class="dropdown-item text-center">
                        No members waiting for approval.
                    </li>
                `;
                        $('#pendingMembersList').append(noMembersItem);

                        if ($.fn.DataTable.isDataTable('#memberTable')) {
                            $('#memberTable').DataTable().ajax.reload(null,
                            false); // Reload table without resetting pagination
                        }
                    }
                },
                error: function(err) {
                    Swal.fire('Error!', 'An error occurred while approving the member.', 'error');
                }
            });
        }

        // Function to delete a member (reject a pending member)
        function rejectMember(memberId, event) {
            event.stopPropagation(); // Prevent dropdown from closing

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to reject this user?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/iclub/pendingMember/${memberId}/reject`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            // Remove the rejected member from the pending list in the dropdown
                            $(`#member-${memberId}`).remove();

                            // Update the badge count
                            let currentCount = parseInt($('#pendingApprovalBadge').text());
                            let newCount = currentCount - 1;

                            if (newCount > 0) {
                                $('#pendingApprovalBadge').text(newCount);
                            } else {
                                // Hide the badge if no pending members are left
                                $('#pendingApprovalBadge').hide();

                                // Show "No members waiting for approval" message if list is empty
                                let noMembersItem = `
                            <li class="dropdown-item text-center">
                                No members waiting for approval.
                            </li>
                        `;
                                $('#pendingMembersList').append(noMembersItem);
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error!', 'An error occurred while rejecting the member.',
                                'error');
                        }
                    });
                }
            });
        }

        // Delete member
        function deleteMember(memberId) {
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
                        url: `/iclub/clubMember/${memberId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(result) {
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
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the member.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endsection
