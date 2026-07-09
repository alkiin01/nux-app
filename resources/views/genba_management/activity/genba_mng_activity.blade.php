<div class="row">
    <div class="col-lg-6 mb-5">
        <div class="row mb-5">
            <label class="col-lg-3 col-form-label">Date</label>
            <div class="col-lg-9">
                <input type="date" class="form-control" name="date" id="date"
                    value="{{ $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d') }}" disabled />
            </div>
        </div>
        <div class="row mb-5">
            <label class="col-lg-3 col-form-label">Process</label>
            <div class="col-lg-9">
                <select class="form-select" name="process" data-kt-select2="true" data-placeholder="Select option"
                    data-allow-clear="false" data-hide-search="true" id="process" disabled>
                    <option value="" {{ $process == '' ? 'selected' : '' }}>
                    </option>
                    <option value="STP" {{ $process == 'STP' ? 'selected' : '' }}>STP
                    </option>
                    <option value="ASSY" {{ $process == 'ASSY' ? 'selected' : '' }}>ASSY
                    </option>
                    <option value="Receiving & Delivery" {{ $process == 'Receiving & Delivery' ? 'selected' : '' }}>
                        Receiving &
                        Delivery</option>
                    <option value="Storage" {{ $process == 'Storage' ? 'selected' : '' }}>
                        Storage</option>
                </select>
            </div>
        </div>
        <div class="row mb-5">
            <label class="col-lg-3 col-form-label">Line
                Checked</label>
            <div class="col-lg-9">
                <select class="form-select" name="area_checked" data-kt-select2="true" data-placeholder="Select option"
                    data-allow-clear="false" data-hide-search="true" id="area_checked" disabled>
                    <option value="{{ $area_checked }}">
                        {{ $area_checked }}</option>
                </select>
            </div>
        </div>

    </div>
    <div class="col-lg-6 mb-5">
        <div class="row mb-5">
            <label class="col-lg-3 col-form-label ">Station / Mech.
                Num</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="station" id="station" value="{{ $station }}"
                    disabled />
            </div>
        </div>
        <div class="row mb-5">
            <label class="col-lg-3 col-form-label disabled">Auditor</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="auditor" id="auditor"
                    value="{{ $auditor == null ? auth()->user()->full_name : $auditor }}"disabled />
            </div>
        </div>

        <div class="row mb-5">
            <label class="col-lg-3 col-form-label">Category
            </label>
            <div class="col-lg-9">
                <select class="form-select" name="genba_category" data-kt-select2="true"
                    data-placeholder="Select option" data-allow-clear="false" data-hide-search="true"
                    id="genba_category" disabled>
                    <option value="{{ $category_id }}">
                        {{ $category }}</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="separator separator-dashed my-10"></div>
