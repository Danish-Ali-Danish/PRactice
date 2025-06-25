@extends('layout.app')

@section('content')
<div class="container dashboard-card">
    <h2>Orders List</h2>
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal" id="addOrderBtn">
            <i class="fas fa-plus-circle"></i> Add New Order
        </button>
    </div>

    <div id="alertContainer"></div>

    <div class="table-responsive d-flex justify-content-center gap-2">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="orderTableBody"></tbody>
        </table>
    </div>
</div>

{{-- Order Modals --}}
@include('orders.edit')
@include('orders.delete')
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const orderTableBody = $('#orderTableBody');
    const orderModal = new bootstrap.Modal($('#orderModal')[0]);
    const deleteConfirmModal = new bootstrap.Modal($('#deleteConfirmModal')[0]);
    const orderForm = $('#orderForm');
    const orderIdInput = $('#orderId');
    const customerSelect = $('#customerSelect');
    const totalAmountInput = $('#totalAmount');
    const statusInput = $('#orderStatus');
    const alertContainer = $('#alertContainer');
    const deleteOrderName = $('#deleteOrderName');
    const addOrderBtn = $('#addOrderBtn');
    const saveOrderBtn = $('#saveOrderBtn');
    const confirmDeleteBtn = $('#confirmDeleteBtn');

    let currentOrderIdToDelete = null;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
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
        orderIdInput.val('');
        orderForm[0].reset();
        $('#orderModalLabel').text('Add New Order');
    }

    function renderOrders(orders) {
        orderTableBody.empty();
        if (orders.length === 0) {
            orderTableBody.append('<tr><td colspan="5" class="text-center">No orders found.</td></tr>');
            return;
        }

        $.each(orders, (index, order) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${order.customer.name}</td>
                    <td>${order.total_amount}</td>
                    <td>${order.status}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info btn-action edit-btn" data-id="${order.id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger btn-action delete-btn" data-id="${order.id}" data-name="${order.customer.name}">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            orderTableBody.append(row);
        });
    }

    function fetchOrders() {
        $.ajax({
            url: '{{ route("orders.list") }}',
            method: 'GET',
            success: function(data) {
                renderOrders(data);
            },
            error: function(xhr) {
                showAlert('Error fetching orders: ' + xhr.statusText, 'danger');
            }
        });
    }

    function fetchCustomers() {
        $.ajax({
            url: '{{ route("customers.list") }}',
            method: 'GET',
            success: function(customers) {
                customerSelect.empty();
                customerSelect.append('<option value="">Select Customer</option>');
                $.each(customers, function(index, customer) {
                    customerSelect.append(`<option value="${customer.id}">${customer.name}</option>`);
                });
            },
            error: function(xhr) {
                showAlert('Error fetching customers: ' + xhr.statusText, 'danger');
            }
        });
    }

    saveOrderBtn.on('click', function() {
        const id = orderIdInput.val();
        const customerId = customerSelect.val();
        const totalAmount = totalAmountInput.val().trim();
        const status = statusInput.val();

        if (!customerId || !totalAmount || !status) {
            showAlert('All fields are required.', 'warning');
            return;
        }

        const formData = {
            customer_id: customerId,
            total_amount: totalAmount,
            status: status
        };

        let url = '{{ route("orders.store") }}';
        let method = 'POST';

        if (id) {
            url = `/orders/${id}`;
            formData._method = 'PUT';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                showAlert('Order ' + (id ? 'updated' : 'added') + ' successfully!');
                orderModal.hide();
                clearForm();
                fetchOrders();
            },
            error: function(xhr) {
                showAlert('Error saving order: ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/orders/${id}`,
            method: 'GET',
            success: function(order) {
                orderIdInput.val(order.id);
                customerSelect.val(order.customer_id);
                totalAmountInput.val(order.total_amount);
                statusInput.val(order.status);
                $('#orderModalLabel').text('Edit Order');
                orderModal.show();
            },
            error: function(xhr) {
                showAlert('Error fetching order: ' + xhr.statusText, 'danger');
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        currentOrderIdToDelete = $(this).data('id');
        const orderName = $(this).data('name');
        deleteOrderName.text(orderName);
        deleteConfirmModal.show();
    });

    confirmDeleteBtn.on('click', function() {
        const idToDelete = currentOrderIdToDelete;
        if (idToDelete) {
            $.ajax({
                url: `/orders/${idToDelete}`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlert('Order deleted successfully!');
                    deleteConfirmModal.hide();
                    fetchOrders();
                    currentOrderIdToDelete = null;
                },
                error: function(xhr) {
                    showAlert('Error deleting order: ' + xhr.statusText, 'danger');
                }
            });
        } else {
            showAlert('No order selected for deletion.', 'warning');
            deleteConfirmModal.hide();
        }
    });

    addOrderBtn.on('click', function() {
        clearForm();
    });

    fetchOrders();
    fetchCustomers();
});
</script>
@endsection
