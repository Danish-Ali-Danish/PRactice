@extends('layout.app')

@section('content')
<div class="container dashboard-card">
    <h2>Customers List</h2>
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal" id="addCustomerBtn">
            <i class="fas fa-plus-circle"></i> Add New Customer
        </button>
    </div>

    <div id="alertContainer"></div>

    <div class="table-responsive d-flex justify-content-center gap-2">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody"></tbody>
        </table>
    </div>
</div>

{{-- Customer Modals --}}
@include('customers.edit')
@include('customers.delete')
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const customerTableBody = $('#customerTableBody');
    const customerModal = new bootstrap.Modal($('#customerModal')[0]);
    const deleteConfirmModal = new bootstrap.Modal($('#deleteConfirmModal')[0]);
    const customerForm = $('#customerForm');
    const customerIdInput = $('#customerId');
    const customerNameInput = $('#customerName');
    const customerEmailInput = $('#customerEmail');
    const customerPhoneInput = $('#customerPhone');
    const alertContainer = $('#alertContainer');
    const deleteCustomerName = $('#deleteCustomerName');
    const addCustomerBtn = $('#addCustomerBtn');
    const saveCustomerBtn = $('#saveCustomerBtn');
    const confirmDeleteBtn = $('#confirmDeleteBtn');

    let currentCustomerIdToDelete = null;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showAlert(message, type = 'success') {
        const alertDiv = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.html('');
        alertContainer.append(alertDiv);
        setTimeout(() => {
            alertContainer.find('.alert').alert('close');
        }, 5000);
    }

    function clearForm() {
        customerIdInput.val('');
        customerForm[0].reset();
        $('#customerModalLabel').text('Add New Customer');
    }

    function renderCustomers(customers) {
        customerTableBody.empty();
        if (customers.length === 0) {
            customerTableBody.append('<tr><td colspan="5" class="text-center">No customers found.</td></tr>');
            return;
        }

        $.each(customers, (index, customer) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${customer.name}</td>
                    <td>${customer.email}</td>
                    <td>${customer.phone}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info btn-action edit-btn" data-id="${customer.id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger btn-action delete-btn" data-id="${customer.id}" data-name="${customer.name}">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            customerTableBody.append(row);
        });
    }

    function fetchCustomers() {
        $.ajax({
            url: '{{ route("customers.index") }}',
            method: 'GET',
            success: function(data) {
                renderCustomers(data);
            },
            error: function(xhr) {
                showAlert('Error fetching customers: ' + xhr.statusText, 'danger');
            }
        });
    }

    saveCustomerBtn.on('click', function() {
        const id = customerIdInput.val();
        const customerName = customerNameInput.val().trim();
        const customerEmail = customerEmailInput.val().trim();
        const customerPhone = customerPhoneInput.val().trim();

        if (!customerName || !customerEmail || !customerPhone) {
            showAlert('All fields are required.', 'warning');
            return;
        }

        const formData = {
            name: customerName,
            email: customerEmail,
            phone: customerPhone,
            _token: '{{ csrf_token() }}'
        };

        let url = '{{ route("customers.store") }}';
        let method = 'POST';

        if (id) {
            url = `/customers/${id}`;
            formData._method = 'PUT';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                showAlert('Customer ' + (id ? 'updated' : 'added') + ' successfully!');
                customerModal.hide();
                clearForm();
                fetchCustomers();
            },
            error: function(xhr) {
                let message = 'Error saving customer.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat()[0];
                }
                showAlert(message, 'danger');
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/customers/${id}`,
            method: 'GET',
            success: function(customer) {
                customerIdInput.val(customer.id);
                customerNameInput.val(customer.name);
                customerEmailInput.val(customer.email);
                customerPhoneInput.val(customer.phone);
                $('#customerModalLabel').text('Edit Customer');
                customerModal.show();
            },
            error: function(xhr) {
                showAlert('Error fetching customer: ' + xhr.statusText, 'danger');
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        currentCustomerIdToDelete = $(this).data('id');
        const customerName = $(this).data('name');
        deleteCustomerName.text(customerName);
        deleteConfirmModal.show();
    });

    confirmDeleteBtn.on('click', function() {
        const idToDelete = currentCustomerIdToDelete;
        if (idToDelete) {
            $.ajax({
                url: `/customers/${idToDelete}`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlert('Customer deleted successfully!');
                    deleteConfirmModal.hide();
                    fetchCustomers();
                    currentCustomerIdToDelete = null;
                },
                error: function(xhr) {
                    showAlert('Error deleting customer: ' + xhr.statusText, 'danger');
                }
            });
        } else {
            showAlert('No customer selected for deletion.', 'warning');
            deleteConfirmModal.hide();
        }
    });

    addCustomerBtn.on('click', function() {
        clearForm();
    });

    fetchCustomers();
});
</script>
@endsection
