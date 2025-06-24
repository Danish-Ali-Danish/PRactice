<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Add New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    @csrf
                    <input type="hidden" id="orderId">

                    <div class="mb-3">
                        <label for="customerSelect" class="form-label">Customer</label>
                        <select class="form-select" id="customerSelect">
                            <option value="">Select Customer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="totalAmount" class="form-label">Total Amount</label>
                        <input type="number" class="form-control" id="totalAmount" placeholder="Enter total amount">
                    </div>

                    <div class="mb-3">
                        <label for="orderStatus" class="form-label">Status</label>
                        <select class="form-select" id="orderStatus">
                            <option value="">Select status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="saveOrderBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
