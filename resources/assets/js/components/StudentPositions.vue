<template>
    <div>
        <div v-if="currentApplications.length > 0" class="card">
            <header class="card-header">
                <p class="card-header-title">Accepted Applications</p>
            </header>
            <div class="card-content">
                You have been accepted for a position. Please confirm if you are still able to do this position, or decline if not.
                <br><br>
                <table class="table is-narrow is-striped">
                    <tbody>
                        <tr v-for="(application, index) in currentApplications" v-if="!application.student_responded">
                            <td>{{ application.request.course.code }} {{ application.request.course.title}}</td>
                            <td>{{ application.request.type }}</td>
                            <td>{{ application.request.hours_needed }} hours</td>
                            <td style="width:50%">
                            <span class="is-pulled-right">
                                <a @click.prevent="accept(application)" class="button is-gla-success is-small">
                                    <span class="icon is-small"><i class="fa fa-check"></i></span>
                                    <span>Accept</span>
                                </a>
                                <a @click.prevent="decline(application)" class="button is-gla-danger is-small">
                                    <span class="icon is-small"><i class="fa fa-times"></i></span>
                                    <span>Decline</span>
                                </a>
                            </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['applications'],

    data () {
        return {
            currentApplications: this.applications,
        }
    },

    methods: {
        accept(application) {
            var self = this;
            var url = '/application/' + application.id + '/student-confirms';
            axios.post(url).then(function (data) {
                application.student_responded = true;
                self.removeApplication(application);
            });
        },

        decline(application) {
            var self = this;
            var url = '/application/' + application.id + '/student-declines';
            axios.post(url).then(function (data) {
                application.student_responded = true;
                self.removeApplication(application);
            });
        },

        removeApplication(application) {
            this.currentApplications = this.currentApplications.filter(function(el) {
                return el.id !== application.id;
            });
        }
    }
}
</script>