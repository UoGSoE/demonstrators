<div class="modal contract-modal">
    <div class="modal-background"></div>
    <div class="modal-card modal-card-form">
        <header class="modal-card-head"><p class="modal-card-title modal-card-title-contract">Contract Start and End Dates</p></header>
        <section class="modal-card-body modal-card-body-form">
            <p>Please submit the start and end dates for this students contract. Leave blank if not required.</p>
            <hr>
            <form id="contract-dates-form">
                <input name="student_id" type="hidden">
                <div class="field is-horizontal">
                    <div class="field-label is-normal"><label class="label">Contract Start Date</label></div>
                    <div class="field-body">
                        <div class="field">
                            <p class="control is-expanded has-icons-left">
                                <input class="input is-small" type="date" name="contract_start" placeholder="Contract Start Date">
                                <span class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"><label class="label">Contract End Date</label></div>
                    <div class="field-body">
                        <div class="field">
                            <p class="control is-expanded has-icons-left">
                                <input class="input is-small" type="date" name="contract_end" placeholder="Contract End Date">
                                <span class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-gla contract-dates-submit">Save</button>
            <button class="button contract-dates-dismiss">Dismiss</button>
        </footer>
    </div>
</div>