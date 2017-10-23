<div class="modal @if (!auth()->user()->hide_blurb) is-active @endif">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">School of Engineering Teaching Assistants</p>
        </header>
        <section class="modal-card-body">
            <h3 class="subtitle">
            Welcome to the School of Engineering Teaching Assistant Pages. Here you can browse the range of demonstrating, tutoring and marking opportunities available in the School across first and second semesters, and our Summer Schools.
            </h3>
            <h3 class="subtitle">
            Our Teaching Assistants make a valuable contribution to the student learning experience within the School, enhancing the quality of teaching and helping to better prepare our undergraduates for the future.  Thank you for taking part, and we hope that the experience of teaching will be of great benefit to you too.
            </h3>
            <h3 class="subtitle">
            Please view the courses available below for further information on course activities, required experience, knowledge and skills and approximate hours available.
            </h3>
            <h3 class="subtitle">
            Should you wish to apply, please select the course you are interested in and provide brief details of your qualifications for the role and availability via the 'Add Extra Information' button at the top of the page.
            </h3>
            <h3 class="subtitle">
            You may apply for as many courses as you wish, however, please withdraw from any applications you have been selected for that do not fit with your availability.
            </h3>
            <h3 class="subtitle">
            Given the time contribution that you will be making as a Teaching Assistant, please remember it is essential that your supervisor (for PGR students) or Adviser of Studies (for UG students) is aware that you are taking on this role and has agreed to the number of hours you wish to work. Please also note if you have a Tier 4 visa there is also a maximum number of hours you can work.
            </h3>
            <h3 class="subtitle">
            The academic staff responsible for each course will select demonstrators based on their experience, knowledge and skills.  If selected, you will be notified by email and provided with guidance on completing the recruitment process.
            </h3>
            <h3 class="subtitle">
            Our Teaching Assistants are required to participate in an Introduction to Learning and Teaching in Higher Education class provided by the University – you will find details here: <a href="http://www.gla.ac.uk/myglasgow/leads/staff/gtas/">http://www.gla.ac.uk/myglasgow/leads/staff/gtas/</a>
            </h3>
            <hr>
            <h3 class="subtitle">
            <b>PLEASE NOTE</b>
            </h3>
            <h3 class="subtitle">
            IF APPLYING FOR TEACHING ASSISTANT POSITIONS IT IS ESSENTIAL THAT YOU CHECK FOR EMAIL UPDATES REGULARLY
            </h3>
            <h3 class="subtitle">
            IF ACCEPTED TO WORK ON A GIVEN COURSE, YOU MUST COMPLETE THE STEPS NECESSARY TO ARRANGE YOUR CONTRACT PRIOR TO PERFORMING ANY WORK FOR THE SCHOOL – FULL DETAILS WILL BE PROVIDED
            </h3>
            <h3 class="subtitle">
            ONE CONTRACT WILL BE ISSUED TO YOU REGARDLESS OF HOW MANY COURSES YOU ARE SELECTED FOR – FOR THIS REASON YOUR CONTRACTED HOURS WILL BE SET TO A STANDARD MINIMUM
            </h3>
            <h3 class="subtitle">
            TEACHING ASSISTANTS IN THE SCHOOL OF ENGINEERING CAN ONLY WORK WITHIN THE DESIGNATED DATES OF THEIR CONTRACTS, AND MUST NOTIFY THE SCHOOL OF THEIR RESIGNATION SHOULD THEY BE UNABLE TO COMPLETE THEIR APPOINTMENT
            </h3>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-gla toggle-blurb">I understand</button>
            @if (!Auth()->user()->hide_blurb)
                <button data-user="{{ auth()->user()->id }}" class="button toggle-blurb disable-blurb">Don't show me this again</button>
            @endif
        </footer>
    </div>
</div>