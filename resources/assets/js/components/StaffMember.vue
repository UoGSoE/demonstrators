<template>
    <div>
        <div class="modal ${staffmember.id}-modal" v-bind:class="{ 'is-active': isActive }">
            <div class="modal-background"></div>
            <div class="modal-card modal-card-form">
                <header class="modal-card-head">
                    <p class="modal-card-title">{{staffmember.fullName}} - {{ courseInfo }}</p>
                </header>
                <section class="modal-card-body modal-card-body-form">
                    This user has requests/applications for this course. Would you like to reassign these to another staff member not on this course, or delete the requests (email will be sent to students if any have applied)?
                    <multiselect
                        v-model="reassignValue"
                        :options="staffoptions"
                        track-by="id"
                        :custom-label="reassignLabel"
                        @select="onReassignSelect"
                    >
                        <pre>{{ value }}</pre>
                    </multiselect>
                </section>
                <footer class="modal-card-foot">
                    <button :disabled="!reassignValue" v-on:click="reassignRequests" class="button is-success">Reassign</button>
                    <button v-on:click="deleteRequests" class="button is-danger">Delete Requests</button>
                    <button v-on:click="cancel" class="button">Cancel</button>
                    <span>{{ modalError }}</span>
                </footer>
            </div>
        </div>
        <div class="columns is-centered">
            <div class="column is-three-quarters">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                        {{ staffmember.fullName }}
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content staff-media-content">
                                <span class="icon is-small"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                                <strong> GUID: </strong>{{ staffmember.username }}<br>
                                <span class="icon is-small"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                                <strong> Email: </strong><a href="mailto:${ staffmember.email }">{{ staffmember.email }}</a><br>
                                <span class="icon is-small"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
                                <strong> Requests: </strong>{{ staffmember.requests.length }}<br>
                                <span class="icon is-small"><i class="fa fa-comments" aria-hidden="true"></i></span>
                                <strong> Applications: </strong>{{ staffmember.applications.length }}<br>
                            </div>
                            <div class="content staff-content">
                                Courses
                                <multiselect
                                    :multiple="true"
                                    v-model="value"
                                    :options="options"
                                    track-by="id"
                                    :custom-label="customLabel"
                                    @select="onSelect"
                                    @remove="onRemove"
                                >
                                </multiselect>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['staffmember', 'allcourses'],

    data () {
        return {
            id: this.staffmember.id,
            value: this.staffmember.currentCourses,
            options: this.allcourses,
            staffoptions: [],
            reassignValue: '',
            isActive: false,
            courseInfo: '',
            reassignId: '',
            courseId: '',
            modalError: '',
        }
    },
    methods: {
  	    customLabel (option) {
            return `${option.code} - ${option.title}`
        },
        onSelect (option) {
            this.addToCourse(option.id);
        },
        onRemove (option) {
            axios.get('/admin/staff/'+this.id+'/course/'+option.id)
                .then((response) => { 
                    if (response.data.requests) {
                        this.showReassignBox(option);
                    } else {
                        this.removeFromCourse(option.id);
                    }
                });
        },

        addToCourse (courseId) {
            axios.post('/admin/staff', {staff_id:this.id, course_id:courseId})
              .catch((error) => {
                console.log(error);
              })
        },
        removeFromCourse (courseId) {
            axios.post('/admin/staff/remove-course', {staff_id:this.id, course_id:courseId})
                .catch((error) => {
                    console.log(error);
                })
        },

        showReassignBox (option) {
            this.courseId = option.id;
            this.courseInfo = option.code + ' ' + option.title;
            axios.get('/api/staff')
                .then((response) => {
                    console.log(response.data.data);
                    this.staffoptions = response.data.data;
                    this.isActive = true;   
                });
        },

        reassignLabel (option) {
            return `${option.name}`
        },

        onReassignSelect (option) {
            this.reassignId = option.id;
        },

        reassignRequests: function (event) {
            axios.post('/admin/staff/reassign-requests', {staff_id:this.id, course_id:this.courseId, reassign_id: this.reassignId})
                .then((response) => {
                    this.isActive = false;
                    location.reload();
                })
                .catch((error) => {
                    this.modalError = error.response.data.status;
                });
        },

        deleteRequests: function (event) {
            axios.post('/admin/staff/remove-requests', {staff_id:this.id, course_id:this.courseId})
                .then((response) => {
                    this.isActive = false;
                    location.reload();
                })
                .catch((error) => {
                    this.modalError = error.response.data.status;
                });
        },

        cancel: function (event) {
            location.reload();
        }
    },
}
</script>