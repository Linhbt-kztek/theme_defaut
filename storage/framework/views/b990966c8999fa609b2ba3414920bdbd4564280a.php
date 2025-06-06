<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.list-view'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Tickets
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Tickets List
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Total Tickets</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="547">0</span>k
                            </h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                    <i class="ri-arrow-up-line align-middle"></i> 17.32 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                    <i class="ri-ticket-2-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div> <!-- end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Pending Tickets</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="124">0</span>k</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                    <i class="ri-arrow-down-line align-middle"></i> 0.96 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                    <i class="mdi mdi-timer-sand"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Closed Tickets</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="107">0</span>K</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                    <i class="ri-arrow-down-line align-middle"></i> 3.87 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                    <i class="ri-shopping-bag-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Deleted Tickets</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="15.95">0</span>%</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                    <i class="ri-arrow-up-line align-middle"></i> 1.09 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="ticketsList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Tickets</h5>
                        <div class="flex-shrink-0">
                            <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#showModal"><i
                                    class="ri-add-line align-bottom me-1"></i> Create Tickets</button>
                            <button class="btn btn-soft-secondary" onClick="deleteMultiple()"><i
                                    class="ri-delete-bin-2-line"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form>
                        <div class="row g-3">
                            <div class="col-xxl-5 col-sm-12">
                                <div class="search-box">
                                    <input type="text" class="form-control search bg-light border-light"
                                        placeholder="Search for ticket details or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-xxl-3 col-sm-4">
                                <input type="text" class="form-control bg-light border-light" data-provider="flatpickr"
                                    data-date-format="d M, Y" data-range-date="true" id="demo-datepicker"
                                    placeholder="Select date range">
                            </div>
                            <!--end col-->

                            <div class="col-xxl-3 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false
                                        name="choices-single-default" id="idStatus">
                                        <option value="">Status</option>
                                        <option value="all" selected>All</option>
                                        <option value="Open">Open</option>
                                        <option value="Inprogress">Inprogress</option>
                                        <option value="Closed">Closed</option>
                                        <option value="New">New</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-4">
                                <button type="button" class="btn btn-primary w-100" onclick="SearchData();"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <div class="table-responsive table-card mb-4">
                        <table class="table align-middle table-nowrap mb-0" id="ticketTable">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="id">ID</th>
                                    <th class="sort" data-sort="tasks_name">Title</th>
                                    <th class="sort" data-sort="client_name">Client</th>
                                    <th class="sort" data-sort="assignedto">Assigned To</th>
                                    <th class="sort" data-sort="create_date">Create Date</th>
                                    <th class="sort" data-sort="due_date">Due Date</th>
                                    <th class="sort" data-sort="status">Status</th>
                                    <th class="sort" data-sort="priority">Priority</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="ticket-list-data">
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="id"><a href="javascript:void(0);" onclick="ViewTickets(this)"
                                            data-id="001" class="fw-medium link-primary">#VLZ001</a></td>
                                    <td class="tasks_name">Error message when placing an orders?</td>
                                    <td class="client_name">Tonya Noble</td>
                                    <td class="assignedto">James Morris</td>
                                    <td class="create_date">08 Dec, 2021</td>
                                    <td class="due_date">25 Jan, 2022</td>
                                    <td class="status"><span
                                            class="badge badge-soft-warning text-uppercase">Inprogress</span></td>
                                    <td class="priority"><span class="badge bg-danger text-uppercase">High</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><button class="dropdown-item"
                                                        onclick="location.href = 'apps-tickets-details.html';"><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View</button></li>
                                                <li><a class="dropdown-item edit-item-btn" href="#showModal"
                                                        data-bs-toggle="modal"><i
                                                            class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn" data-bs-toggle="modal"
                                                        href="#deleteOrder">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#25a0e2,secondary:#00bd9d" style="width:75px;height:75px">
                                </lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 150+ Tickets We did not find any
                                    Tickets for you search.</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                Previous
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Next
                            </a>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#25a0e2,secondary:#00bd9d" style="width:90px;height:90px">
                                    </lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4>You are about to delete a order ?</h4>
                                        <p class="text-muted fs-14 mb-4">Deleting your order will remove all of
                                            your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button class="btn btn-link link-primary fw-medium text-decoration-none"
                                                data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-primary" id="delete-record">Yes, Delete It</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="modal fade zoomIn" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-primary">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div id="modal-id">
                                <label for="orderId" class="form-label">ID</label>
                                <input type="text" id="orderId" class="form-control" placeholder="ID" value="#VLZ462"
                                    readonly />
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <label for="tasksTitle-field" class="form-label">Title</label>
                                    <input type="text" id="tasksTitle-field" class="form-control" placeholder="Title"
                                        required />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label for="client_nameName-field" class="form-label">Client</label>
                                    <input type="text" id="client_nameName-field" class="form-control"
                                        placeholder="Client Name" required />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label for="assignedtoName-field" class="form-label">Assigned To</label>
                                    <input type="text" id="assignedtoName-field" class="form-control"
                                        placeholder="Assigned to" required />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="date-field" class="form-label">Create Date</label>
                                <input type="text" id="date-field" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y" placeholder="Create Date" required />
                            </div>
                            <div class="col-lg-6">
                                <label for="duedate-field" class="form-label">Due Date</label>
                                <input type="text" id="duedate-field" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y" placeholder="Due Date" required />
                            </div>
                            <div class="col-lg-6">
                                <label for="ticket-status" class="form-label">Status</label>
                                <select class="form-control" data-plugin="choices" name="ticket-status"
                                    id="ticket-status">
                                    <option value="">Status</option>
                                    <option value="New">New</option>
                                    <option value="Inprogress">Inprogress</option>
                                    <option value="Closed">Closed</option>
                                    <option value="Open">Open</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="priority-field" class="form-label">Priority</label>
                                <select class="form-control" data-plugin="choices" name="priority-field"
                                    id="priority-field">
                                    <option value="">Priority</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="add-btn">Add Ticket</button>
                            <button type="button" class="btn btn-primary" id="edit-btn">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/list.js/list.js.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/ticketlist.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\bui_linh_project\project_in_github\minimal-theme\resources\views/apps-tickets-list.blade.php ENDPATH**/ ?>