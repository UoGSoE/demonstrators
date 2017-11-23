<div class="modal @if (!auth()->user()->hide_blurb) is-active @endif">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">School of Engineering Teaching Assistants</p>
        </header>
        <section class="modal-card-body">
            <h3 class="subtitle">
            Welcome to the School of Engineering Teaching Assistants homepage.
            </h3>
            <h3 class="subtitle">
            As an academic staff member involved in teaching delivery, it is your responsibility to ensure that your Teaching Assistant (TA) requirements are accurate, and applicants are considered in a timely manner. Remember: Teaching Assistant’s cannot undertake any demonstrating/teaching duties, without a fully signed contract being in place prior to delivery of teaching (see notes below).
            </h3>
            <h3 class="subtitle">
            The first task you need to complete is to update your requirements.  Please review these by visiting each course entry, and ensure that the correct course information, functions (i.e. Demonstrator, Tutor, Marker), number of persons required in each function, and the anticipated number of hours for each function per person are all entered accurately.  This information has been transferred from the previous session and may now be out of date. Failure to update may result in inaccurate or incomplete information being used to recruit.  You will find guidance on updating the information below.
            </h3>
            <h3 class="subtitle">
            Following this, you will be notified by email as and when applicants make themselves available for your course(s).  Applicants are selected by toggling from a list which is displayed within each course entry.  Potential TAs apply for specific functions within each course, rather than for the course generally, so please ensure that you select for all  functions required if applicable.
            </h3>
            <h3 class="subtitle">
            Should an applicant withdraw or fail to confirm their intention to work within the designated period, you will be notified and you will be required to select another applicant.
            </h3>
            <h3 class="subtitle">
            The creation of accurate requirements and the selection of TAs is the first essential step in creating contracts for the TAs involved.  Any delay in completing this task will result in a commensurate delay in the TA(s) having a completed contract and being available to work.
            </h3>
            <hr>
            <h3 class="subtitle">
            <b>PLEASE NOTE</b>
            </h3>
            <h3 class="subtitle">
            ABSOLUTELY NO WORK WHATSOEVER CAN BE PERFORMED BY PERSONS WHO DO NOT HOLD A CURRENT, SIGNED CONTRACT – CANDIDATES WILL BE INFORMED OF THEIR AUTHORISATION TO WORK ON EACH SPECIFIC FUNCTION WITHIN EACH COURSE AND CANNOT WORK PRIOR TO THIS AUTHORISATION BEING RECEIVED
            </h3>
            <h3 class="subtitle">
            YOU CANNOT WAIVE THESE REQUIREMENTS – PLEASE DO NOT INSTRUCT ANY TEACHING ASSISTANT TO PERFORM WORK PRIOR TO THEIR AUTHORISATION
            </h3>
            <h3 class="subtitle">
            ANY ACTIVITY PERFORMED BY TEACHING ASSISTANTS PRIOR TO THEIR AUTHORISATION WILL NOT BE REMUNERATED
            </h3>
            <hr>
            <h3 class="subtitle">
            <strong>Guidance on updating your course information</strong><br>
            Having logged in, you will see all of the courses our records show you to be involved in (by involved, we mean responsible for your own teaching component which includes labs/tutorials – this is not necessarily going to be limited to the course coordinator).  If you do not see a course listed which you know yourself to be involved in, please contact the Teaching Office (eng-teachingoffice@glasgow.ac.uk).
            </h3>
            <h3 class="subtitle">
            All the information provided in these sections will be accessible to all students who have logged on to the system.
            </h3>
            <h3 class="subtitle">
            <u>Course information</u><br>
            The basics – course code, course name and semester for teaching (1, 2, 1&2 or Summer) – in the vast majority of cases these will be correct, please review and update as appropriate.
            </h3>
            <h3 class="subtitle">
            <u>Functions</u><br>
            Each function (demonstrator, tutor, marker) has an entry, which may or may not have information entered.  Please either update the existing information or enter new information indicating how many persons (if any) are required in each function, how many hours of teaching work will be required in each function per person, and how many hours of training (if applicable) each TA will require to undertake.
            </h3>
            <h3 class="subtitle">
            <u>Special Requirements</u><br>
            A free-form section where you may identify any preferences you have for the level, experience or knowledge type of the TAs required. You can also list any relevant knowledge and/or experience you have here.
            </h3>
        </section>
        <footer class="modal-card-foot">
            <button id="dismiss-blurb" class="button is-gla toggle-blurb">I understand</button>
            @if (!Auth()->user()->hide_blurb)
                <button data-user="{{ auth()->user()->id }}" class="button toggle-blurb disable-blurb">Don't show me this again</button>
            @endif
        </footer>
    </div>
</div>