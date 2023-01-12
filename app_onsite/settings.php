<div class="modal fade" id="settingModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="process.php" method="post">
                <div class="modal-header ngbAutofocus">
                    <h5 class="modal-title" id="exampleModalToggleLabel">Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span id="mess"></span>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <div class="col-12">
                            <label for="timeIn" class="form-label">Place</label>
                            <select name="" id="" class="form-control">
                                <option value="" disabled selected></option>
                                <option value="oneGate1">Main building - Gate 1</option>
                                <option value="oneGate2">Main building - Gate 2</option>
                                <option value="twoGate1">New building - Gate 1</option>
                                <option value="twoGate1">New building - Gate 2</option>
                            </select>
                        </div>
                    </div>
                    <!-- <hr> -->
                    <div class="mb-3 row">
                        <div class="col-6 mb-3">
                            <label for="timeIn" class="form-label">Time In</label>
                            <input type="time" name="timeIn" value="<?php echo $timeIn; ?>" class="form-control" id="timeIn" placeholder="Time In" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="timeOut" class="form-label">Time Out</label>
                            <input type="time" name="timeOut" value="<?php echo $timeOut; ?>" class="form-control" id="timeOut" placeholder="Time Out" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="updateTime">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>