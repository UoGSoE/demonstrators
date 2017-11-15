<div class="modal rtw-modal">
    <div class="modal-background"></div>
    <div class="modal-card modal-card-form">
        <header class="modal-card-head"><p class="modal-card-title modal-card-title-rtw">RTW Start and End Dates</p></header>
        <section class="modal-card-body modal-card-body-form">
            <p>Please submit the start and end dates for this students RTW Information. Leave blank if not required.</p>
            <hr>
            <form id="rtw-dates-form">
                <input name="student_id" type="hidden">
                <div class="field is-horizontal">
                    <div class="field-label is-normal"><label class="label">RTW Start Date</label></div>
                    <div class="field-body">
                        <div class="field">
                            <p class="control is-expanded has-icons-left">
                                <input class="input is-small" type="date" name="rtw_start" placeholder="RTW Start Date">
                                <span class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"><label class="label">RTW End Date</label></div>
                    <div class="field-body">
                        <div class="field">
                            <p class="control is-expanded has-icons-left">
                                <input class="input is-small" type="date" name="rtw_end" placeholder="RTW End Date">
                                <span class="icon is-small is-left"><i class="fa fa-calendar fa-calendar-vue"></i></span>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-gla rtw-dates-submit">Save</button>
            <button class="button rtw-dates-dismiss">Dismiss</button>
        </footer>
    </div>
</div>