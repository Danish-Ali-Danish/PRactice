<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    @csrf
                    <input type="hidden" id="customerId">

                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Enter customer name">
                    </div>

                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customerEmail" placeholder="Enter email">
                    </div>

                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="customerPhone" placeholder="Enter phone number">
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="saveCustomerBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
